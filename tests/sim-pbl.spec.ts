import { test, expect } from '@playwright/test';

const BASE_URL = process.env.APP_URL || 'http://127.0.0.1:8000';

const MAHASISWA_NIM = process.env.SIAKAD_NIM || '244107060044';
const MAHASISWA_PASSWORD = process.env.SIAKAD_PASSWORD || 'Adzikra_280706';
const DOSEN_NIDN = '0099000002';
const DOSEN_PASSWORD = 'password';
const KOORDINATOR_NIDN = '0099000001';
const KOORDINATOR_PASSWORD = 'password';

async function loginMahasiswa(page) {
  await page.goto(`${BASE_URL}/login`);
  await page.fill('input[name="identifier"]', MAHASISWA_NIM);
  await page.fill('input[name="password"]', MAHASISWA_PASSWORD);
  await page.click('button[type="submit"]');
}

async function loginRole(page, role, identifier, password) {
  await page.goto(`${BASE_URL}/login`);

  if (role !== 'mahasiswa') {
    await page.getByRole('button', { name: role === 'dosen' ? 'Dosen' : 'Koordinator' }).click();
  }

  await page.fill('input[name="identifier"]', identifier);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"]');
}

test.describe('Test Plan SIM-PBL', () => {
  test('TC-01: Halaman login tampil', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await expect(page.getByRole('heading', { name: 'Masuk Ke SIM-PBL' })).toBeVisible();
  });

  test('TC-02: Pergantian role', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.getByRole('button', { name: 'Dosen' }).click();
    await expect(page.getByRole('button', { name: /Masuk Sebagai Dosen/i })).toBeVisible();
    await page.getByRole('button', { name: 'Koordinator' }).click();
    await expect(page.getByRole('button', { name: /Masuk Sebagai Koordinator/i })).toBeVisible();
    await page.getByRole('button', { name: 'Mahasiswa' }).click();
    await expect(page.getByRole('button', { name: /Masuk Sebagai Mahasiswa/i })).toBeVisible();
  });

  test('TC-03: Password salah', async ({ page }) => {
    await loginRole(page, 'dosen', DOSEN_NIDN, 'salah');
    await expect(page).toHaveURL(/\/login/);
  });

  test('TC-04: Identifier kosong', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/login/);
  });

 test('TC-05: Login dosen berhasil', async ({ page }) => {
  await loginRole(page, 'dosen', DOSEN_NIDN, DOSEN_PASSWORD);
  await expect(page).toHaveURL(`${BASE_URL}/dosen/pilih-semester`);
});

  test('TC-06: Login koordinator berhasil', async ({ page }) => {
    await loginRole(page, 'koordinator', KOORDINATOR_NIDN, KOORDINATOR_PASSWORD);
    await expect(page).toHaveURL(`${BASE_URL}/koordinator/dashboard`);
  });

  test('TC-07: Dashboard tanpa login', async ({ page }) => {
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await expect(page).toHaveURL(/\/login/);
  });

  test('TC-08: Checkbox Remember Me', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    const checkbox = page.locator('input[name="remember"]');
    await checkbox.check();
    await expect(checkbox).toBeChecked();
  });

  test('TC-09: Lupa kata sandi', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await expect(page.getByRole('link', { name: 'Lupa kata sandi?' })).toHaveAttribute('href', '#');
  });

  
      test('TD-01: Dashboard terbuka setelah login mahasiswa', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await expect(page).toHaveURL(`${BASE_URL}/mahasiswa/dashboard`);
    await expect(page.locator('h1')).toHaveText('Halo, Mahasiswa');
  });

  test('TD-02: Nama mahasiswa tampil', async ({ page }) => {
  await loginMahasiswa(page);

  await expect(page).toHaveURL(`${BASE_URL}/mahasiswa/pilih-semester`);
  await expect(
    page.locator('.sim-header__user-name')
  ).toHaveText('Mahasiswa 244107060044');
});
 test('TD-03: NIM mahasiswa tampil', async ({ page }) => {
  await loginMahasiswa(page);

  await expect(page).toHaveURL(`${BASE_URL}/mahasiswa/pilih-semester`);
  await expect(
    page.locator('.sim-header__user-role')
  ).toContainText('244107060044');
});

  test('TD-04: Kelas mahasiswa tampil', async ({ page }) => {
  await loginMahasiswa(page);

  await expect(page).toHaveURL(`${BASE_URL}/mahasiswa/pilih-semester`);
  await expect(
    page.locator('.sim-header__user-role')
  ).toContainText('· -');
});
  test('TD-06: Navigasi ke menu kelompok', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await page.getByRole('link', { name: 'Kelompok saya' }).click();
    await expect(page).toHaveURL(/\/mahasiswa\/kelompok/);
  });

  test('TD-07: Navigasi ke menu proposal', async ({ page }) => {
  await loginMahasiswa(page);
  await page.goto(`${BASE_URL}/mahasiswa/dashboard`);

  await page.getByRole('link', {
    name: 'Proposal',
    exact: true
  }).click();

  await expect(page).toHaveURL(/\/mahasiswa\/proposal/);
});

  test('TD-08: Navigasi ke menu milestone', async ({ page }) => {
  await loginMahasiswa(page);
  await page.goto(`${BASE_URL}/mahasiswa/dashboard`);

  await page.getByRole('link', {
    name: 'Milestone',
    exact: true
  }).click();

  await expect(page).toHaveURL(/\/mahasiswa\/milestone/);
});

  test('TD-09: Navigasi ke menu notifikasi', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await page.getByRole('link', { name: /Notifikasi/i }).click();
    await expect(page).toHaveURL(/\/mahasiswa\/notifikasi/);
  });

  test('TD-10: Logout dari dashboard', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await page.getByRole('button', { name: /Keluar/i }).click();
    await expect(page).toHaveURL(/\/login/);
  });

  test('TP-01: Halaman proposal terbuka', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/proposal`);
    await expect(page.locator('h1')).toContainText('Proposal');
  });

  test('TP-02: Data proposal tampil', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/proposal`);
    await expect(page.locator('text=Pengembangan Sistem Monitoring PBL')).toBeVisible();
  });

  test('TP-03: User dapat melihat status proposal', async ({ page }) => {
  await loginMahasiswa(page);
  await page.goto(`${BASE_URL}/mahasiswa/proposal`);

  await expect(
    page.getByText('Disetujui', { exact: true })
  ).toBeVisible();
});
});