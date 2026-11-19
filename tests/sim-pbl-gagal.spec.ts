import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

const BASE_URL = process.env.APP_URL || 'http://127.0.0.1:8000';

const MAHASISWA_NIM = process.env.SIAKAD_NIM || '244107060044';
const MAHASISWA_PASSWORD = process.env.SIAKAD_PASSWORD || 'Adzikra_280706';
const DOSEN_NIDN = '0099000002';
const DOSEN_PASSWORD = 'password';

function resetRateLimit() {
  try {
    execSync('php artisan cache:clear');
  } catch {
    // ignore
  }
}

test.describe('E2E SIM-PBL - Skenario Negatif & Validasi Batas Hak Akses', () => {

  test.beforeAll(() => {
    resetRateLimit();
  });

  test('TN-01: Akses dashboard mahasiswa tanpa login ditolak dan diarahkan ke login', async ({ page }) => {
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await expect(page).toHaveURL(`${BASE_URL}/login`);
    await expect(page.getByRole('heading', { name: /Masuk ke SIM-PBL/i })).toBeVisible();
  });

  test('TN-02: Akses dashboard dosen tanpa login ditolak dan diarahkan ke login', async ({ page }) => {
    await page.goto(`${BASE_URL}/dosen/dashboard`);
    await expect(page).toHaveURL(`${BASE_URL}/login`);
    await expect(page.getByRole('heading', { name: /Masuk ke SIM-PBL/i })).toBeVisible();
  });

  test('TN-03: Akses dashboard koordinator tanpa login ditolak dan diarahkan ke login', async ({ page }) => {
    await page.goto(`${BASE_URL}/koordinator/dashboard`);
    await expect(page).toHaveURL(`${BASE_URL}/login`);
    await expect(page.getByRole('heading', { name: /Masuk ke SIM-PBL/i })).toBeVisible();
  });

  test('TN-04: Akses halaman internal proposal tanpa login ditolak dan diarahkan ke login', async ({ page }) => {
    await page.goto(`${BASE_URL}/mahasiswa/proposal`);
    await expect(page).toHaveURL(`${BASE_URL}/login`);
    await expect(page.getByRole('heading', { name: /Masuk ke SIM-PBL/i })).toBeVisible();
  });

  test('TN-05: Form login mewajibkan pengisian identifier (client-side required)', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="password"]', 'anyPassword');
    await page.click('button[type="submit"]');

    // Validasi form HTML5 menahan submission tetap di /login
    await expect(page).toHaveURL(`${BASE_URL}/login`);
    const identifierInput = page.locator('input[name="identifier"]');
    const isRequired = await identifierInput.getAttribute('required');
    expect(isRequired).not.toBeNull();
  });

  test('TN-06: Form login mewajibkan pengisian kata sandi (client-side required)', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="identifier"]', DOSEN_NIDN);
    await page.click('button[type="submit"]');

    // Validasi form HTML5 menahan submission tetap di /login
    await expect(page).toHaveURL(`${BASE_URL}/login`);
    const passwordInput = page.locator('input[name="password"]');
    const isRequired = await passwordInput.getAttribute('required');
    expect(isRequired).not.toBeNull();
  });

  test('TN-07: Login dengan password salah ditolak dan menampilkan pesan kesalahan', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="identifier"]', DOSEN_NIDN);
    await page.fill('input[name="password"]', 'passwordsalah123');
    await page.click('button[type="submit"]');

    await expect(page).toHaveURL(`${BASE_URL}/login`);
    await expect(page.locator('.login-error')).toBeVisible();
    await expect(page.locator('.login-error')).toContainText('salah');
  });

  test('TN-08: Login dengan identifier tidak terdaftar ditolak dan menampilkan pesan kesalahan', async ({ page }) => {
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="identifier"]', '0000000000');
    await page.fill('input[name="password"]', 'passwordsalah123');
    await page.click('button[type="submit"]');

    await expect(page).toHaveURL(`${BASE_URL}/login`);
    await expect(page.locator('.login-error')).toBeVisible();
  });

  test('TN-09: Mahasiswa login ditolak saat mencoba mengakses area Koordinator', async ({ page }) => {
    resetRateLimit();
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="identifier"]', MAHASISWA_NIM);
    await page.fill('input[name="password"]', MAHASISWA_PASSWORD);
    await page.click('button[type="submit"]');

    await page.waitForURL(`${BASE_URL}/mahasiswa/pilih-semester`);

    // Mencoba bypass akses ke dashboard koordinator
    await page.goto(`${BASE_URL}/koordinator/dashboard`);
    // Harus dicegah dari area koordinator dan diredirect ke area mahasiswa
    await expect(page).not.toHaveURL(/\/koordinator/);
    await expect(page).toHaveURL(`${BASE_URL}/mahasiswa/dashboard`);
  });

  test('TN-10: Dosen login ditolak saat mencoba mengakses area Koordinator', async ({ page }) => {
    resetRateLimit();
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="identifier"]', DOSEN_NIDN);
    await page.fill('input[name="password"]', DOSEN_PASSWORD);
    await page.click('button[type="submit"]');

    await page.waitForURL(`${BASE_URL}/dosen/pilih-semester`);

    // Mencoba bypass akses ke dashboard koordinator
    await page.goto(`${BASE_URL}/koordinator/dashboard`);
    // Harus dicegah dari area koordinator dan diredirect ke area dosen
    await expect(page).not.toHaveURL(/\/koordinator/);
    await expect(page).toHaveURL(`${BASE_URL}/dosen/pilih-semester`);
  });

});
