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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaMilestonePageTest extends TestCase
{
    use RefreshDatabase;

    private Periode $periode;

    private Kelas $kelas;

    private Kelompok $kelompok;

    private Mahasiswa $mahasiswa;

    /** @var array<string, MataKuliah> */
    private array $mataKuliah = [];

    /** @var array<int, Milestone> */
    private array $milestones = [];

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

        $dosen = Dosen::create([
            'nama' => 'Dosen Milestone',
            'email' => 'dosen.milestone@test.com',
            'password' => 'password123',
            'nidn' => '9000000101',
            'is_aktif' => true,
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Milestone',
            'email' => 'mahasiswa.milestone@test.com',
            'password' => 'password123',
            'nim' => '2026601',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Milestone',
            'dosen_id' => $dosen->id,
            'kelas_id' => $this->kelas->id,
            'periode_id' => $this->periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);
        $this->kelompok->mahasiswa()->attach($this->mahasiswa->id, [
            'role_label' => 'Ketua',
            'is_ketua' => true,
        ]);

        foreach (
            [
                'Pemrograman Web Lanjut' => 'SIB301',
                'Manajemen Proyek Sistem Informasi' => 'SIB302',
                'Penjaminan Mutu Perangkat Lunak' => 'SIB303',
            ] as $nama => $kode
        ) {
            $this->mataKuliah[$nama] = MataKuliah::create([
                'kode' => $kode,
                'nama_matkul' => $nama,
                'is_aktif' => true,
            ]);
        }

        $this->milestones = [
            Milestone::create([
                'periode_id' => $this->periode->id,
                'kode' => 'M01',
                'judul' => 'Milestone Satu',
                'urutan' => 1,
            ]),
            Milestone::create([
                'periode_id' => $this->periode->id,
                'kode' => 'M02',
                'judul' => 'Milestone Dua',
                'urutan' => 2,
            ]),
        ];
    }

    public function test_tiga_mata_kuliah_tampil_tanpa_penyelesaian_dan_progres_nol(): void
    {
        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone'));

        $response->assertOk()
            ->assertSee('Pemrograman Web Lanjut')
            ->assertSee('Manajemen Proyek Sistem Informasi')
            ->assertSee('Penjaminan Mutu Perangkat Lunak')
            ->assertSee('0%')
            ->assertSee('Belum dimulai');
    }

    public function test_mapping_tersedia_tanpa_penyelesaian_status_belum_dimulai(): void
    {
        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone'));

        $response->assertOk()->assertSee('Belum dimulai');
    }

    public function test_mapping_kosong_menampilkan_belum_ada_data(): void
    {
        MilestoneMataKuliah::query()
            ->where('mata_kuliah_id', $this->mataKuliah['Penjaminan Mutu Perangkat Lunak']->id)
            ->delete();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone'))
            ->assertOk()
            ->assertSee('Penjaminan Mutu Perangkat Lunak')
            ->assertSee('Belum ada data');
    }

    public function test_progres_selesai_dihitung(): void
    {
        foreach ($this->milestones as $milestone) {
            PenyelesaianMilestone::create([
                'kelompok_id' => $this->kelompok->id,
                'milestone_id' => $milestone->id,
                'mata_kuliah_id' => $this->mataKuliah['Pemrograman Web Lanjut']->id,
                'status' => 'selesai',
            ]);
        }

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone'))
            ->assertOk()
            ->assertSee('100%');
    }

    public function test_tiga_mata_kuliah_tampil_saat_mahasiswa_belum_punya_kelompok(): void
    {
        $mahasiswaTanpaKelompok = Mahasiswa::create([
            'nama' => 'Mahasiswa Tanpa Kelompok',
            'email' => 'mahasiswa.tanpa.kelompok@test.com',
            'password' => 'password123',
            'nim' => '2026602',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($mahasiswaTanpaKelompok, 'mahasiswa')
            ->get(route('mahasiswa.milestone'));

        $response->assertOk()
            ->assertSee('Pemrograman Web Lanjut')
            ->assertSee('Manajemen Proyek Sistem Informasi')
            ->assertSee('Penjaminan Mutu Perangkat Lunak')
            ->assertSee('0%')
            ->assertSee('Belum ada data')
            ->assertSee('Belum ditugaskan');
    }

    public function test_mata_kuliah_lain_tidak_tampil(): void
    {
        $lain = MataKuliah::create([
            'kode' => 'SIB999',
            'nama_matkul' => 'Mata Kuliah Lain',
            'is_aktif' => true,
        ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone'))
            ->assertOk()
            ->assertDontSee('Mata Kuliah Lain');
    }


    public function test_halaman_detail_menampilkan_tahapan_milestone_meski_belum_ada_berkas(): void
    {
        $this->milestones[0]->update([
            'deskripsi' => 'Penyusunan dokumen analisis kebutuhan sistem.',
            'tanggal_mulai' => '2026-09-10',
            'deadline' => '2026-10-25',
        ]);

        MilestoneMataKuliah::firstOrCreate([
            'milestone_id' => $this->milestones[0]->id,
            'mata_kuliah_id' => $this->mataKuliah['Pemrograman Web Lanjut']->id,
        ]);

        app()->setLocale('id');
        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', 'pemrograman-web-lanjut'))
            ->assertOk()
            ->assertSee('M01')
            ->assertSee('Milestone 1 – Milestone Satu')
            ->assertSee('Penyusunan dokumen analisis kebutuhan sistem.')
            ->assertSee('10 September 2026')
            ->assertSee('Tenggat waktu:')
            ->assertSee('25 Oktober 2026')
            ->assertSee('Belum Dikumpulkan');
    }

    public function test_judul_milestone_tidak_ganda_saat_judul_database_sudah_mengandung_kata_milestone(): void
    {
        $this->milestones[0]->update(['judul' => 'MILESTONE 1']);
        $this->milestones[1]->update(['judul' => 'MILESTONE 2 : Desain Basis Data']);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', 'pemrograman-web-lanjut'))
            ->assertOk()
            ->assertSee('Milestone 1')
            ->assertDontSee('Milestone 1 – MILESTONE 1')
            ->assertSee('Milestone 2 – Desain Basis Data')
            ->assertDontSee('Milestone 2 – MILESTONE 2');
    }

    public function test_halaman_detail_menampilkan_catatan_revisi_dan_status_verifikasi_dosen(): void
    {
        $dosenVerifikator = Dosen::where('email', 'dosen.milestone@test.com')->firstOrFail();
        $pwl = $this->mataKuliah['Pemrograman Web Lanjut'];

        PengumpulanMilestone::create([
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $this->milestones[0]->id,
            'diunggah_oleh' => $this->mahasiswa->id,
            'mata_kuliah' => $pwl->slug,
            'file_name' => 'laporan-m1.pdf',
            'file_path' => 'milestone-submissions/laporan-m1.pdf',
            'catatan' => 'Implementasi autentikasi awal.',
            'status' => 'revisi',
        ]);

        PenyelesaianMilestone::create([
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $this->milestones[0]->id,
            'mata_kuliah_id' => $pwl->id,
            'status' => PenyelesaianMilestone::STATUS_BERJALAN,
            'catatan' => 'Lengkapi validasi form dan tambahkan skema ERD terbaru.',
            'diverifikasi_oleh' => $dosenVerifikator->id,
            'diverifikasi_pada' => '2026-10-20 14:30:00',
        ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', 'pemrograman-web-lanjut'))
            ->assertOk()
            ->assertSee('Perlu Revisi')
            ->assertSee('Sedang Berjalan')
            ->assertSee('Dosen Milestone')
            ->assertSee('Lengkapi validasi form dan tambahkan skema ERD terbaru.');
    }

    public function test_halaman_detail_tetap_menampilkan_tahapan_periode_aktif_saat_mahasiswa_belum_punya_kelompok(): void
    {
        $mahasiswaTanpaKelompok = Mahasiswa::create([
            'nama' => 'Mahasiswa Baru',
            'email' => 'mahasiswa.baru@test.com',
            'password' => 'password123',
            'nim' => '2026699',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->actingAs($mahasiswaTanpaKelompok, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', 'pemrograman-web-lanjut'))
            ->assertOk()
            ->assertSee('Milestone 1 – Milestone Satu')
            ->assertSee('Milestone 2 – Milestone Dua')
            ->assertSee('Belum Dikumpulkan');
    }

    public function test_milestone_yang_sudah_disetujui_tidak_ditampilkan_lagi_di_dropdown_pengumpulan(): void
    {
        $pwl = $this->mataKuliah['Pemrograman Web Lanjut'];

        PengumpulanMilestone::create([
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $this->milestones[0]->id,
            'diunggah_oleh' => $this->mahasiswa->id,
            'mata_kuliah' => $pwl->slug,
            'file_name' => 'laporan-m1.pdf',
            'file_path' => 'milestone-submissions/laporan-m1.pdf',
            'catatan' => 'Sudah selesai.',
            'status' => 'disetujui',
        ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.milestone.detail', 'pemrograman-web-lanjut'))
            ->assertOk()
            ->assertSee('Milestone 1 – Milestone Satu')
            ->assertSee('Disetujui')
            ->assertDontSee('M01 - Milestone Satu')
            ->assertSee('M02 - Milestone Dua');
    }
}
