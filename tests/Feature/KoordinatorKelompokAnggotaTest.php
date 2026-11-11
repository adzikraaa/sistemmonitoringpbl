<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoordinatorKelompokAnggotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_can_add_member_to_kelompok(): void
    {
        $koordinator = Koordinator::create([
            'nama' => 'Koordinator Test',
            'email' => 'koordinator@test.com',
            'password' => 'password123',
            'nidn' => '000111222',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3C',
            'status' => 'Aktif',
        ]);

        $periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2026-12-31',
            'is_aktif' => true,
        ]);

        $dosen = Dosen::create([
            'nama' => 'Dr. Dosen Test',
            'nidn' => '0034567890',
            'email' => 'dosen@test.com',
            'password' => 'password123',
            'is_aktif' => true,
        ]);

        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Test',
            'judul_proyek' => 'Proyek Test',
            'kelas_id' => $kelas->id,
            'periode_id' => $periode->id,
            'dosen_id' => $dosen->id,
            'jadwal_bimbingan' => 'Jumat, 13.00',
            'tanggal_dibuat' => now()->toDateString(),
        ]);

        $mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Baru',
            'email' => 'mahasiswa@test.com',
            'password' => 'password123',
            'nim' => '2025001',
            'kelas_id' => $kelas->id,
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')->post(route('koordinator.kelompok.anggota.store', $kelompok->id), [
            'mahasiswa_id' => $mahasiswa->id,
            'role_label' => 'Anggota',
        ]);

        $response->assertRedirect(route('koordinator.kelompok.show', $kelompok->id));
        $this->assertDatabaseHas('anggota_kelompok', [
            'kelompok_id' => $kelompok->id,
            'mahasiswa_id' => $mahasiswa->id,
            'role_label' => 'Anggota',
        ]);
    }
}
