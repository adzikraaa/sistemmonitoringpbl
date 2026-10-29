<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\Periode;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_proposal_page_shows_status_and_feedback(): void
    {
        $periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3B',
            'prodi' => 'Teknologi Informasi',
            'periode_id' => $periode->id,
            'is_aktif' => true,
        ]);

        $dosen = Dosen::create([
            'nama' => 'Dosen Pembimbing',
            'email' => 'dosen@test.ac.id',
            'password' => bcrypt('password'),
            'nidn' => '1234567890',
            'is_aktif' => true,
        ]);

        $mahasiswa = Mahasiswa::create([
            'nama' => 'Mochammad Tanggaq',
            'email' => 'tanggaq@student.polinema.ac.id',
            'password' => bcrypt('secret'),
            'nim' => '244107060105',
            'kelas_id' => $kelas->id,
            'is_aktif' => true,
        ]);

        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok 01',
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id,
            'periode_id' => $periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);

        AnggotaKelompok::create([
            'kelompok_id' => $kelompok->id,
            'mahasiswa_id' => $mahasiswa->id,
            'role_label' => 'PM',
            'is_ketua' => true,
        ]);

        Proposal::create([
            'kelompok_id' => $kelompok->id,
            'judul' => 'Pengembangan Sistem Monitoring PBL',
            'deskripsi' => 'Deskripsi proposal uji',
            'latar_belakang' => 'Latar belakang uji',
            'tujuan' => 'Tujuan uji',
            'metode' => 'Metode uji',
            'status' => 'disetujui',
            'catatan_revisi' => 'Struktur proposal sudah baik. Lanjutkan ke implementasi modul logbook.',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        $response = $this->actingAs($mahasiswa, 'mahasiswa')->get('/mahasiswa/proposal');

        $response->assertStatus(200)
            ->assertSee('Status Proposal')
            ->assertSee('Feedback Pembimbing')
            ->assertSee('Disetujui');
    }
}
