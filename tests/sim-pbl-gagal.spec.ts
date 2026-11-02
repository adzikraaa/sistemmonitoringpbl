import { test, expect } from '@playwright/test';

const BASE_URL = process.env.APP_URL || 'http://127.0.0.1:8000';
const MAHASISWA_NIM = process.env.SIAKAD_NIM || '244107060044';
const MAHASISWA_PASSWORD = process.env.SIAKAD_PASSWORD || 'Adzikra_280706';

async function loginMahasiswa(page) {
  await page.goto(`${BASE_URL}/login`);
  await page.fill('input[name="identifier"]', MAHASISWA_NIM);
  await page.fill('input[name="password"]', MAHASISWA_PASSWORD);
  await page.click('button[type="submit"]');
}

test.describe('Test Plan SIM-PBL - skenario gagal saat ini', () => {
  test('TD-02: Nama mahasiswa tampil', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await expect(page.locator('text=Mochammad Tanggaq').first()).toBeVisible();
  });

  test('TD-03: NIM mahasiswa tampil', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await expect(page.locator('text=244107060105').first()).toBeVisible();
  });

  test('TD-04: Kelas mahasiswa tampil', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await expect(page.locator('text=SIB 3B').first()).toBeVisible();
  });

  test('TD-05: Program studi tampil', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await expect(page.locator('text=Teknologi Informasi').first()).toBeVisible();
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
    await page.getByRole('link', { name: 'Proposal' }).click();
    await expect(page).toHaveURL(/\/mahasiswa\/proposal/);
  });

  test('TD-08: Navigasi ke menu milestone', async ({ page }) => {
    await loginMahasiswa(page);
    await page.goto(`${BASE_URL}/mahasiswa/dashboard`);
    await page.getByRole('link', { name: 'Milestone' }).click();
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
    await expect(page.locator('text=Disetujui')).toBeVisible();
  });
});
