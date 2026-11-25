<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\PengumpulanMilestone;
use App\Models\PenyelesaianMilestone;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoordinatorDashboardRealDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_menghitung_data_aktual_tanpa_fallback_dummy(): void
    {
        $koordinator = $this->buatKoordinator('data');
        $periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027', 'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01', 'tanggal_selesai' => '2027-01-31', 'is_aktif' => true,
        ]);
        $kelas = Kelas::create(['nama_kelas' => 'SIB 3A', 'periode_id' => $periode->id, 'is_aktif' => true]);
        Kelas::create(['nama_kelas' => 'SIB Nonaktif', 'periode_id' => $periode->id, 'is_aktif' => false]);
        $dosen = Dosen::create([
            'nama' => 'Dosen Pembimbing Data', 'email' => 'dosen-dashboard@test.com',
            'password' => 'password123', 'nidn' => '800000001', 'is_aktif' => true,
        ]);
        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Data Nyata', 'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id, 'periode_id' => $periode->id,
            'status' => 'Aktif', 'tanggal_dibuat' => '2026-09-01',
        ]);
        $mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Anggota', 'email' => 'anggota-dashboard@test.com',
            'password' => 'password123', 'nim' => '202600001', 'kelas_id' => $kelas->id, 'is_aktif' => true,
        ]);
        Mahasiswa::create([
            'nama' => 'Mahasiswa Belum Berkelompok', 'email' => 'tanpa-kelompok-dashboard@test.com',
            'password' => 'password123', 'nim' => '202600002', 'kelas_id' => $kelas->id, 'is_aktif' => true,
        ]);
        $kelompok->mahasiswa()->attach($mahasiswa->id, ['role_label' => 'Ketua', 'is_ketua' => true]);
        $mataKuliahSatu = MataKuliah::create([
            'kode' => 'SIB301', 'nama_matkul' => 'Pemrograman Web Lanjut', 'is_aktif' => true,
        ]);
        $mataKuliahDua = MataKuliah::create([
            'kode' => 'SIB302', 'nama_matkul' => 'Manajemen Proyek Sistem Informasi', 'is_aktif' => true,
        ]);
        DosenPengampu::create([
            'dosen_id' => $dosen->id, 'mata_kuliah_id' => $mataKuliahSatu->id, 'kelas_id' => $kelas->id,
        ]);
        $milestone = Milestone::create([
            'periode_id' => $periode->id, 'kode' => 'M01', 'judul' => 'Implementasi Sistem',
            'urutan' => 1, 'deadline' => '2020-01-01',
        ]);
        PengumpulanMilestone::create([
            'kelompok_id' => $kelompok->id, 'milestone_id' => $milestone->id,
            'diunggah_oleh' => $mahasiswa->id, 'mata_kuliah' => $mataKuliahSatu->slug,
            'file_name' => 'laporan.pdf', 'file_path' => 'milestone/laporan.pdf', 'status' => 'disetujui',
        ]);
        PenyelesaianMilestone::create([
            'kelompok_id' => $kelompok->id, 'milestone_id' => $milestone->id,
            'mata_kuliah_id' => $mataKuliahSatu->id, 'status' => PenyelesaianMilestone::STATUS_SELESAI,
            'diverifikasi_oleh' => $dosen->id,
        ]);
        PenyelesaianMilestone::create([
            'kelompok_id' => $kelompok->id, 'milestone_id' => $milestone->id,
            'mata_kuliah_id' => $mataKuliahDua->id, 'status' => PenyelesaianMilestone::STATUS_SELESAI,
            'diverifikasi_oleh' => $dosen->id,
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')->get(route('koordinator.dashboard'));

        $response->assertOk();
        $response->assertViewHas('kelasCount', 1);
        $response->assertViewHas('totalKelompok', 1);
        $response->assertViewHas('mahasiswaCount', 2);
        $response->assertViewHas('dosenPembimbingCount', 1);
        $response->assertViewHas('totalSelesaiSemua', 1);
        $response->assertViewHas('totalTargetSemua', 2);
        $response->assertViewHas('pctCapaian', 50.0);
        $response->assertViewHas('overdueAlert', fn (?array $alert): bool => $alert !== null && $alert['remaining'] === 1);
        $response->assertSee('1 / 2 target');
        $response->assertDontSee('Capaian seluruh program');
        $response->assertDontSee('Setiap milestone memiliki bobot yang sama.');
        $response->assertDontSee('SIB Nonaktif');
        $response->assertDontSee('2 kelompok melewati tenggat proposal');
    }

    public function test_dashboard_tanpa_data_menampilkan_nol_dan_tidak_membuat_data_rekaan(): void
    {
        $response = $this->actingAs($this->buatKoordinator('kosong'), 'koordinator')
            ->get(route('koordinator.dashboard'));

        $response->assertOk();
        $response->assertViewHas('kelasCount', 0);
        $response->assertViewHas('totalKelompok', 0);
        $response->assertViewHas('mahasiswaCount', 0);
        $response->assertViewHas('milestoneChartData', []);
        $response->assertSee('Belum ada milestone global pada periode aktif.');
        $response->assertDontSee('Tim &amp; Topik', false);
    }

    private function buatKoordinator(string $suffix): Koordinator
    {
        return Koordinator::create([
            'nama' => 'Koordinator '.ucfirst($suffix), 'email' => 'koordinator-'.$suffix.'@test.com',
            'password' => 'password123', 'nidn' => $suffix === 'data' ? '900000001' : '900000002',
        ]);
    }
}
