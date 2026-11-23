<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\MilestoneMataKuliah;
use App\Models\PengumpulanMilestone;
use App\Models\PenyelesaianMilestone;
use App\Models\Periode;
use App\Models\RiwayatPenyelesaianMilestone;
use App\Services\PenyelesaianMilestoneService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenyelesaianMilestoneTest extends TestCase
{
    use RefreshDatabase;

    private Periode $periode;

    private Kelas $kelasA;

    private Kelas $kelasB;

    private Kelompok $kelompokA;

    private Dosen $dosenA;

    private Dosen $dosenB;

    private Dosen $dosenTanpaPengampu;

    private MataKuliah $mataKuliahA;

    private MataKuliah $mataKuliahB;

    private Milestone $milestone;

    private PenyelesaianMilestoneService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PenyelesaianMilestoneService::class);

        $this->periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $this->kelasA = Kelas::create([
            'nama_kelas' => 'SIB 3A',
            'periode_id' => $this->periode->id,
        ]);
        $this->kelasB = Kelas::create([
            'nama_kelas' => 'SIB 3B',
            'periode_id' => $this->periode->id,
        ]);

        $this->dosenA = Dosen::create([
            'nama' => 'Dosen A',
            'email' => 'dosen.a@test.com',
            'password' => 'password123',
            'nidn' => '9000000001',
            'is_aktif' => true,
        ]);
        $this->dosenB = Dosen::create([
            'nama' => 'Dosen B',
            'email' => 'dosen.b@test.com',
            'password' => 'password123',
            'nidn' => '9000000002',
            'is_aktif' => true,
        ]);
        $this->dosenTanpaPengampu = Dosen::create([
            'nama' => 'Dosen Tanpa Pengampu',
            'email' => 'dosen.c@test.com',
            'password' => 'password123',
            'nidn' => '9000000003',
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

        $this->milestone = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M01',
            'judul' => 'Milestone Pertama',
            'urutan' => 1,
        ]);

        $this->kelompokA = Kelompok::create([
            'nama_kelompok' => 'Kelompok A',
            'dosen_id' => $this->dosenA->id,
            'kelas_id' => $this->kelasA->id,
            'periode_id' => $this->periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);

        DosenPengampu::create([
            'dosen_id' => $this->dosenA->id,
            'mata_kuliah_id' => $this->mataKuliahA->id,
            'kelas_id' => $this->kelasA->id,
        ]);
        DosenPengampu::create([
            'dosen_id' => $this->dosenA->id,
            'mata_kuliah_id' => $this->mataKuliahB->id,
            'kelas_id' => $this->kelasA->id,
        ]);
        DosenPengampu::create([
            'dosen_id' => $this->dosenB->id,
            'mata_kuliah_id' => $this->mataKuliahA->id,
            'kelas_id' => $this->kelasB->id,
        ]);
    }

    public function test_membuat_penyelesaian_dan_riwayat_awal(): void
    {
        $penyelesaian = $this->service->ubahStatus(
            $this->kelompokA,
            $this->milestone,
            $this->mataKuliahA,
            $this->dosenA,
            'selesai',
            'Selesai sesuai bukti.'
        );

        $this->assertDatabaseHas('penyelesaian_milestone', [
            'id' => $penyelesaian->id,
            'kelompok_id' => $this->kelompokA->id,
            'milestone_id' => $this->milestone->id,
            'mata_kuliah_id' => $this->mataKuliahA->id,
            'status' => 'selesai',
            'diverifikasi_oleh' => $this->dosenA->id,
        ]);
        $this->assertNotNull($penyelesaian->diverifikasi_pada);

        $riwayat = RiwayatPenyelesaianMilestone::firstOrFail();

        $this->assertSame($this->dosenA->id, $riwayat->dosen_id);
        $this->assertNull($riwayat->status_lama);
        $this->assertSame('selesai', $riwayat->status_baru);
        $this->assertSame('Selesai sesuai bukti.', $riwayat->catatan);
    }

    public function test_perubahan_status_mencatat_riwayat(): void
    {
        $this->service->ubahStatus($this->kelompokA, $this->milestone, $this->mataKuliahA, $this->dosenA, 'berjalan');
        $this->service->ubahStatus($this->kelompokA, $this->milestone, $this->mataKuliahA, $this->dosenA, 'selesai');

        $this->assertDatabaseCount('penyelesaian_milestone', 1);
        $this->assertDatabaseCount('riwayat_penyelesaian_milestone', 2);
        $this->assertDatabaseHas('penyelesaian_milestone', ['status' => 'selesai']);
        $this->assertDatabaseHas('riwayat_penyelesaian_milestone', [
            'status_lama' => 'berjalan',
            'status_baru' => 'selesai',
            'dosen_id' => $this->dosenA->id,
        ]);
    }

    public function test_status_sama_tidak_membuat_riwayat_duplikat(): void
    {
        $this->service->ubahStatus($this->kelompokA, $this->milestone, $this->mataKuliahA, $this->dosenA, 'selesai');
        $this->service->ubahStatus($this->kelompokA, $this->milestone, $this->mataKuliahA, $this->dosenA, 'selesai');

        $this->assertDatabaseCount('penyelesaian_milestone', 1);
        $this->assertDatabaseCount('riwayat_penyelesaian_milestone', 1);
    }

    public function test_identitas_triple_unik_di_database(): void
    {
        $this->service->ubahStatus($this->kelompokA, $this->milestone, $this->mataKuliahA, $this->dosenA, 'selesai');

        $this->expectException(QueryException::class);

        PenyelesaianMilestone::create([
            'kelompok_id' => $this->kelompokA->id,
            'milestone_id' => $this->milestone->id,
            'mata_kuliah_id' => $this->mataKuliahA->id,
            'status' => 'berjalan',
        ]);
    }

    public function test_konflik_unique_ditangani_dengan_mengambil_baris_yang_menang(): void
    {
        $eventName = 'eloquent.creating: '.PenyelesaianMilestone::class;

        Event::listen($eventName, function () {
            DB::table('penyelesaian_milestone')->insert([
                'kelompok_id' => $this->kelompokA->id,
                'milestone_id' => $this->milestone->id,
                'mata_kuliah_id' => $this->mataKuliahA->id,
                'status' => 'berjalan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            throw new UniqueConstraintViolationException(
                'sqlite',
                'insert into penyelesaian_milestone ...',
                [],
                new \RuntimeException('UNIQUE constraint failed')
            );
        });

        try {
            $penyelesaian = $this->service->ubahStatus(
                $this->kelompokA,
                $this->milestone,
                $this->mataKuliahA,
                $this->dosenA,
                'selesai'
            );
        } finally {
            Event::forget($eventName);
        }

        $this->assertSame('selesai', $penyelesaian->status);
        $this->assertDatabaseCount('penyelesaian_milestone', 1);
        $this->assertDatabaseCount('riwayat_penyelesaian_milestone', 1);
        $this->assertDatabaseHas('riwayat_penyelesaian_milestone', [
            'status_lama' => 'berjalan',
            'status_baru' => 'selesai',
            'dosen_id' => $this->dosenA->id,
        ]);
    }

    public function test_dosen_tanpa_penugasan_tidak_dapat_mengubah_status(): void
    {
        $this->expectException(AuthorizationException::class);

        $this->service->ubahStatus(
            $this->kelompokA,
            $this->milestone,
            $this->mataKuliahA,
            $this->dosenTanpaPengampu,
            'selesai'
        );
    }

    public function test_dosen_dengan_kelas_berbeda_tidak_dapat_mengubah_status(): void
    {
        $this->expectException(AuthorizationException::class);

        $this->service->ubahStatus(
            $this->kelompokA,
            $this->milestone,
            $this->mataKuliahA,
            $this->dosenB,
            'selesai'
        );
    }

    public function test_milestone_periode_lain_ditolak(): void
    {
        $periodeLain = Periode::create([
            'nama_periode' => 'Genap 2025/2026',
            'tahun_akademik' => '2025/2026',
            'tanggal_mulai' => '2026-02-01',
            'tanggal_selesai' => '2026-07-31',
            'is_aktif' => false,
        ]);
        $milestoneLain = Milestone::create([
            'periode_id' => $periodeLain->id,
            'kode' => 'M02',
            'judul' => 'Milestone Lain',
            'urutan' => 1,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->ubahStatus(
            $this->kelompokA,
            $milestoneLain,
            $this->mataKuliahA,
            $this->dosenA,
            'selesai'
        );
    }

    public function test_milestone_tanpa_mapping_ditolak(): void
    {
        $milestoneTanpaMapping = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M03',
            'judul' => 'Tanpa Mapping',
            'urutan' => 3,
        ]);

        MilestoneMataKuliah::where('milestone_id', $milestoneTanpaMapping->id)->delete();

        $this->expectException(\InvalidArgumentException::class);

        $this->service->ubahStatus(
            $this->kelompokA,
            $milestoneTanpaMapping,
            $this->mataKuliahA,
            $this->dosenA,
            'selesai'
        );
    }

    public function test_route_dosen_pengampu_dapat_mengubah_status(): void
    {
        $this->actingAs($this->dosenA, 'dosen')
            ->patch(route('dosen.penyelesaian-milestone.update', [
                $this->kelompokA,
                $this->milestone,
                $this->mataKuliahA,
            ]), [
                'status' => 'selesai',
                'catatan' => 'Lewat route.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('penyelesaian_milestone', [
            'kelompok_id' => $this->kelompokA->id,
            'milestone_id' => $this->milestone->id,
            'mata_kuliah_id' => $this->mataKuliahA->id,
            'status' => 'selesai',
        ]);
    }

    public function test_route_dosen_tanpa_penugasan_ditolak(): void
    {
        $this->actingAs($this->dosenB, 'dosen')
            ->patch(route('dosen.penyelesaian-milestone.update', [
                $this->kelompokA,
                $this->milestone,
                $this->mataKuliahA,
            ]), ['status' => 'selesai'])
            ->assertForbidden();

        $this->assertDatabaseCount('penyelesaian_milestone', 0);
    }

    public function test_halaman_index_menampilkan_laporan_mahasiswa_sesuai_mata_kuliah(): void
    {
        PengumpulanMilestone::create([
            'milestone_id' => $this->milestone->id,
            'kelompok_id' => $this->kelompokA->id,
            'mata_kuliah' => $this->mataKuliahA->slug,
            'file_name' => 'laporan_pwl.pdf',
            'file_path' => 'milestone-submissions/test/laporan_pwl.pdf',
            'catatan' => 'Catatan PWL mahasiswa',
            'status' => 'dikumpulkan',
        ]);

        $response = $this->actingAs($this->dosenA, 'dosen')
            ->get(route('dosen.penyelesaian-milestone.index'));

        $response->assertOk();
        $response->assertSee('laporan_pwl.pdf');
        $response->assertSee('Catatan PWL mahasiswa');
        $response->assertSee(route('dosen.penyelesaian-milestone.download', [
            $this->kelompokA,
            $this->milestone,
            $this->mataKuliahA,
        ]));
    }

    public function test_dosen_dapat_mengunduh_laporan_milestone_milik_kelas_dan_matkul_yang_diampu(): void
    {
        Storage::fake('local');
        $filePath = 'milestone-submissions/test/laporan_pwl.pdf';
        Storage::disk('local')->put($filePath, 'konten file laporan');

        PengumpulanMilestone::create([
            'milestone_id' => $this->milestone->id,
            'kelompok_id' => $this->kelompokA->id,
            'mata_kuliah' => $this->mataKuliahA->slug,
            'file_name' => 'laporan_asli.pdf',
            'file_path' => $filePath,
            'status' => 'dikumpulkan',
        ]);

        $response = $this->actingAs($this->dosenA, 'dosen')
            ->get(route('dosen.penyelesaian-milestone.download', [
                $this->kelompokA,
                $this->milestone,
                $this->mataKuliahA,
            ]));

        $response->assertOk();
        $response->assertDownload('laporan_asli.pdf');
    }

    public function test_dosen_ditolak_mengunduh_laporan_jika_bukan_pengampu_matkul_atau_kelas(): void
    {
        Storage::fake('local');
        $filePath = 'milestone-submissions/test/laporan_pwl.pdf';
        Storage::disk('local')->put($filePath, 'konten file');

        PengumpulanMilestone::create([
            'milestone_id' => $this->milestone->id,
            'kelompok_id' => $this->kelompokA->id,
            'mata_kuliah' => $this->mataKuliahA->slug,
            'file_name' => 'laporan_asli.pdf',
            'file_path' => $filePath,
            'status' => 'dikumpulkan',
        ]);

        // dosenB does not teach kelompokA's class for mataKuliahA
        $response = $this->actingAs($this->dosenB, 'dosen')
            ->get(route('dosen.penyelesaian-milestone.download', [
                $this->kelompokA,
                $this->milestone,
                $this->mataKuliahA,
            ]));

        $response->assertForbidden();
    }

    public function test_unduh_laporan_404_jika_file_tidak_ditemukan(): void
    {
        Storage::fake('local');

        PengumpulanMilestone::create([
            'milestone_id' => $this->milestone->id,
            'kelompok_id' => $this->kelompokA->id,
            'mata_kuliah' => $this->mataKuliahA->slug,
            'file_name' => 'laporan_hilang.pdf',
            'file_path' => 'milestone-submissions/test/laporan_hilang.pdf',
            'status' => 'dikumpulkan',
        ]);

        $response = $this->actingAs($this->dosenA, 'dosen')
            ->get(route('dosen.penyelesaian-milestone.download', [
                $this->kelompokA,
                $this->milestone,
                $this->mataKuliahA,
            ]));

        $response->assertNotFound();
    }
}
