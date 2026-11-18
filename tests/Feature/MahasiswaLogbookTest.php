<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaLogbookTest extends TestCase
{
    use RefreshDatabase;

    private Periode $periode;

    private Kelas $kelas;

    private Mahasiswa $mahasiswa;

    private Kelompok $kelompok;

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
            'nama' => 'Mahasiswa Logbook',
            'email' => 'logbook@test.com',
            'password' => 'password123',
            'nim' => '2026101',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->kelompok = $this->buatKelompok('Kelompok Logbook');
        $this->lampirkan($this->kelompok, $this->mahasiswa);
    }

    public function test_logbook_tersimpan_ke_database(): void
    {
        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->postJson(route('mahasiswa.logbook.store'), [
                'tanggal' => '2026-10-20',
                'judul' => 'Implementasi modul',
                'deskripsi' => 'Menyelesaikan modul utama.',
                'progress' => 40,
                'hasil' => 'Modul berjalan.',
                'kendala' => 'Tidak ada.',
                'rencana' => 'Lanjut pengujian.',
            ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('logbook_milestone', [
            'kelompok_id' => $this->kelompok->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'milestone_id' => null,
            'judul' => 'Implementasi modul',
            'status' => 'diajukan',
            'progress' => 40,
        ]);
    }

    public function test_kegiatan_memakai_judul_jika_deskripsi_kosong(): void
    {
        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->postJson(route('mahasiswa.logbook.store'), [
                'tanggal' => '2026-10-21',
                'judul' => 'Rapat kelompok',
                'deskripsi' => '',
                'progress' => 10,
            ])
            ->assertOk();

        $this->assertDatabaseHas('logbook_milestone', [
            'mahasiswa_id' => $this->mahasiswa->id,
            'judul' => 'Rapat kelompok',
            'kegiatan' => 'Rapat kelompok',
        ]);
    }

    public function test_mahasiswa_tanpa_kelompok_tidak_bisa_menyimpan_logbook(): void
    {
        $tanpaKelompok = Mahasiswa::create([
            'nama' => 'Tanpa Kelompok',
            'email' => 'tanpa.logbook@test.com',
            'password' => 'password123',
            'nim' => '2026199',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->actingAs($tanpaKelompok, 'mahasiswa')
            ->postJson(route('mahasiswa.logbook.store'), [
                'tanggal' => '2026-10-20',
                'judul' => 'Tidak boleh tersimpan',
                'progress' => 10,
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('logbook_milestone', 0);
    }

    public function test_validasi_progress_di_luar_rentang_ditolak(): void
    {
        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->postJson(route('mahasiswa.logbook.store'), [
                'tanggal' => '2026-10-20',
                'judul' => 'Progress salah',
                'progress' => 101,
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('logbook_milestone', 0);
    }

    public function test_halaman_hanya_menampilkan_logbook_milik_mahasiswa_login(): void
    {
        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->postJson(route('mahasiswa.logbook.store'), [
                'tanggal' => '2026-10-20',
                'judul' => 'Catatan Milik Sendiri',
                'progress' => 20,
            ])
            ->assertOk();

        $mahasiswaLain = Mahasiswa::create([
            'nama' => 'Mahasiswa Lain',
            'email' => 'lain.logbook@test.com',
            'password' => 'password123',
            'nim' => '2026102',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->lampirkan($this->kelompok, $mahasiswaLain, false);

        $this->actingAs($mahasiswaLain, 'mahasiswa')
            ->postJson(route('mahasiswa.logbook.store'), [
                'tanggal' => '2026-10-21',
                'judul' => 'Catatan Milik Orang Lain',
                'progress' => 30,
            ])
            ->assertOk();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.logbook'))
            ->assertOk()
            ->assertSee('Catatan Milik Sendiri')
            ->assertDontSee('Catatan Milik Orang Lain');
    }

    public function test_logbook_tampil_setelah_halaman_dimuat_ulang(): void
    {
        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->postJson(route('mahasiswa.logbook.store'), [
                'tanggal' => '2026-10-22',
                'judul' => 'Tersimpan dan terbaca',
                'deskripsi' => 'Isi catatan.',
                'progress' => 55,
            ])
            ->assertOk();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.logbook'))
            ->assertOk()
            ->assertSee('Tersimpan dan terbaca')
            ->assertSee('Menunggu Review');
    }

    private function buatKelompok(string $nama): Kelompok
    {
        $dosen = Dosen::create([
            'nama' => 'Dosen '.$nama,
            'email' => strtolower(str_replace(' ', '.', $nama)).'.'.uniqid().'@test.com',
            'password' => 'password123',
            'nidn' => '6'.substr(uniqid(), -8),
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

    private function lampirkan(Kelompok $kelompok, Mahasiswa $mahasiswa, bool $ketua = true): void
    {
        $kelompok->mahasiswa()->attach($mahasiswa->id, [
            'role_label' => $ketua ? 'Ketua' : 'Anggota',
            'is_ketua' => $ketua,
        ]);
    }
}
