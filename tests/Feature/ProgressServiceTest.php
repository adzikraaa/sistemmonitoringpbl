<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\MilestoneMataKuliah;
use App\Models\PengumpulanMilestone;
use App\Models\PenyelesaianMilestone;
use App\Models\Periode;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    private Periode $periode;

    private Kelompok $kelompok;

    private MataKuliah $mataKuliahA;

    private MataKuliah $mataKuliahB;

    private ProgressService $service;

    /** @var array<int, Milestone> */
    private array $milestones = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ProgressService::class);

        $this->periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $this->mataKuliahA = MataKuliah::create([
            'kode' => 'SIB301',
            'nama_matkul' => 'Pemrograman Web Lanjut',
            'is_aktif' => true,
        ]);

        $this->mataKuliahB = MataKuliah::create([
            'kode' => 'SIB302',
            'nama_matkul' => 'Manajemen Proyek Sistem Informasi',
            'is_aktif' => true,
        ]);

        MataKuliah::create([
            'kode' => 'SIB303',
            'nama_matkul' => 'Mata Kuliah Nonaktif',
            'is_aktif' => false,
        ]);

        $this->kelompok = $this->buatKelompok($this->periode);
        $this->milestones = $this->buatMilestones(9);
    }

    public function test_tanpa_mapping_menghasilkan_nol_dan_tanpa_data(): void
    {
        MilestoneMataKuliah::query()->delete();

        $hasil = $this->service->untukKelompok($this->kelompok);

        $this->assertSame(0.0, $hasil['persen']);
        $this->assertSame(0, $hasil['selesai']);
        $this->assertSame(0, $hasil['total']);
        $this->assertFalse($hasil['punya_data']);
    }

    public function test_mapping_ada_tetapi_belum_selesai_menghasilkan_nol_dengan_data(): void
    {
        $hasil = $this->service->untukKelompok($this->kelompok);

        $this->assertSame(0.0, $hasil['persen']);
        $this->assertSame(0, $hasil['selesai']);
        $this->assertSame(18, $hasil['total']);
        $this->assertTrue($hasil['punya_data']);
    }

    public function test_progres_keseluruhan_dan_per_matkul_dihitung_terpisah(): void
    {
        foreach (array_slice($this->milestones, 0, 3) as $milestone) {
            $this->selesaikan($milestone, $this->mataKuliahA, ' SELESAI ');
        }

        $keseluruhan = $this->service->untukKelompok($this->kelompok);
        $matkulA = $this->service->untukMataKuliah($this->kelompok, $this->mataKuliahA);
        $matkulB = $this->service->untukMataKuliah($this->kelompok, $this->mataKuliahB);

        $this->assertSame(16.67, $keseluruhan['persen']);
        $this->assertSame(3, $keseluruhan['selesai']);
        $this->assertSame(18, $keseluruhan['total']);

        $this->assertSame(33.33, $matkulA['persen']);
        $this->assertSame(3, $matkulA['selesai']);
        $this->assertSame(9, $matkulA['total']);

        $this->assertSame(0.0, $matkulB['persen']);
        $this->assertSame(0, $matkulB['selesai']);
        $this->assertSame(9, $matkulB['total']);
    }

    public function test_semua_mapping_selesai_menghasilkan_100(): void
    {
        foreach ($this->milestones as $milestone) {
            $this->selesaikan($milestone, $this->mataKuliahA);
            $this->selesaikan($milestone, $this->mataKuliahB);
        }

        $hasil = $this->service->untukKelompok($this->kelompok);

        $this->assertSame(100.0, $hasil['persen']);
        $this->assertSame(18, $hasil['selesai']);
        $this->assertSame(18, $hasil['total']);
    }

    public function test_status_selain_selesai_tidak_dihitung(): void
    {
        $this->selesaikan($this->milestones[0], $this->mataKuliahA, 'disetujui');
        $this->selesaikan($this->milestones[1], $this->mataKuliahA, 'berjalan');
        $this->selesaikan($this->milestones[2], $this->mataKuliahA, 'belum_dimulai');

        $hasil = $this->service->untukKelompok($this->kelompok);

        $this->assertSame(0.0, $hasil['persen']);
        $this->assertSame(0, $hasil['selesai']);
        $this->assertTrue($hasil['punya_data']);
    }

    public function test_status_pengumpulan_dokumen_tidak_dihitung_sebagai_penyelesaian(): void
    {
        PengumpulanMilestone::create([
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $this->milestones[0]->id,
            'mata_kuliah' => $this->mataKuliahA->slug,
            'file_name' => 'laporan.pdf',
            'file_path' => 'proposals/laporan.pdf',
            'status' => 'selesai',
        ]);

        $hasil = $this->service->untukKelompok($this->kelompok);

        $this->assertSame(0.0, $hasil['persen']);
        $this->assertSame(0, $hasil['selesai']);
        $this->assertSame(18, $hasil['total']);
        $this->assertTrue($hasil['punya_data']);
    }

    public function test_mapping_periode_lain_tidak_masuk_denominator(): void
    {
        $periodeLain = Periode::create([
            'nama_periode' => 'Genap 2025/2026',
            'tahun_akademik' => '2025/2026',
            'tanggal_mulai' => '2026-02-01',
            'tanggal_selesai' => '2026-07-31',
            'is_aktif' => false,
        ]);

        $this->buatMilestones(4, $periodeLain);

        $hasil = $this->service->untukKelompok($this->kelompok);

        $this->assertSame(18, $hasil['total']);
    }

    public function test_batch_tidak_menghasilkan_n_plus_1(): void
    {
        $kelompokLain = collect([
            $this->buatKelompok($this->periode),
            $this->buatKelompok($this->periode),
        ]);
        $semua = collect([$this->kelompok])->concat($kelompokLain);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $hasil = $this->service->untukDaftar($semua);

        $this->assertCount(3, $hasil);
        $this->assertLessThanOrEqual(2, count(DB::getQueryLog()));

        DB::disableQueryLog();
    }

    public function test_halaman_pilih_semester_memakai_progres_aktual(): void
    {
        $mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Pilih Semester',
            'email' => 'pilih.semester@test.com',
            'password' => 'password123',
            'nim' => '2026501',
            'kelas_id' => $this->kelompok->kelas_id,
            'is_aktif' => true,
        ]);
        $this->kelompok->mahasiswa()->attach($mahasiswa->id, [
            'role_label' => 'Ketua',
            'is_ketua' => true,
        ]);

        $this->selesaikan($this->milestones[0], $this->mataKuliahA);

        $this->actingAs($mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.pilih-semester'))
            ->assertOk()
            ->assertSee('Progres PBL')
            ->assertSee('5.56%')
            ->assertDontSee('65%');
    }

    public function test_dashboard_dan_kelompok_memakai_nilai_progres_yang_sama(): void
    {
        $mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Konsistensi',
            'email' => 'konsistensi@test.com',
            'password' => 'password123',
            'nim' => '2026502',
            'kelas_id' => $this->kelompok->kelas_id,
            'is_aktif' => true,
        ]);
        $this->kelompok->mahasiswa()->attach($mahasiswa->id, [
            'role_label' => 'Ketua',
            'is_ketua' => true,
        ]);

        $this->selesaikan($this->milestones[0], $this->mataKuliahA);

        $this->actingAs($mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSee('5.56%');

        $this->actingAs($mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.kelompok'))
            ->assertOk()
            ->assertSee('5.56%');
    }

    private function selesaikan(Milestone $milestone, MataKuliah $mataKuliah, string $status = 'selesai'): void
    {
        PenyelesaianMilestone::create([
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $milestone->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'status' => $status,
        ]);
    }

    private function buatKelompok(Periode $periode): Kelompok
    {
        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3B',
            'periode_id' => $periode->id,
        ]);

        $dosen = Dosen::create([
            'nama' => 'Dosen Progres',
            'email' => 'dosen.progres.'.uniqid().'@test.com',
            'password' => 'password123',
            'nidn' => '9'.substr(uniqid(), -8),
            'is_aktif' => true,
        ]);

        return Kelompok::create([
            'nama_kelompok' => 'Kelompok '.uniqid(),
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id,
            'periode_id' => $periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);
    }

    /**
     * @return array<int, Milestone>
     */
    private function buatMilestones(int $jumlah, ?Periode $periode = null): array
    {
        $periode ??= $this->periode;
        $milestones = [];

        for ($urutan = 1; $urutan <= $jumlah; $urutan++) {
            $milestones[] = Milestone::create([
                'periode_id' => $periode->id,
                'kode' => 'M'.str_pad((string) $urutan, 2, '0', STR_PAD_LEFT),
                'judul' => 'Milestone '.$urutan,
                'urutan' => $urutan,
            ]);
        }

        return $milestones;
    }
}
