<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoordinatorKelasManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_membuat_kelas_mengarahkan_ke_penugasan_dosen_tanpa_memindahkan_mahasiswa(): void
    {
        $koordinator = $this->buatKoordinator();
        $periode = $this->buatPeriode();
        $dosen = Dosen::create([
            'nama' => 'Dosen Pengampu',
            'email' => 'dosen-kelas@test.com',
            'password' => 'password123',
            'nidn' => '900000021',
            'is_aktif' => true,
        ]);
        MataKuliah::create([
            'kode' => 'MK001',
            'nama_matkul' => 'Mata Kuliah Uji',
            'sks' => 3,
            'semester' => 5,
            'is_aktif' => true,
        ]);
        $mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Tanpa Kelas',
            'email' => 'mahasiswa-tanpa-kelas@test.com',
            'password' => 'password123',
            'nim' => '244107099999',
            'kelas_id' => null,
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->post(route('koordinator.master-data.kelas.store'), [
                'nama_kelas' => 'SIB 3C',
                'prodi' => 'D4 Sistem Informasi Bisnis',
                'periode_id' => $periode->id,
                'is_aktif' => 1,
                'dosen_id' => $dosen->id,
            ]);

        $kelas = Kelas::query()->where('nama_kelas', 'SIB 3C')->firstOrFail();
        $response->assertRedirect(route('koordinator.penugasan-dosen.index', ['kelas_id' => $kelas->id]));
        $this->assertNull($mahasiswa->fresh()->kelas_id);
        $this->assertDatabaseMissing('dosen_pengampu', ['kelas_id' => $kelas->id]);
    }

    public function test_halaman_kelas_tidak_menampilkan_dosen_koordinator_atau_angka_dummy(): void
    {
        $koordinator = $this->buatKoordinator();
        $periode = $this->buatPeriode();
        Kelas::create([
            'nama_kelas' => 'SIB Kosong',
            'prodi' => 'D4 Sistem Informasi Bisnis',
            'periode_id' => $periode->id,
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->get(route('koordinator.master-data.kelas'));

        $response->assertOk();
        $response->assertDontSee('Dosen koordinator');
        $response->assertDontSee('SIB301');
        $response->assertSee('Belum ada mata kuliah');
        $response->assertSee('class="td-mahasiswa-num">0</div>', false);
    }

    private function buatKoordinator(): Koordinator
    {
        return Koordinator::create([
            'nama' => 'Koordinator Kelas',
            'email' => 'koordinator-kelas@test.com',
            'password' => 'password123',
            'nidn' => '900000020',
        ]);
    }

    private function buatPeriode(): Periode
    {
        return Periode::create([
            'nama_periode' => 'Ganjil',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);
    }
}
