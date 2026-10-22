<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PilihSemesterDebugTest extends DuskTestCase
{
    public function test_debug_pilih_semester(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/login');
            $b->driver->manage()->deleteAllCookies();
            $b->visit('/login')->pause(300)
              ->type('identifier', '244107060044')
              ->type('password', 'Adzikra_280706')
              ->click('button[type="submit"]')
              ->waitForLocation('/mahasiswa/pilih-semester', 30)
              ->pause(1500);

            $info = $b->script("return JSON.stringify({
                teks: document.body.innerText.substring(0, 600),
                elemen: [...document.querySelectorAll('button,a[href],select,input[type=radio],input[type=submit]')]
                    .map(e => e.tagName + ' | ' + (e.getAttribute('href') || e.name || e.value || '') + ' | ' + e.innerText.trim().substring(0, 40))
            });")[0];
            fwrite(STDERR, "\n" . $info . "\n");
            $b->screenshot('debug-pilih-semester');
            $this->assertTrue(true);
        });
    }
}