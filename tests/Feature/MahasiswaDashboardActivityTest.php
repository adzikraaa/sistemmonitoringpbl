<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaDashboardActivityTest extends TestCase
{
    use RefreshDatabase;

    private Periode $periode;

    private Kelas $kelas;

    private Kelompok $kelompok;

    private Mahasiswa $mahasiswa;

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
            'nama' => 'Dosen Dashboard',
            'email' => 'dosen.dashboard@test.com',
            'password' => 'password123',
            'nidn' => '9000000201',
            'is_aktif' => true,
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Dashboard',
            'email' => 'mahasiswa.dashboard@test.com',
            'password' => 'password123',
            'nim' => '2026701',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Dashboard',
            'dosen_id' => $dosen->id,
            'kelas_id' => $this->kelas->id,
            'periode_id' => $this->periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);
        $this->kelompok->mahasiswa()->attach($this->mahasiswa->id, [
            'role_label' => 'Ketua',
            'is_ketua' => true,
        ]);
    }

    public function test_aktivitas_dashboard_berasal_dari_logbook_tersimpan(): void
    {
        $this->buatLogbook($this->mahasiswa, $this->kelompok, '2026-10-01', 'Aktivitas Tersimpan');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSee('Aktivitas Tersimpan');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.logbook'))
            ->assertOk()
            ->assertSee('Aktivitas Tersimpan');
    }

    public function test_logbook_kosong_menampilkan_empty_state(): void
    {
        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSee('Belum ada aktivitas logbook.');
    }

    public function test_aktivitas_mahasiswa_lain_tidak_tampil(): void
    {
        $mahasiswaLain = $this->buatMahasiswa('2026702', 'Mahasiswa Lain');
        $this->kelompok->mahasiswa()->attach($mahasiswaLain->id, [
            'role_label' => 'Anggota',
            'is_ketua' => false,
        ]);

        $this->buatLogbook($mahasiswaLain, $this->kelompok, '2026-10-02', 'Aktivitas Orang Lain');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertDontSee('Aktivitas Orang Lain');
    }

    public function test_aktivitas_kelompok_lain_tidak_tampil(): void
    {
        $mahasiswaLain = $this->buatMahasiswa('2026703', 'Mahasiswa Kelompok Lain');
        $kelompokLain = $this->buatKelompok('Kelompok Lain');
        $kelompokLain->mahasiswa()->attach($mahasiswaLain->id, [
            'role_label' => 'Ketua',
            'is_ketua' => true,
        ]);

        $this->buatLogbook($mahasiswaLain, $kelompokLain, '2026-10-03', 'Aktivitas Kelompok Lain');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertDontSee('Aktivitas Kelompok Lain');
    }

    public function test_aktivitas_diurutkan_dari_terbaru(): void
    {
        $this->buatLogbook($this->mahasiswa, $this->kelompok, '2026-10-01', 'Aktivitas Lama');
        $this->buatLogbook($this->mahasiswa, $this->kelompok, '2026-10-05', 'Aktivitas Baru');
        $this->buatLogbook($this->mahasiswa, $this->kelompok, '2026-10-03', 'Aktivitas Tengah');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Aktivitas Baru', 'Aktivitas Tengah', 'Aktivitas Lama']);
    }

    public function test_aktivitas_dashboard_maksimal_lima(): void
    {
        foreach (range(1, 6) as $index) {
            $this->buatLogbook(
                $this->mahasiswa,
                $this->kelompok,
                '2026-10-0'.$index,
                'Aktivitas '.$index
            );
        }

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.dashboard'))
            ->assertOk()
            ->assertDontSee('Aktivitas 1');
    }

    private function buatLogbook(
        Mahasiswa $mahasiswa,
        Kelompok $kelompok,
        string $tanggal,
        string $judul
    ): LogbookMilestone {
        return LogbookMilestone::create([
            'milestone_id' => null,
            'kelompok_id' => $kelompok->id,
            'mahasiswa_id' => $mahasiswa->id,
            'tanggal' => $tanggal,
            'judul' => $judul,
            'kegiatan' => 'Kegiatan '.$judul,
            'status' => 'diajukan',
        ]);
    }

    private function buatMahasiswa(string $nim, string $nama): Mahasiswa
    {
        return Mahasiswa::create([
            'nama' => $nama,
            'email' => $nim.'@test.com',
            'password' => 'password123',
            'nim' => $nim,
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);
    }

    private function buatKelompok(string $nama): Kelompok
    {
        $dosen = Dosen::create([
            'nama' => 'Dosen '.$nama,
            'email' => strtolower(str_replace(' ', '.', $nama)).'.'.uniqid().'@test.com',
            'password' => 'password123',
            'nidn' => '8'.substr(uniqid(), -8),
            'is_aktif' => true,
        ]);

        return Kelompok::create([
            'nama_kelompok' => $nama,
            'dosen_id' => $dosen->id,
            'kelas_id' => $this->kelas->id,
            'periode_id' => $this->periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);
    }
}
