<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginSuiteTest extends DuskTestCase
{
    private const ID_MAHASISWA   = '244107060044';
private const PASS_MAHASISWA = 'Adzikra_280706';
    private const ID_DOSEN       = '0099000002';
    private const ID_KOORDINATOR = '0099000001';
    private const PASS           = 'password';

    // urutan tombol role di halaman login
    private const ROLE_MAHASISWA   = 1;
    private const ROLE_DOSEN       = 2;
    private const ROLE_KOORDINATOR = 3;

    /** Hapus sesi supaya login dari test sebelumnya tidak terbawa. */
    private function bersih(Browser $b): void
    {
        $b->visit('/login');
        $b->driver->manage()->deleteAllCookies();
    }

   private function masuk(Browser $b, int $urutanRole, string $id, string $pass = self::PASS): void
{
    $this->bersih($b);
    $b->visit('/login')
      ->click("button:nth-of-type($urutanRole)")
      ->pause(500)
      ->type('identifier', $id)
      ->type('password', $pass)
      ->click('button[type="submit"]');
}


    public function test_01_halaman_login_tampil(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Masuk Ke SIM-PBL')
                ->assertSee('Mahasiswa')
                ->assertSee('Dosen')
                ->assertSee('Koordinator')
                ->assertSee('Kata Sandi')
                ->assertSee('Ingat Perangkat Ini')
                ->assertSee('Lupa kata sandi?')
                ->screenshot('TC-LGN-01_pass');
        });
    }

    public function test_02_pergantian_role_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertInputValue('input[name="role"]', 'mahasiswa')
                ->click('button:nth-of-type(2)')
                ->pause(500)
                ->assertInputValue('input[name="role"]', 'dosen')
                ->click('button:nth-of-type(3)')
                ->pause(500)
                ->assertInputValue('input[name="role"]', 'koordinator')
                ->screenshot('TC-LGN-02_pass');
        });
    }

    public function test_03_login_dosen_dengan_password_salah(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->click('button:nth-of-type(2)')
                ->type('identifier', self::ID_DOSEN)
                ->type('password', 'password_salah_123')
                ->click('button[type="submit"]')
                ->waitForText('NIDN atau password salah.')
                ->assertSee('NIDN atau password salah.')
                ->screenshot('TC-LGN-03_pass');
        });
    }

    public function test_04_identifier_kosong_ditolak_server(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->click('button:nth-of-type(2)')
                ->type('password', self::PASS)
                ->click('button[type="submit"]')
                ->waitForText('The identifier field is required.')
                ->assertSee('The identifier field is required.')
                ->screenshot('TC-LGN-04_pass');
        });
    }

    public function test_05_login_dosen_berhasil(): void
    {
        $this->browse(function (Browser $b) {
            $this->masuk($b, self::ROLE_DOSEN, self::ID_DOSEN);
            $b->waitForLocation('/dosen/dashboard')
              ->assertPathIs('/dosen/dashboard')
              ->screenshot('TC-LGN-05_pass');
        });
    }

    public function test_06_login_koordinator_berhasil(): void
    {
        $this->browse(function (Browser $b) {
            $this->masuk($b, self::ROLE_KOORDINATOR, self::ID_KOORDINATOR);
            $b->waitForLocation('/koordinator/dashboard')
              ->assertPathIs('/koordinator/dashboard')
              ->screenshot('TC-LGN-06_pass');
        });
    }

    public function test_07_dashboard_tidak_bisa_diakses_tanpa_login(): void
    {
        $this->browse(function (Browser $b) {
            $this->bersih($b);
            foreach (['dosen', 'koordinator', 'mahasiswa'] as $role) {
                $b->visit("/$role/dashboard")->assertPathIs('/login');
            }
            $b->screenshot('TC-LGN-07_pass');
        });
    }

    public function test_08_checkbox_remember_me_tersedia(): void
    {
        $this->browse(function (Browser $b) {
            $this->bersih($b);
            $b->visit('/login')
              ->assertSee('Ingat Perangkat Ini')
              ->assertPresent('input[type="checkbox"]');
            $b->script("document.querySelector('input[type=checkbox]').click()");
            $this->assertTrue($b->element('input[type="checkbox"]')->isSelected());
            $b->screenshot('TC-LGN-08_pass');
        });
    }

    public function test_09_tautan_lupa_password(): void
{
        $this->browse(function (Browser $b) {
            $this->bersih($b);
            $b->visit('/login')->assertSee('Lupa kata sandi?');

            $href = $b->script(
            "var a=[...document.querySelectorAll('a')].find(x=>x.textContent.includes('Lupa kata sandi?')); return a ? a.getAttribute('href') : 'TIDAK_ADA';"
         )[0];

        $b->screenshot('TC-LGN-09_' . ($href === '#' ? 'fail' : 'pass'));
        $this->assertNotSame('TIDAK_ADA', $href, 'Elemen <a> lupa kata sandi tidak ditemukan');
        $this->assertNotSame('#', $href, 'Tautan lupa kata sandi masih href="#"');
    });
}

 public function test_10_login_mahasiswa_berhasil(): void
{
    $this->browse(function (Browser $b) {
        $this->masuk($b, self::ROLE_MAHASISWA, self::ID_MAHASISWA, self::PASS_MAHASISWA);
        $b->waitForLocation('/mahasiswa/pilih-semester', 60)
          ->assertPathIs('/mahasiswa/pilih-semester')
          ->screenshot('TC-LGN-10_pass');
    });
}

public function test_11_logout(): void
{
    $this->browse(function (Browser $b) {
        $this->masuk($b, self::ROLE_MAHASISWA, self::ID_MAHASISWA, self::PASS_MAHASISWA);
        $b->waitForLocation('/mahasiswa/pilih-semester', 60);

        $ketemu = $b->script(
            'var el = [...document.querySelectorAll("button,a")].find(e => e.innerText.trim().toLowerCase() === "keluar"); if (el) { el.click(); return true; } return false;'
        )[0];
        $this->assertTrue($ketemu, 'Tombol Keluar tidak ditemukan');

        $b->pause(2000)
          ->visit('/mahasiswa/dashboard')
          ->assertPathIs('/login')
          ->screenshot('TC-LGN-11_pass');
    });
}}