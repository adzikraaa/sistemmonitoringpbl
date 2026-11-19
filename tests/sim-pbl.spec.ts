import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

const BASE_URL = process.env.APP_URL || 'http://127.0.0.1:8000';

const MAHASISWA_NIM = process.env.SIAKAD_NIM || '244107060044';
const MAHASISWA_PASSWORD = process.env.SIAKAD_PASSWORD || 'Adzikra_280706';
const DOSEN_NIDN = '0099000002';
const DOSEN_PASSWORD = 'password';
const KOORDINATOR_NIDN = '0099000001';
const KOORDINATOR_PASSWORD = 'password';

function resetRateLimit() {
  try {
    execSync('php artisan cache:clear');
  } catch {
    // ignore
  }
}

test.describe('E2E SIM-PBL - Skenario Fungsional & Navigasi Positif', () => {

  test.describe.serial('1. Antarmuka Login & Branding', () => {
    test('TC-01: Halaman login dapat diakses dan menampilkan branding SIM-PBL', async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      await expect(page.getByRole('heading', { name: /Masuk ke SIM-PBL/i })).toBeVisible();
      await expect(page.locator('.brand-name')).toHaveText('SIM-PBL');
      await expect(page.locator('.brand-sub')).toHaveText('SISTEM MONITORING PBL');
    });

    test('TC-02: Form login single identity menampilkan field Username, Kata Sandi, dan Tombol Masuk', async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      const identifierInput = page.locator('input[name="identifier"]');
      const passwordInput = page.locator('input[name="password"]');
      const submitBtn = page.locator('button[type="submit"]');

      await expect(identifierInput).toBeVisible();
      await expect(identifierInput).toHaveAttribute('placeholder', 'NIM / NIDN');
      await expect(passwordInput).toBeVisible();
      await expect(passwordInput).toHaveAttribute('placeholder', 'Kata sandi');
      await expect(submitBtn).toBeVisible();
      await expect(submitBtn).toContainText('Masuk');
    });

    test('TC-03: Toggle visibilitas kata sandi berfungsi (Lihat / Tutup)', async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      const passInput = page.locator('input[name="password"]');
      const showBtn = page.locator('.login-show-btn');

      await passInput.fill('Secret123');
      await expect(passInput).toHaveAttribute('type', 'password');
      await expect(showBtn).toHaveText('Lihat');

      await showBtn.click();
      await expect(passInput).toHaveAttribute('type', 'text');
      await expect(showBtn).toHaveText('Tutup');

      await showBtn.click();
      await expect(passInput).toHaveAttribute('type', 'password');
      await expect(showBtn).toHaveText('Lihat');
    });

    test('TC-04: Checkbox Ingat Perangkat Ini dapat dicentang', async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      const checkbox = page.locator('input[name="remember"]');
      await checkbox.check();
      await expect(checkbox).toBeChecked();
    });

    test('TC-05: Link Lupa Kata Sandi mengarah ke URL recovery SIAKAD Polinema', async ({ page }) => {
      await page.goto(`${BASE_URL}/login`);
      const forgotLink = page.getByRole('link', { name: 'Lupa kata sandi?' });
      await expect(forgotLink).toHaveAttribute('href', 'https://siakad.polinema.ac.id/recovery_password');
    });
  });

  test.describe.serial('2. Alur Pengguna: Dosen Pengampu', () => {
    let context;
    let page;

    test.beforeAll(async ({ browser }) => {
      resetRateLimit();
      context = await browser.newContext();
      page = await context.newPage();
    });

    test.afterAll(async () => {
      await context.close();
    });

    test('TC-06: Login Dosen berhasil dan mengarahkan ke halaman Pilih Semester', async () => {
      await page.goto(`${BASE_URL}/login`);
      await page.fill('input[name="identifier"]', DOSEN_NIDN);
      await page.fill('input[name="password"]', DOSEN_PASSWORD);
      await page.click('button[type="submit"]');

      await page.waitForURL(`${BASE_URL}/dosen/pilih-semester`);
      await expect(page).toHaveURL(`${BASE_URL}/dosen/pilih-semester`);
      await expect(page.locator('.dp-hero__title')).toContainText('Pilih Semester');
    });

    test('TC-07: Dosen dapat mengakses menu Kelompok Bimbingan', async () => {
      await page.goto(`${BASE_URL}/dosen/kelompok`);
      await expect(page).toHaveURL(`${BASE_URL}/dosen/kelompok`);
      await expect(page.locator('text=Kelompok Terdaftar')).toBeVisible();
    });

    test('TC-08: Dosen dapat mengakses menu Penyelesaian Milestone', async () => {
      await page.goto(`${BASE_URL}/dosen/penyelesaian-milestone`);
      await expect(page).toHaveURL(`${BASE_URL}/dosen/penyelesaian-milestone`);
      await expect(page.locator('.sim-page-header__title')).toContainText('Penyelesaian milestone');
    });

    test('TC-09: Dosen dapat mengakses menu Laporan Aktivitas', async () => {
      await page.goto(`${BASE_URL}/dosen/laporan-aktivitas`);
      await expect(page).toHaveURL(`${BASE_URL}/dosen/laporan-aktivitas`);
    });

    test('TC-10: Dosen dapat melakukan logout dan kembali ke halaman login', async () => {
      await page.goto(`${BASE_URL}/dosen/kelompok`);
      await page.getByRole('button', { name: /Keluar/i }).click();
      await expect(page).toHaveURL(`${BASE_URL}/login`);
    });
  });

  test.describe.serial('3. Alur Pengguna: Koordinator PBL', () => {
    let context;
    let page;

    test.beforeAll(async ({ browser }) => {
      resetRateLimit();
      context = await browser.newContext();
      page = await context.newPage();
    });

    test.afterAll(async () => {
      await context.close();
    });

    test('TC-11: Login Koordinator berhasil dan mengarahkan ke Dashboard Koordinator', async () => {
      await page.goto(`${BASE_URL}/login`);
      await page.fill('input[name="identifier"]', KOORDINATOR_NIDN);
      await page.fill('input[name="password"]', KOORDINATOR_PASSWORD);
      await page.click('button[type="submit"]');

      await page.waitForURL(`${BASE_URL}/koordinator/dashboard`);
      await expect(page).toHaveURL(`${BASE_URL}/koordinator/dashboard`);
      await expect(page.locator('.krd-dash-title')).toBeVisible();
    });

    test('TC-12: Koordinator dapat mengakses Master Data Kelas', async () => {
      await page.goto(`${BASE_URL}/koordinator/kelas`);
      await expect(page).toHaveURL(/\/koordinator\/(master-data\/)?kelas/);
      await expect(page.locator('.figma-title-h1')).toHaveText('Master Data');
      await expect(page.locator('.figma-subtab--active')).toContainText('Kelas');
    });

    test('TC-13: Koordinator dapat mengakses Master Data Pengguna', async () => {
      await page.goto(`${BASE_URL}/koordinator/pengguna`);
      await expect(page).toHaveURL(/\/koordinator\/(master-data\/)?pengguna/);
      await expect(page.locator('.figma-title-h1')).toHaveText('Master Data');
      await expect(page.locator('.figma-subtab--active')).toContainText('Pengguna');
    });

    test('TC-14: Koordinator dapat mengakses halaman Notifikasi', async () => {
      await page.goto(`${BASE_URL}/koordinator/notifikasi`);
      await expect(page).toHaveURL(`${BASE_URL}/koordinator/notifikasi`);
      await expect(page.locator('.notif-title')).toBeVisible();
    });

    test('TC-15: Koordinator dapat melakukan logout dan kembali ke halaman login', async () => {
      await page.goto(`${BASE_URL}/koordinator/dashboard`);
      await page.locator('.krd-logout-btn').click();
      await expect(page).toHaveURL(`${BASE_URL}/login`);
    });
  });

  test.describe.serial('4. Alur Pengguna: Mahasiswa', () => {
    let context;
    let page;

    test.beforeAll(async ({ browser }) => {
      resetRateLimit();
      context = await browser.newContext();
      page = await context.newPage();
    });

    test.afterAll(async () => {
      await context.close();
    });

    test('TC-16: Login Mahasiswa berhasil dan mengarahkan ke halaman Pilih Semester', async () => {
      await page.goto(`${BASE_URL}/login`);
      await page.fill('input[name="identifier"]', MAHASISWA_NIM);
      await page.fill('input[name="password"]', MAHASISWA_PASSWORD);
      await page.click('button[type="submit"]');

      await page.waitForURL(`${BASE_URL}/mahasiswa/pilih-semester`);
      await expect(page).toHaveURL(`${BASE_URL}/mahasiswa/pilih-semester`);
      await expect(page.locator('.sim-ps-header__title')).toContainText('Pilih Semester');
    });

    test('TC-17: Mahasiswa dapat membuka Ruang Kerja Dashboard', async () => {
      await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
      await expect(page).toHaveURL(`${BASE_URL}/mahasiswa/dashboard`);
      await expect(page.locator('h1')).toContainText('Halo, Mahasiswa');
    });

    test('TC-18: Header Mahasiswa menampilkan identitas pengguna dan program studi', async () => {
      await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
      await expect(page.locator('.sim-header__user-name')).toContainText(MAHASISWA_NIM);
      await expect(page.locator('.sim-header__user-role')).toContainText('Teknologi Informasi');
    });

    test('TC-19: Mahasiswa dapat mengakses menu Kelompok Saya', async () => {
      await page.goto(`${BASE_URL}/mahasiswa/kelompok`);
      await expect(page).toHaveURL(/\/mahasiswa\/kelompok/);
      await expect(page.locator('.sim-page-header__eyebrow')).toHaveText('KELOMPOK SAYA');
    });

    test('TC-20: Mahasiswa dapat mengakses menu Proposal', async () => {
      await page.goto(`${BASE_URL}/mahasiswa/proposal`);
      await expect(page).toHaveURL(/\/mahasiswa\/proposal/);
      await expect(page.locator('.sim-page-header__title')).toHaveText('Proposal');
    });

    test('TC-21: Mahasiswa dapat mengakses menu Logbook', async () => {
      await page.goto(`${BASE_URL}/mahasiswa/logbook`);
      await expect(page).toHaveURL(/\/mahasiswa\/logbook/);
      await expect(page.locator('.sim-page-header__title')).toHaveText('Logbook');
    });

    test('TC-22: Mahasiswa dapat mengakses menu Milestone', async () => {
      await page.goto(`${BASE_URL}/mahasiswa/milestone`);
      await expect(page).toHaveURL(/\/mahasiswa\/milestone/);
      await expect(page.locator('.sim-page-header__title')).toContainText('Milestone');
    });

    test('TC-23: Mahasiswa dapat mengakses menu Notifikasi', async () => {
      await page.goto(`${BASE_URL}/mahasiswa/notifikasi`);
      await expect(page).toHaveURL(/\/mahasiswa\/notifikasi/);
      await expect(page.locator('.sim-page-header__title')).toHaveText('Notifikasi');
    });

    test('TC-24: Mahasiswa dapat melakukan logout dan kembali ke halaman login', async () => {
      await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
      await page.getByRole('button', { name: /Keluar/i }).click();
      await expect(page).toHaveURL(`${BASE_URL}/login`);
      await expect(page.getByRole('heading', { name: /Masuk ke SIM-PBL/i })).toBeVisible();
    });
  });

});
