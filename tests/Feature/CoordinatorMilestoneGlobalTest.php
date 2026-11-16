<?php

namespace Tests\Feature;

use App\Models\Koordinator;
use App\Models\Periode;
use App\Models\Milestone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoordinatorMilestoneGlobalTest extends TestCase
{
    use RefreshDatabase;

    protected Periode $periode;
    protected Koordinator $koordinator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-08-31',
            'tanggal_selesai' => '2027-01-15',
            'is_aktif' => true,
        ]);

        $this->koordinator = Koordinator::create([
            'nama' => 'Nadia Kusuma',
            'email' => 'koordinator@test.com',
            'password' => bcrypt('password'),
            'nidn' => '0000000001',
        ]);
    }

    public function test_guest_cannot_access_milestone_global_page(): void
    {
        $response = $this->get(route('koordinator.milestone-global.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_koordinator_can_view_milestone_global_page(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->get(route('koordinator.milestone-global.index'));

        $response->assertStatus(200);
        $response->assertSee('Milestone Global');
        $response->assertSee('Tambah Milestone');
    }

    public function test_koordinator_can_create_milestone_global(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->post(route('koordinator.milestone-global.store'), [
                'judul' => 'Prototype & UI awal',
                'deskripsi' => 'Tahun ajaran baru.',
                'deadline' => '2026-11-06',
                'urutan' => 1,
            ]);

        $response->assertRedirect(route('koordinator.milestone-global.index'));
        $this->assertDatabaseHas('milestone', [
            'periode_id' => $this->periode->id,
            'judul' => 'Prototype & UI awal',
            'urutan' => 1,
        ]);

        $page = $this->actingAs($this->koordinator, 'koordinator')
            ->get(route('koordinator.milestone-global.index'));

        $page->assertSee('Prototype & UI awal');
    }

    public function test_koordinator_can_update_milestone_global(): void
    {
        $milestone = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M1',
            'judul' => 'Prototype awal',
            'deskripsi' => 'Deskripsi lama',
            'urutan' => 1,
            'deadline' => '2026-11-06',
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->put(route('koordinator.milestone-global.update', $milestone->id), [
                'judul' => 'Prototype & UI awal revisi',
                'deskripsi' => 'Deskripsi baru.',
                'deadline' => '2026-11-15',
                'urutan' => 1,
            ]);

        $response->assertRedirect(route('koordinator.milestone-global.index'));
        $this->assertDatabaseHas('milestone', [
            'id' => $milestone->id,
            'judul' => 'Prototype & UI awal revisi',
            'deskripsi' => 'Deskripsi baru.',
        ]);
    }

    public function test_duplicate_urutan_is_auto_assigned_to_next_available_slot(): void
    {
        Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M1',
            'judul' => 'Prototype awal',
            'deskripsi' => 'Deskripsi lama',
            'urutan' => 1,
            'deadline' => '2026-11-06',
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->post(route('koordinator.milestone-global.store'), [
                'judul' => 'Tambah milestone baru',
                'deskripsi' => 'Jadwal dari form tambah.',
                'deadline' => '2026-11-20',
                'urutan' => 1,
            ]);

        $response->assertRedirect(route('koordinator.milestone-global.index'));
        $this->assertDatabaseHas('milestone', [
            'periode_id' => $this->periode->id,
            'judul' => 'Tambah milestone baru',
            'urutan' => 2,
        ]);
    }
}
