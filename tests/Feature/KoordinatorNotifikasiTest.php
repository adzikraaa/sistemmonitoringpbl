<?php

namespace Tests\Feature;

use App\Models\Koordinator;
use App\Models\Notifikasi;
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

        $koordinator->notifikasi()->create([
            'judul' => 'Dosen Menyetujui Logbook',
            'isi' => 'Dosen Dian telah mereview logbook Kelompok Satu.',
            'tautan' => route('koordinator.kelompok.index'),
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->get(route('koordinator.notifikasi'));

        $response->assertOk()
            ->assertSee('Notifikasi & Aktivitas Sistem')
            ->assertSee('Total Notifikasi')
            ->assertSee('Belum Dibaca')
            ->assertSee('Dosen Menyetujui Logbook')
            ->assertSee('Dosen Dian telah mereview logbook Kelompok Satu.');
    }

    public function test_koordinator_can_mark_all_notifications_as_read(): void
    {
        $koordinator = Koordinator::create([
            'nama' => 'Koordinator Utama',
            'email' => 'koordinator.notif2@test.com',
            'password' => bcrypt('password123'),
            'nidn' => '0099887767',
        ]);

        $notification = $koordinator->notifikasi()->create([
            'judul' => 'Dosen Meminta Revisi Proposal',
            'isi' => 'Proposal kelompok perlu direvisi.',
            'tautan' => route('koordinator.kelompok.index'),
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->post(route('koordinator.notifikasi.mark-all-read'));

        $response->assertRedirect(route('koordinator.notifikasi'));
        $this->assertNotNull($notification->fresh()->dibaca_pada);
    }

    public function test_koordinator_can_mark_own_notification_as_read(): void
    {
        $koordinator = Koordinator::create([
            'nama' => 'Koordinator Utama',
            'email' => 'koordinator.notif3@test.com',
            'password' => bcrypt('password123'),
            'nidn' => '0099887768',
        ]);

        $notification = $koordinator->notifikasi()->create([
            'judul' => 'Dosen Menyetujui Milestone',
            'isi' => 'Milestone kelompok telah disetujui.',
            'tautan' => route('koordinator.kelompok.index'),
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->post(route('koordinator.notifikasi.mark-read', $notification));

        $response->assertRedirect(route('koordinator.kelompok.index'));
        $this->assertNotNull($notification->fresh()->dibaca_pada);
    }

    public function test_koordinator_cannot_mark_another_coordinator_notification_as_read(): void
    {
        $owner = Koordinator::create([
            'nama' => 'Koordinator Pemilik',
            'email' => 'koordinator.owner@test.com',
            'password' => bcrypt('password123'),
            'nidn' => '0099887769',
        ]);
        $other = Koordinator::create([
            'nama' => 'Koordinator Lain',
            'email' => 'koordinator.other@test.com',
            'password' => bcrypt('password123'),
            'nidn' => '0099887770',
        ]);
        $notification = Notifikasi::create([
            'penerima_type' => $owner->getMorphClass(),
            'penerima_id' => $owner->id,
            'judul' => 'Aktivitas Dosen',
            'isi' => 'Aktivitas hanya untuk koordinator pemilik.',
            'tautan' => route('koordinator.notifikasi'),
        ]);

        $this->actingAs($other, 'koordinator')
            ->post(route('koordinator.notifikasi.mark-read', $notification))
            ->assertForbidden();

        $this->assertNull($notification->fresh()->dibaca_pada);
    }
}
