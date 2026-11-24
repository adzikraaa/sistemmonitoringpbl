<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\MilestoneMataKuliah;
use App\Models\PengumpulanMilestone;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DosenMilestoneReviewTest extends TestCase
{
    use RefreshDatabase;

    private function createSetup(): array
    {
        Storage::fake('local');

        $dosen = Dosen::create([
            'nama' => 'Dian Hanifudin Subhi',
            'email' => 'dian.dosen@example.com',
            'password' => 'secret123',
            'nidn' => '000111222',
            'is_aktif' => true,
        ]);

        $periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-02-01',
            'is_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3A',
            'angkatan' => 2024,
            'is_aktif' => true,
            'periode_id' => $periode->id,
        ]);

        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok 01',
            'judul_proyek' => 'Pengembangan Sistem Magang',
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id,
            'periode_id' => $periode->id,
            'status' => 'Aktif',
            'tanggal_dibuat' => '2026-09-01',
        ]);

        $mhs1 = Mahasiswa::create([
            'nama' => 'Muhammad Raihan',
            'email' => 'raihan@student.polinema.ac.id',
            'password' => 'secret123',
            'nim' => '2141720012',
            'kelas_id' => $kelas->id,
            'is_aktif' => true,
        ]);

        AnggotaKelompok::create([
            'kelompok_id' => $kelompok->id,
            'mahasiswa_id' => $mhs1->id,
            'role_label' => 'Ketua',
            'is_ketua' => true,
        ]);

        $milestone = Milestone::create([
            'periode_id' => $periode->id,
            'kode' => 'M1',
            'judul' => 'Milestone 1 : Laporan Progress Minggu 1 - 4',
            'deskripsi' => 'Pengumpulan sprint 1-4',
            'urutan' => 1,
            'deadline' => '2026-10-25',
        ]);

        $mk1 = MataKuliah::firstOrCreate(
            ['slug' => 'pemrograman-web-lanjut'],
            ['nama_matkul' => 'Pemrograman Web Lanjut', 'kode' => 'PWL', 'is_aktif' => true]
        );
        $mk2 = MataKuliah::firstOrCreate(
            ['slug' => 'manajemen-proyek-sistem-informasi'],
            ['nama_matkul' => 'Manajemen Proyek Sistem Informasi', 'kode' => 'MPSI', 'is_aktif' => true]
        );
        $mk3 = MataKuliah::firstOrCreate(
            ['slug' => 'penjaminan-mutu-perangkat-lunak'],
            ['nama_matkul' => 'Penjaminan Mutu Perangkat Lunak', 'kode' => 'PMPL', 'is_aktif' => true]
        );

        foreach ([$mk1, $mk2, $mk3] as $mk) {
            MilestoneMataKuliah::firstOrCreate([
                'milestone_id' => $milestone->id,
                'mata_kuliah_id' => $mk->id,
            ]);
        }

        $file = UploadedFile::fake()->create('laporan-pwl.pdf', 500, 'application/pdf');
        $filePath = $file->store('milestone-submissions/1/1/pwl', 'local');

        PengumpulanMilestone::create([
            'kelompok_id' => $kelompok->id,
            'milestone_id' => $milestone->id,
            'diunggah_oleh' => $mhs1->id,
            'mata_kuliah' => $mk1->slug,
            'file_name' => 'laporan-pwl.pdf',
            'file_path' => $filePath,
            'link_bukti' => 'https://github.com/sim-pbl/inventaris',
            'catatan' => 'Fokus pada arsitektur RESTful API backend dan otentikasi JWT.',
            'status' => 'diajukan',
        ]);

        return compact('dosen', 'kelompok', 'milestone', 'mhs1', 'mk1', 'mk2', 'mk3');
    }

    public function test_dosen_dapat_melihat_halaman_review_milestone_terpadu(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas.milestone', [$setup['kelompok'], $setup['milestone']]));

        $response->assertOk();
        $response->assertSee('MILESTONE 1');
        $response->assertSee('Kelompok 01');
        $response->assertSee('Pemrograman Web Lanjut');
        $response->assertSee('Manajemen Proyek Sistem Informasi');
        $response->assertSee('Penjaminan Mutu Perangkat Lunak');
        $response->assertSee('Fokus pada arsitektur RESTful API backend');
        $response->assertSee('Muhammad Raihan');
        $response->assertSee('Catatan Pembimbing');
    }

    public function test_dosen_dapat_menyetujui_laporan_milestone_dan_mengirim_notifikasi(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->post(route('dosen.laporan-aktivitas.milestone.review', [$setup['kelompok'], $setup['milestone']]), [
                'action' => 'setujui',
                'catatan' => 'Capaian milestone 1 sangat baik. Lanjutkan ke sprint berikutnya.',
            ]);

        $response->assertRedirect(route('dosen.laporan-aktivitas.milestone', [$setup['kelompok'], $setup['milestone']]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengumpulan_milestone', [
            'kelompok_id' => $setup['kelompok']->id,
            'milestone_id' => $setup['milestone']->id,
            'status' => 'disetujui',
        ]);

        $this->assertDatabaseHas('penyelesaian_milestone', [
            'kelompok_id' => $setup['kelompok']->id,
            'milestone_id' => $setup['milestone']->id,
            'status' => 'selesai',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => Mahasiswa::class,
            'penerima_id' => $setup['mhs1']->id,
            'judul' => 'Milestone Disetujui: '.$setup['milestone']->judul,
        ]);
    }

    public function test_dosen_dapat_meminta_revisi_milestone_dengan_catatan(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->post(route('dosen.laporan-aktivitas.milestone.review', [$setup['kelompok'], $setup['milestone']]), [
                'action' => 'revisi',
                'catatan' => 'Diagram ERD dan test plan testing unit belum lengkap.',
            ]);

        $response->assertRedirect(route('dosen.laporan-aktivitas.milestone', [$setup['kelompok'], $setup['milestone']]));

        $this->assertDatabaseHas('pengumpulan_milestone', [
            'kelompok_id' => $setup['kelompok']->id,
            'milestone_id' => $setup['milestone']->id,
            'status' => 'revisi',
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => Mahasiswa::class,
            'penerima_id' => $setup['mhs1']->id,
            'judul' => 'Milestone Perlu Revisi: '.$setup['milestone']->judul,
        ]);
    }

    public function test_revisi_milestone_wajib_menyertakan_catatan(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->post(route('dosen.laporan-aktivitas.milestone.review', [$setup['kelompok'], $setup['milestone']]), [
                'action' => 'revisi',
                'catatan' => '',
            ]);

        $response->assertSessionHasErrors(['catatan']);
    }
}
