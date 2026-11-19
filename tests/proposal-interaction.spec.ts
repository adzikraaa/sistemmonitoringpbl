import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Verifikasi Interaktivitas Halaman Proposal Mahasiswa', () => {
  test('Halaman proposal bebas dari SyntaxError & modal form berfungsi utuh', async ({ page }) => {
    const consoleErrors: string[] = [];
    const pageErrors: Error[] = [];

    page.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    page.on('pageerror', err => {
      pageErrors.push(err);
    });

    // 1. Login sebagai Mahasiswa SIAKAD
    const nim = process.env.SIAKAD_NIM || '244107060044';
    const pwd = process.env.SIAKAD_PASSWORD || 'Adzikra_280706';

    await page.goto('http://127.0.0.1:8000/login');
    await page.fill('input[name="identifier"]', nim);
    await page.fill('input[name="password"]', pwd);
    await page.click('button[type="submit"]');

    // Tunggu navigasi setelah login
    await page.waitForURL(/.*(mahasiswa\/pilih-semester|mahasiswa\/dashboard).*/);

    // 2. Kunjungi halaman proposal dengan query parameter semester=5
    await page.goto('http://127.0.0.1:8000/mahasiswa/proposal?semester=5');
    await page.waitForLoadState('networkidle');

    // 3. Pastikan tidak ada SyntaxError maupun ReferenceError pada Console
    const syntaxOrRefErrors = consoleErrors.filter(e => 
      e.includes('SyntaxError') || 
      e.includes('ReferenceError') || 
      e.includes('has already been declared') || 
      e.includes('openProposalModal is not defined')
    );
    expect(syntaxOrRefErrors).toHaveLength(0);
    expect(pageErrors).toHaveLength(0);

    // 4. Periksa tombol "+ Ajukan proposal" dan klik untuk membuka modal
    const btnAjukan = page.locator('#btn-ajukan-proposal');
    await expect(btnAjukan).toBeVisible();
    await btnAjukan.click();

    // 5. Pastikan modal form pengajuan terbuka
    const overlay = page.locator('#proposal-modal-overlay');
    await expect(overlay).toHaveClass(/sim-modal-overlay--visible/);
    const modalTitle = page.locator('#modal-title');
    await expect(modalTitle).toHaveText('Ajukan proposal baru');

    // 6. Uji tombol tutup modal
    const btnClose = page.locator('.sim-modal__close');
    await btnClose.click();
    await expect(overlay).not.toHaveClass(/sim-modal-overlay--visible/);

    // 7. Buka modal kembali
    await btnAjukan.click();
    await expect(overlay).toHaveClass(/sim-modal-overlay--visible/);

    // 8. Uji validasi input (klik Kirim proposal tanpa mengisi data)
    const btnSubmit = page.locator('#btn-submit-proposal');
    await btnSubmit.click();

    const errJudul = page.locator('#err-judul');
    const errDeskripsi = page.locator('#err-deskripsi');
    const errTujuan = page.locator('#err-tujuan');
    const errFile = page.locator('#err-file_proposal');

    await expect(errJudul).toHaveText('Judul proyek wajib diisi.');
    await expect(errDeskripsi).toHaveText('Deskripsi singkat wajib diisi.');
    await expect(errTujuan).toHaveText('Tujuan wajib diisi.');
    await expect(errFile).toHaveText('Berkas proposal (PDF) wajib diunggah.');

    // 9. Isi data proposal
    await page.fill('#field-judul', 'Sistem Monitoring Otomatis Playwright');
    await page.fill('#field-deskripsi', 'Deskripsi proposal untuk pengujian interaktivitas.');
    await page.fill('#field-tujuan', 'Tujuan proposal pengujian.');

    // Buat file PDF sementara untuk pengujian upload
    const dummyPdfPath = path.resolve('temp-test-proposal.pdf');
    fs.writeFileSync(dummyPdfPath, '%PDF-1.4\n%EOF');

    // 10. Uji pemilihan dokumen proposal via file input
    const fileInput = page.locator('#fileProposalInput');
    await fileInput.setInputFiles(dummyPdfPath);

    // Pastikan UI dropzone menampilkan nama file yang terpilih
    const dropzoneFilled = page.locator('#proposalDropzoneFilled');
    await expect(dropzoneFilled).toBeVisible();
    const fileNameEl = page.locator('#proposalFileName');
    await expect(fileNameEl).toContainText('temp-test-proposal.pdf');

    // 11. Kirim proposal
    await btnSubmit.click();

    // Pastikan diarahkan kembali dengan notifikasi sukses
    await page.waitForURL(/.*mahasiswa\/proposal.*/);
    const successBox = page.locator('div:has-text("Proposal berhasil diajukan")').first();
    await expect(successBox).toBeVisible();

    // Hapus file temporary
    if (fs.existsSync(dummyPdfPath)) {
      fs.unlinkSync(dummyPdfPath);
    }
  });
});
