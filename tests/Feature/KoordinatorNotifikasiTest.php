<?php

namespace Tests\Feature;

use App\Models\Koordinator;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoordinatorNotifikasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_koordinator_can_access_notifikasi_page(): void
    {
        Periode::create([
            'nama_periode' => '2026/2027 Ganjil',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $koordinator = Koordinator::create([
            'nama' => 'Koordinator Utama',
            'email' => 'koordinator.notif@test.com',
            'password' => bcrypt('password123'),
            'nidn' => '0099887766',
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->get(route('koordinator.notifikasi'));

        $response->assertOk()
            ->assertSee('Notifikasi & Aktivitas Sistem')
            ->assertSee('Total Notifikasi')
            ->assertSee('Belum Dibaca');
    }

    public function test_koordinator_can_mark_all_notifications_as_read(): void
    {
        $koordinator = Koordinator::create([
            'nama' => 'Koordinator Utama',
            'email' => 'koordinator.notif2@test.com',
            'password' => bcrypt('password123'),
            'nidn' => '0099887767',
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->post(route('koordinator.notifikasi.mark-all-read'));

        $response->assertRedirect(route('koordinator.notifikasi'));
        $response->assertSessionHas('all_notif_read', true);
    }
}

