<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\MilestoneMataKuliah;
use App\Models\PengumpulanMilestone;
use App\Models\PenyelesaianMilestone;
use App\Models\Periode;
use App\Models\Proposal;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaMilestoneUploadTest extends TestCase
{
    use RefreshDatabase;

    private Periode $periode;

    private Kelas $kelas;

    private Mahasiswa $mahasiswa;

    private Kelompok $kelompok;

    private Milestone $milestone;

    private MataKuliah $mataKuliah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => 'SIB 3B',
            'periode_id' => $this->periode->id,
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Upload',
            'email' => 'upload@test.com',
            'password' => 'password123',
            'nim' => '2026001',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->kelompok = $this->buatKelompok('Kelompok Upload', $this->kelas);
        $this->lampirkan($this->kelompok, $this->mahasiswa);
        $this->milestone = $this->buatMilestone($this->periode, 1, 'M01');
        $this->mataKuliah = $this->buatMataKuliah('pemrograman-web-lanjut', 'Pemrograman Web Lanjut', 'SIB301');
    }

    public function test_upload_menyimpan_submission_dan_file_tanpa_menandai_milestone_selesai(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Laporan implementasi modul utama.',
                'berkas' => UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf'),
                'link_bukti' => 'https://github.com/contoh/proyek',
            ]);

        $response->assertRedirect(route('mahasiswa.milestone.detail', 'pemrograman-web-lanjut'));

        $submission = PengumpulanMilestone::firstOrFail();

        $this->assertSame($this->kelompok->id, $submission->kelompok_id);
        $this->assertSame($this->milestone->id, $submission->milestone_id);
        $this->assertSame($this->mahasiswa->id, $submission->diunggah_oleh);
        $this->assertSame('pemrograman-web-lanjut', $submission->mata_kuliah);
        $this->assertSame('diajukan', $submission->status);
        $this->assertSame('laporan.pdf', $submission->file_name);
        Storage::disk('local')->assertExists($submission->file_path);

        $this->assertDatabaseCount('milestone_kelompok', 0);
    }

    public function test_upload_milestone_mengirim_notifikasi_ke_dosen_terkait_saja(): void
    {
        Storage::fake('local');
        $dosenPengampu = Dosen::create([
            'nama' => 'Dosen Pengampu PWL',
            'email' => 'pengampu-pwl@test.com',
            'password' => 'password123',
            'nidn' => '700000010',
            'is_aktif' => true,
        ]);
        $dosenTidakTerkait = Dosen::create([
            'nama' => 'Dosen Tidak Terkait',
            'email' => 'tidak-terkait@test.com',
            'password' => 'password123',
            'nidn' => '700000011',
            'is_aktif' => true,
        ]);
        DosenPengampu::create([
            'dosen_id' => $dosenPengampu->id,
            'mata_kuliah_id' => $this->mataKuliah->id,
            'kelas_id' => $this->kelas->id,
        ]);

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', $this->mataKuliah->slug), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Laporan implementasi modul utama.',
                'berkas' => UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf'),
            ]);

        $response->assertRedirect(route('mahasiswa.milestone.detail', $this->mataKuliah->slug));
        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => $this->kelompok->dosen->getMorphClass(),
            'penerima_id' => $this->kelompok->dosen_id,
            'judul' => 'Laporan Milestone Dikumpulkan',
            'dibaca_pada' => null,
        ]);
        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => $dosenPengampu->getMorphClass(),
            'penerima_id' => $dosenPengampu->id,
            'judul' => 'Laporan Milestone Dikumpulkan',
            'dibaca_pada' => null,
        ]);
        $this->assertDatabaseMissing('notifikasi', [
            'penerima_type' => $dosenTidakTerkait->getMorphClass(),
            'penerima_id' => $dosenTidakTerkait->id,
        ]);
    }

    public function test_mata_kuliah_diambil_dari_route_bukan_dari_request(): void
    {
        Storage::fake('local');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'mata_kuliah' => 'penjaminan-mutu-perangkat-lunak',
                'catatan' => 'Catatan milestone.',
                'berkas' => UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pengumpulan_milestone', [
            'kelompok_id' => $this->kelompok->id,
            'mata_kuliah' => 'pemrograman-web-lanjut',
        ]);
    }

    public function test_milestone_dari_periode_lain_ditolak(): void
    {
        Storage::fake('local');

        $periodeLain = Periode::create([
            'nama_periode' => 'Genap 2025/2026',
            'tahun_akademik' => '2025/2026',
            'tanggal_mulai' => '2026-02-01',
            'tanggal_selesai' => '2026-07-31',
            'is_aktif' => false,
        ]);

        $milestoneLain = $this->buatMilestone($periodeLain, 1, 'M01');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $milestoneLain->id,
                'catatan' => 'Catatan milestone.',
                'berkas' => UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('milestone_id');

        $this->assertDatabaseCount('pengumpulan_milestone', 0);
    }

    public function test_mahasiswa_tanpa_kelompok_tidak_bisa_upload(): void
    {
        Storage::fake('local');

        $tanpaKelompok = Mahasiswa::create([
            'nama' => 'Mahasiswa Tanpa Kelompok',
            'email' => 'tanpa@test.com',
            'password' => 'password123',
            'nim' => '2026999',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->actingAs($tanpaKelompok, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Catatan milestone.',
                'berkas' => UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('berkas');

        $this->assertDatabaseCount('pengumpulan_milestone', 0);
        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    public function test_jenis_berkas_tidak_valid_ditolak(): void
    {
        Storage::fake('local');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Catatan milestone.',
                'berkas' => UploadedFile::fake()->create('catatan.txt', 10, 'text/plain'),
            ])
            ->assertSessionHasErrors('berkas');

        $this->assertDatabaseCount('pengumpulan_milestone', 0);
    }

    public function test_upload_ulang_memperbarui_submission_dan_menghapus_file_lama(): void
    {
        Storage::fake('local');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Versi pertama.',
                'berkas' => UploadedFile::fake()->create('laporan-v1.pdf', 100, 'application/pdf'),
            ]);

        $submission = PengumpulanMilestone::firstOrFail();
        $oldPath = $submission->file_path;

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Versi kedua.',
                'berkas' => UploadedFile::fake()->create('laporan-v2.pdf', 100, 'application/pdf'),
            ]);

        $this->assertDatabaseCount('pengumpulan_milestone', 1);

        $submission->refresh();

        $this->assertSame('laporan-v2.pdf', $submission->file_name);
        $this->assertNotSame($oldPath, $submission->file_path);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($submission->file_path);
    }

    public function test_milestone_yang_sudah_disetujui_tidak_bisa_diunggah_ulang(): void
    {
        Storage::fake('local');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Versi disetujui.',
                'berkas' => UploadedFile::fake()->create('laporan-disetujui.pdf', 100, 'application/pdf'),
            ]);

        $submission = PengumpulanMilestone::firstOrFail();
        $approvedPath = $submission->file_path;
        $submission->update(['status' => 'disetujui']);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Percobaan timpa setelah disetujui.',
                'berkas' => UploadedFile::fake()->create('laporan-baru.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('milestone_id');

        $submission->refresh();
        $this->assertSame('laporan-disetujui.pdf', $submission->file_name);
        $this->assertSame('disetujui', $submission->status);
        Storage::disk('local')->assertExists($approvedPath);
        $this->assertCount(1, Storage::disk('local')->allFiles());
    }

    public function test_pemilik_bisa_mengunduh_dan_kelompok_lain_tidak_bisa(): void
    {
        Storage::fake('local');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Catatan milestone.',
                'berkas' => UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf'),
            ]);

        $submission = PengumpulanMilestone::firstOrFail();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.berkas.download', $submission->id))
            ->assertOk();

        $mahasiswaLain = Mahasiswa::create([
            'nama' => 'Mahasiswa Lain',
            'email' => 'lain@test.com',
            'password' => 'password123',
            'nim' => '2026002',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $kelompokLain = $this->buatKelompok('Kelompok Lain', $this->kelas);
        $this->lampirkan($kelompokLain, $mahasiswaLain);

        $this->actingAs($mahasiswaLain, 'mahasiswa')
            ->get(route('mahasiswa.milestone.berkas.download', $submission->id))
            ->assertNotFound();
    }

    public function test_halaman_detail_menampilkan_submission_setelah_reload(): void
    {
        Storage::fake('local');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Ringkasan tersimpan di database.',
                'berkas' => UploadedFile::fake()->create('laporan-final.pdf', 100, 'application/pdf'),
            ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', 'pemrograman-web-lanjut'))
            ->assertOk()
            ->assertSee('laporan-final.pdf')
            ->assertSee('Ringkasan tersimpan di database.')
            ->assertSee('Menunggu review')
            ->assertSee('0/1 selesai, 1 terkumpul')
            ->assertDontSee('Belum dimulai')
            ->assertSee('Menunggu Review');
    }

    public function test_judul_proyek_di_header_milestone_mengambil_judul_proposal_kelompok(): void
    {
        Proposal::create([
            'kelompok_id' => $this->kelompok->id,
            'judul' => 'Sistem Informasi Monitoring PBL Polinema',
            'deskripsi' => 'Deskripsi proposal.',
            'latar_belakang' => 'Latar belakang.',
            'tujuan' => 'Tujuan proyek.',
            'metode' => 'Scrum.',
            'status' => 'diajukan',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', 'pemrograman-web-lanjut'))
            ->assertOk()
            ->assertSee('Sistem Informasi Monitoring PBL Polinema')
            ->assertDontSee('Belum ada proyek');
    }

    public function test_status_perlu_revisi_dan_berjalan_serta_selesai_pada_detail_dan_daftar(): void
    {
        Storage::fake('local');

        $milestone2 = $this->buatMilestone($this->periode, 2, 'M02');

        // 1. Upload milestone 1 lalu set ke status revisi
        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', $this->mataKuliah->slug), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Draf awal milestone 1.',
                'berkas' => UploadedFile::fake()->create('m1-v1.pdf', 100, 'application/pdf'),
            ]);

        $sub1 = PengumpulanMilestone::where('milestone_id', $this->milestone->id)->firstOrFail();
        $sub1->update(['status' => 'revisi']);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', $this->mataKuliah->slug))
            ->assertOk()
            ->assertSee('Perlu revisi')
            ->assertSee('0/2 selesai, 1 terkumpul');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone'))
            ->assertOk()
            ->assertSee('Perlu revisi')
            ->assertSee('0/2 selesai, 1 terkumpul');

        // 2. Milestone 1 diverifikasi selesai oleh dosen, lalu upload milestone 2 -> Berjalan (1/2 selesai, 2 terkumpul)
        $sub1->update(['status' => 'disetujui']);
        PenyelesaianMilestone::create([
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $this->milestone->id,
            'mata_kuliah_id' => $this->mataKuliah->id,
            'status' => PenyelesaianMilestone::STATUS_SELESAI,
        ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', $this->mataKuliah->slug), [
                'milestone_id' => $milestone2->id,
                'catatan' => 'Submission milestone 2.',
                'berkas' => UploadedFile::fake()->create('m2.pdf', 100, 'application/pdf'),
            ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', $this->mataKuliah->slug))
            ->assertOk()
            ->assertSee('Berjalan')
            ->assertSee('1/2 selesai, 2 terkumpul');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone'))
            ->assertOk()
            ->assertSee('Berjalan')
            ->assertSee('50%')
            ->assertSee('1/2 selesai, 2 terkumpul');

        // 3. Milestone 2 juga diverifikasi selesai -> Selesai (2/2 selesai)
        PengumpulanMilestone::where('milestone_id', $milestone2->id)->firstOrFail()->update(['status' => 'disetujui']);
        PenyelesaianMilestone::create([
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $milestone2->id,
            'mata_kuliah_id' => $this->mataKuliah->id,
            'status' => PenyelesaianMilestone::STATUS_SELESAI,
        ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', $this->mataKuliah->slug))
            ->assertOk()
            ->assertSee('Selesai')
            ->assertSee('2/2 selesai')
            ->assertDontSee('2 terkumpul');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone'))
            ->assertOk()
            ->assertSee('Selesai')
            ->assertSee('100%')
            ->assertSee('2/2 selesai');
    }

    public function test_satu_milestone_pada_dua_matkul_menghasilkan_dua_submission(): void
    {
        Storage::fake('local');

        $mpsi = $this->buatMataKuliah(
            'manajemen-proyek-sistem-informasi',
            'Manajemen Proyek Sistem Informasi',
            'SIB302'
        );

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Submission PWL.',
                'berkas' => UploadedFile::fake()->create('pwl.pdf', 100, 'application/pdf'),
            ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'manajemen-proyek-sistem-informasi'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Submission MPSI.',
                'berkas' => UploadedFile::fake()->create('mpsi.pdf', 100, 'application/pdf'),
            ]);

        $this->assertDatabaseCount('pengumpulan_milestone', 2);

        $pwl = PengumpulanMilestone::where('mata_kuliah', 'pemrograman-web-lanjut')->firstOrFail();
        $mpsiSubmission = PengumpulanMilestone::where('mata_kuliah', 'manajemen-proyek-sistem-informasi')->firstOrFail();

        $this->assertSame('pwl.pdf', $pwl->file_name);
        $this->assertSame('mpsi.pdf', $mpsiSubmission->file_name);
        $this->assertNotSame($pwl->file_path, $mpsiSubmission->file_path);
        Storage::disk('local')->assertExists($pwl->file_path);
        Storage::disk('local')->assertExists($mpsiSubmission->file_path);
    }

    public function test_reupload_pada_pwl_tidak_mengubah_submission_mpsi(): void
    {
        Storage::fake('local');

        $mpsi = $this->buatMataKuliah(
            'manajemen-proyek-sistem-informasi',
            'Manajemen Proyek Sistem Informasi',
            'SIB302'
        );

        foreach (
            [
                ['pemrograman-web-lanjut', 'pwl-v1.pdf'],
                ['manajemen-proyek-sistem-informasi', 'mpsi.pdf'],
            ] as [$slug, $fileName]
        ) {
            $this->actingAs($this->mahasiswa, 'mahasiswa')
                ->post(route('mahasiswa.milestone.upload', $slug), [
                    'milestone_id' => $this->milestone->id,
                    'catatan' => 'Catatan ' . $slug,
                    'berkas' => UploadedFile::fake()->create($fileName, 100, 'application/pdf'),
                ]);
        }

        $mpsiSubmission = PengumpulanMilestone::where('mata_kuliah', 'manajemen-proyek-sistem-informasi')
            ->firstOrFail();
        $mpsiPath = $mpsiSubmission->file_path;

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Catatan PWL versi dua.',
                'berkas' => UploadedFile::fake()->create('pwl-v2.pdf', 100, 'application/pdf'),
            ]);

        $this->assertDatabaseCount('pengumpulan_milestone', 2);

        $mpsiSubmission->refresh();

        $this->assertSame('mpsi.pdf', $mpsiSubmission->file_name);
        $this->assertSame($mpsiPath, $mpsiSubmission->file_path);
        $this->assertSame('Catatan manajemen-proyek-sistem-informasi', $mpsiSubmission->catatan);
        Storage::disk('local')->assertExists($mpsiPath);

        $pwl = PengumpulanMilestone::where('mata_kuliah', 'pemrograman-web-lanjut')->firstOrFail();
        $this->assertSame('pwl-v2.pdf', $pwl->file_name);
    }

    public function test_file_lama_hanya_terhapus_untuk_kombinasi_yang_sama(): void
    {
        Storage::fake('local');

        $mpsi = $this->buatMataKuliah(
            'manajemen-proyek-sistem-informasi',
            'Manajemen Proyek Sistem Informasi',
            'SIB302'
        );

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'PWL awal.',
                'berkas' => UploadedFile::fake()->create('pwl-lama.pdf', 100, 'application/pdf'),
            ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'manajemen-proyek-sistem-informasi'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'MPSI tetap.',
                'berkas' => UploadedFile::fake()->create('mpsi-tetap.pdf', 100, 'application/pdf'),
            ]);

        $pwl = PengumpulanMilestone::where('mata_kuliah', 'pemrograman-web-lanjut')->firstOrFail();
        $mpsiSubmission = PengumpulanMilestone::where('mata_kuliah', 'manajemen-proyek-sistem-informasi')->firstOrFail();
        $pwlOldPath = $pwl->file_path;
        $mpsiPath = $mpsiSubmission->file_path;

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'PWL baru.',
                'berkas' => UploadedFile::fake()->create('pwl-baru.pdf', 100, 'application/pdf'),
            ]);

        $pwl->refresh();

        Storage::disk('local')->assertMissing($pwlOldPath);
        Storage::disk('local')->assertExists($pwl->file_path);
        Storage::disk('local')->assertExists($mpsiPath);
    }

    public function test_milestone_yang_tidak_terdaftar_pada_pivot_ditolak(): void
    {
        Storage::fake('local');

        $mpsi = $this->buatMataKuliah(
            'manajemen-proyek-sistem-informasi',
            'Manajemen Proyek Sistem Informasi',
            'SIB302'
        );

        MilestoneMataKuliah::where('mata_kuliah_id', $mpsi->id)->delete();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'manajemen-proyek-sistem-informasi'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Tidak boleh tersimpan.',
                'berkas' => UploadedFile::fake()->create('mpsi.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('milestone_id');

        $this->assertDatabaseCount('pengumpulan_milestone', 0);
        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    public function test_slug_mata_kuliah_tidak_dikenal_ditolak(): void
    {
        Storage::fake('local');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'slug-tidak-dikenal'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Tidak boleh tersimpan.',
                'berkas' => UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf'),
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('pengumpulan_milestone', 0);
    }

    public function test_mahasiswa_tanpa_kelompok_tidak_bisa_mengunduh(): void
    {
        Storage::fake('local');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Catatan milestone.',
                'berkas' => UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf'),
            ]);

        $submission = PengumpulanMilestone::firstOrFail();

        $tanpaKelompok = Mahasiswa::create([
            'nama' => 'Tanpa Kelompok',
            'email' => 'tanpa.unduh@test.com',
            'password' => 'password123',
            'nim' => '2026003',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->actingAs($tanpaKelompok, 'mahasiswa')
            ->get(route('mahasiswa.milestone.berkas.download', $submission->id))
            ->assertNotFound();
    }

    public function test_duplicate_key_dengan_triple_ada_menghasilkan_validation_response(): void
    {
        Storage::fake('local');

        $existingPath = 'milestone-submissions/existing.pdf';
        Storage::disk('local')->put($existingPath, 'existing');

        DB::table('pengumpulan_milestone')->insert([
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $this->milestone->id,
            'mata_kuliah' => $this->mataKuliah->slug,
            'diunggah_oleh' => $this->mahasiswa->id,
            'file_name' => 'existing.pdf',
            'file_path' => $existingPath,
            'link_bukti' => null,
            'catatan' => 'Submission yang sudah ada.',
            'status' => 'diajukan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $eventName = 'eloquent.updating: ' . PengumpulanMilestone::class;

        Event::listen($eventName, function () {
            throw new UniqueConstraintViolationException(
                'sqlite',
                'update pengumpulan_milestone ...',
                [],
                new \RuntimeException('UNIQUE constraint failed')
            );
        });

        try {
            $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
                ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                    'milestone_id' => $this->milestone->id,
                    'catatan' => 'Request gagal.',
                    'berkas' => UploadedFile::fake()->create('gagal.pdf', 100, 'application/pdf'),
                ]);
        } finally {
            Event::forget($eventName);
        }

        $response->assertRedirect(route('mahasiswa.milestone.detail', 'pemrograman-web-lanjut'));
        $response->assertSessionHasErrors('berkas');

        $this->assertDatabaseCount('pengumpulan_milestone', 1);
        $this->assertDatabaseHas('pengumpulan_milestone', [
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $this->milestone->id,
            'mata_kuliah' => $this->mataKuliah->slug,
            'file_path' => $existingPath,
        ]);

        Storage::disk('local')->assertExists($existingPath);
        $this->assertCount(1, Storage::disk('local')->allFiles());
    }

    public function test_unique_violation_tanpa_triple_tidak_disamarkan(): void
    {
        Storage::fake('local');
        $this->withoutExceptionHandling();

        $eventName = 'eloquent.creating: ' . PengumpulanMilestone::class;
        $thrown = null;

        Event::listen($eventName, function () {
            throw new UniqueConstraintViolationException(
                'sqlite',
                'insert into pengumpulan_milestone ...',
                [],
                new \RuntimeException('UNIQUE constraint failed')
            );
        });

        try {
            $this->actingAs($this->mahasiswa, 'mahasiswa')
                ->post(route('mahasiswa.milestone.upload', 'pemrograman-web-lanjut'), [
                    'milestone_id' => $this->milestone->id,
                    'catatan' => 'Tidak ada triple.',
                    'berkas' => UploadedFile::fake()->create('gagal.pdf', 100, 'application/pdf'),
                ]);
        } catch (UniqueConstraintViolationException $exception) {
            $thrown = $exception;
        } finally {
            Event::forget($eventName);
        }

        $this->assertInstanceOf(UniqueConstraintViolationException::class, $thrown);
        $this->assertDatabaseCount('pengumpulan_milestone', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles());
    }

    public function test_modal_menampilkan_keterangan_saat_tahapan_milestone_belum_tersedia(): void
    {
        MilestoneMataKuliah::where('mata_kuliah_id', $this->mataKuliah->id)->delete();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', $this->mataKuliah->slug))
            ->assertOk()
            ->assertSee('Tahapan Milestone Belum Tersedia')
            ->assertSee('Tahapan milestone untuk mata kuliah ini belum tersedia pada periode berjalan.');
    }

    public function test_modal_menampilkan_keterangan_saat_mahasiswa_belum_tergabung_kelompok(): void
    {
        $mahasiswaTanpaKelompok = Mahasiswa::create([
            'nama' => 'Mahasiswa Belum Kelompok',
            'email' => 'belum.kelompok@test.com',
            'password' => 'password123',
            'nim' => '2026888',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->actingAs($mahasiswaTanpaKelompok, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', $this->mataKuliah->slug))
            ->assertOk()
            ->assertSee('Belum Tergabung dalam Kelompok')
            ->assertSee('Akun Anda belum tergabung dalam kelompok pada periode aktif');
    }

    public function test_validasi_gagal_menampilkan_pesan_error_dan_membuka_kembali_modal_dengan_isian_lama(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->from(route('mahasiswa.milestone.detail', $this->mataKuliah->slug))
            ->followingRedirects()
            ->post(route('mahasiswa.milestone.upload', $this->mataKuliah->slug), [
                'milestone_id' => '',
                'catatan' => 'Ringkasan draf mahasiswa yang tetap tersimpan.',
                'link_bukti' => 'tautan-tidak-valid',
            ]);

        $response->assertOk()
            ->assertSee('Tahapan milestone wajib dipilih.')
            ->assertSee('Berkas laporan (PDF/ZIP) wajib diunggah.')
            ->assertSee('Format tautan repositori/demo tidak valid')
            ->assertSee('Ringkasan draf mahasiswa yang tetap tersimpan.')
            ->assertSee('openModalForm();', false);
    }

    public function test_ukuran_berkas_melebihi_dua_mb_ditolak_dengan_pesan_spesifik(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->from(route('mahasiswa.milestone.detail', $this->mataKuliah->slug))
            ->followingRedirects()
            ->post(route('mahasiswa.milestone.upload', $this->mataKuliah->slug), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Laporan ukuran besar.',
                'berkas' => UploadedFile::fake()->create('laporan-besar.pdf', 2500, 'application/pdf'),
            ]);

        $response->assertOk()
            ->assertSee('melebihi batas maksimal 2 MB')
            ->assertSee('openModalForm();', false);

        $this->assertDatabaseCount('pengumpulan_milestone', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles());
    }

    public function test_format_berkas_tidak_sesuai_menampilkan_pesan_spesifik_di_modal(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->from(route('mahasiswa.milestone.detail', $this->mataKuliah->slug))
            ->followingRedirects()
            ->post(route('mahasiswa.milestone.upload', $this->mataKuliah->slug), [
                'milestone_id' => $this->milestone->id,
                'catatan' => 'Laporan format salah.',
                'berkas' => UploadedFile::fake()->create('laporan.docx', 150, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            ]);

        $response->assertOk()
            ->assertSee('Format berkas harus berupa PDF atau ZIP.')
            ->assertSee('openModalForm();', false);

        $this->assertDatabaseCount('pengumpulan_milestone', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles());
    }

    private function buatMataKuliah(string $slug, string $nama, string $kode): MataKuliah
    {
        return MataKuliah::create([
            'kode' => $kode,
            'nama_matkul' => $nama,
            'slug' => $slug,
            'is_aktif' => true,
        ]);
    }

    private function buatKelompok(string $nama, Kelas $kelas): Kelompok
    {
        $dosen = Dosen::create([
            'nama' => 'Dosen ' . $nama,
            'email' => strtolower(str_replace(' ', '.', $nama)) . '.' . uniqid() . '@test.com',
            'password' => 'password123',
            'nidn' => '7' . substr(uniqid(), -8),
            'is_aktif' => true,
        ]);

        return Kelompok::create([
            'nama_kelompok' => $nama,
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id,
            'periode_id' => $kelas->periode_id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);
    }

    private function lampirkan(Kelompok $kelompok, Mahasiswa $mahasiswa, bool $ketua = true): void
    {
        $kelompok->mahasiswa()->attach($mahasiswa->id, [
            'role_label' => $ketua ? 'Ketua' : 'Anggota',
            'is_ketua' => $ketua,
        ]);
    }

    private function buatMilestone(Periode $periode, int $urutan, string $kode): Milestone
    {
        return Milestone::create([
            'periode_id' => $periode->id,
            'kode' => $kode,
            'judul' => 'Milestone ' . $urutan,
            'urutan' => $urutan,
        ]);
    }
}
