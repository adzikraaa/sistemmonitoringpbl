<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\MataKuliah;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenClassSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_can_access_semester_and_activity_pages(): void
    {
        $dosen = Dosen::create([
            'nama' => 'Dian Hanifudin Subhi',
            'email' => 'dian.dosen@example.com',
            'password' => 'secret123',
            'nidn' => '000111222',
            'is_aktif' => true,
        ]);

        $this->actingAs($dosen, 'dosen')
            ->get(route('dosen.pilih-semester'))
            ->assertOk();

        $this->actingAs($dosen, 'dosen')
            ->get(route('dosen.laporan-aktivitas'))
            ->assertOk();

        $this->actingAs($dosen, 'dosen')
            ->get(route('dosen.notifikasi'))
            ->assertOk();
    }

    public function test_dosen_can_select_a_semester_and_land_on_dashboard(): void
    {
        $dosen = Dosen::create([
            'nama' => 'Dian Hanifudin Subhi',
            'email' => 'dian.dosen@example.com',
            'password' => 'secret123',
            'nidn' => '000111222',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($dosen, 'dosen')
            ->from(route('dosen.pilih-semester'))
            ->post(route('dosen.set-semester'), ['semester_id' => 'smt5']);

        $response->assertRedirect(route('dosen.dashboard'));

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_dosen_dashboard_is_available_after_semester_selection_without_class_selection(): void
    {
        $dosen = Dosen::create([
            'nama' => 'Dian Hanifudin Subhi',
            'email' => 'dian.dosen@example.com',
            'password' => 'secret123',
            'nidn' => '000111222',
            'is_aktif' => true,
        ]);

        $this->actingAs($dosen, 'dosen')
            ->withSession(['dosen.semester_id' => 'smt5'])
            ->get(route('dosen.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_dosen_can_select_a_class_and_store_it_in_session(): void
    {
        $periode = Periode::create([
            'nama_periode' => 'Semester 5',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-12-31',
            'is_aktif' => true,
        ]);

        $dosen = Dosen::create([
            'nama' => 'Dian Hanifudin Subhi',
            'email' => 'dian.dosen@example.com',
            'password' => 'secret123',
            'nidn' => '000111222',
            'is_aktif' => true,
        ]);

        $matkul = MataKuliah::create([
            'nama_matkul' => 'Proyek PBL',
            'prodi' => 'Sistem Informasi Bisnis',
            'sks' => 3,
            'is_aktif' => true,
        ]);

        $kelasA = Kelas::create([
            'nama_kelas' => 'SIB 3A',
            'prodi' => 'Sistem Informasi Bisnis',
            'periode_id' => $periode->id,
            'is_aktif' => true,
        ]);

        $kelasB = Kelas::create([
            'nama_kelas' => 'SIB 3B',
            'prodi' => 'Sistem Informasi Bisnis',
            'periode_id' => $periode->id,
            'is_aktif' => true,
        ]);

        DosenPengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $matkul->id,
            'kelas_id' => $kelasA->id,
        ]);

        DosenPengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $matkul->id,
            'kelas_id' => $kelasB->id,
        ]);

        Kelompok::create([
            'nama_kelompok' => 'Kelompok 01',
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelasB->id,
            'periode_id' => $periode->id,
            'jadwal_bimbingan' => 'Senin, 09:00',
            'tanggal_dibuat' => now()->toDateString(),
        ]);

        $response = $this->actingAs($dosen, 'dosen')
            ->post(route('dosen.pilih-kelas.select'), ['class_id' => $kelasB->id]);

        $response->assertSessionHas('dosen_active_class_id', $kelasB->id);
        $this->assertEquals($kelasB->id, session('dosen_active_class_id'));

        $this->actingAs($dosen, 'dosen')
            ->get(route('dosen.kelompok', ['class_id' => $kelasB->id]))
            ->assertOk()
            ->assertSee('SIB 3B');
    }
}
