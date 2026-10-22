<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardMahasiswaTest extends DuskTestCase
{
    private const ID_MAHASISWA = '244107060044';
    private const PASS         = 'Adzikra_280706';

    private function masukMahasiswa(Browser $b): void
    {
        $b->visit('/login');
        $b->driver->manage()->deleteAllCookies();
        $b->visit('/login')
          ->pause(300)
          ->type('identifier', self::ID_MAHASISWA)
          ->type('password', self::PASS)
          ->click('button[type="submit"]')
          ->waitForLocation('/mahasiswa/pilih-semester', 60)
          ->pause(500);

        // alur aplikasi: pilih semester dulu, baru masuk dashboard
        $b->script('[...document.querySelectorAll("a")].find(a => a.href.endsWith("/mahasiswa/dashboard")).click();');
        $b->waitForLocation('/mahasiswa/dashboard', 60);
    }

    private function teks(Browser $b): string
    {
        return (string) $b->script('return document.body.innerText')[0];
    }

    private function links(Browser $b): array
    {
        return $b->script(
            'return [...document.querySelectorAll("a[href]")].map(a => a.getAttribute("href") === "#" ? "#" : a.pathname);'
        )[0];
    }

    public function test_dsm_01_dashboard_tampil_setelah_login(): void
    {
        $this->browse(function (Browser $b) {
            $this->masukMahasiswa($b);
            $b->assertPathIs('/mahasiswa/dashboard')
              ->assertDontSee('Whoops')
              ->assertDontSee('Server Error');
            $teks = trim($this->teks($b));
            $this->assertNotEmpty($teks);
            fwrite(STDERR, "\n--- TEKS DASHBOARD ---\n" . substr($teks, 0, 1000) . "\n");
            fwrite(STDERR, "--- LINK ---\n" . implode("\n", $this->links($b)) . "\n");
            $b->screenshot('TC-DSM-01_pass');
        });
    }

    public function test_dsm_02_menu_sesuai_role(): void
    {
        $this->browse(function (Browser $b) {
            $this->masukMahasiswa($b);
            $links = $this->links($b);
            $this->assertNotEmpty($links, 'Tidak ada tautan menu di dashboard');
            foreach ($links as $h) {
                $this->assertStringNotContainsString('/dosen', $h, "Menu mahasiswa memuat tautan dosen: $h");
                $this->assertStringNotContainsString('/koordinator', $h, "Menu mahasiswa memuat tautan koordinator: $h");
            }
            $b->screenshot('TC-DSM-02_pass');
        });
    }

    public function test_dsm_04_navigasi_menu(): void
    {
        $this->browse(function (Browser $b) {
            $this->masukMahasiswa($b);
            $mati = [];
            $tujuan = [];
            foreach ($this->links($b) as $h) {
                if ($h === '#' || $h === '') { $mati[] = '#'; continue; }
                if (preg_match('/logout|keluar/i', $h)) { continue; }
                $tujuan[$h] = true;
            }
            foreach (array_keys($tujuan) as $path) {
                $b->visit($path)->assertDontSee('Whoops')->assertDontSee('Server Error');
                $this->assertStringNotContainsStringIgnoringCase('not found', $this->teks($b), "Halaman tidak ditemukan: $path");
            }
            $this->assertEmpty($mati, 'Ada ' . count($mati) . ' tautan mati (href="#") di dashboard');
            $b->screenshot('TC-DSM-04_pass');
        });
    }

    public function test_dsm_05_batasan_hak_akses(): void
    {
        $this->browse(function (Browser $b) {
            $this->masukMahasiswa($b);
            foreach (['/dosen/dashboard', '/koordinator/dashboard'] as $url) {
                $b->visit($url);
                $path    = parse_url($b->driver->getCurrentURL(), PHP_URL_PATH);
                $teks    = strtolower($this->teks($b));
                $ditolak = $path !== $url
                    || str_contains($teks, '403')
                    || str_contains($teks, 'forbidden')
                    || str_contains($teks, 'tidak memiliki akses');
                $this->assertTrue($ditolak, "Mahasiswa bisa membuka $url");
            }
            $b->screenshot('TC-DSM-05_pass');
        });
    }
}