<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\MilestoneMataKuliah;
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

        foreach ([
            'Pemrograman Web Lanjut' => 'SIB301',
            'Manajemen Proyek Sistem Informasi' => 'SIB302',
            'Penjaminan Mutu Perangkat Lunak' => 'SIB303',
        ] as $nama => $kode) {
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
}
