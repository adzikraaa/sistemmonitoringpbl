<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\Periode;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProposalStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_proposal_page_shows_status_and_feedback(): void
    {
        $mahasiswa = Mahasiswa::first() ?? Mahasiswa::create([
            'nama' => 'Mahasiswa Test',
            'nim' => '2024999',
            'email' => 'mhs@test.com',
            'password' => bcrypt('password'),
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($mahasiswa, 'mahasiswa')
            ->get('/mahasiswa/proposal');

        $response->assertStatus(200)
            ->assertSee('Status Proposal')
            ->assertSee('Feedback Pembimbing')
            ->assertSee('Disetujui');
    }

    public function test_mahasiswa_can_upload_pdf_proposal_and_store_to_database(): void
    {
        Storage::fake('public');

        $periode = Periode::create([
            'nama_periode' => '2026/2027 Ganjil',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3B',
            'angkatan' => 2024,
            'is_aktif' => true,
        ]);

        $dosen = Dosen::create([
            'nama' => 'Dosen Pembimbing',
            'email' => 'dosen@test.com',
            'password' => bcrypt('password'),
            'nidn' => '0011223344',
            'is_aktif' => true,
        ]);

        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok 01',
            'kelas_id' => $kelas->id,
            'periode_id' => $periode->id,
            'dosen_id' => $dosen->id,
            'tanggal_dibuat' => '2026-09-10',
        ]);

        $mahasiswa = Mahasiswa::create([
            'nama' => 'Muhammad Adzikra',
            'nim' => '244107060044',
            'email' => 'adzikra@test.com',
            'password' => bcrypt('password'),
            'kelas_id' => $kelas->id,
            'is_aktif' => true,
        ]);

        $kelompok->mahasiswa()->attach($mahasiswa->id, ['role_label' => 'Ketua', 'is_ketua' => true]);

        $pdf = UploadedFile::fake()->create('proposal_pbl_final.pdf', 1024, 'application/pdf');

        $payload = [
            'judul' => 'Sistem Informasi Monitoring PBL',
            'deskripsi' => 'Aplikasi web pemantauan proyek PBL mahasiswa.',
            'latar_belakang' => 'Proses monitoring sebelumnya masih manual.',
            'tujuan' => 'Meningkatkan efisiensi kolaborasi tim.',
            'metode' => 'Scrum / Agile Methodology.',
            'file_proposal' => $pdf,
        ];

        $response = $this->actingAs($mahasiswa, 'mahasiswa')
            ->post('/mahasiswa/proposal', $payload);

        $response->assertRedirect('/mahasiswa/proposal');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('proposal', [
            'kelompok_id' => $kelompok->id,
            'judul' => 'Sistem Informasi Monitoring PBL',
            'status' => 'diajukan',
        ]);

        $proposal = Proposal::where('kelompok_id', $kelompok->id)->first();
        $this->assertNotNull($proposal->file_proposal);
        Storage::disk('public')->assertExists($proposal->file_proposal);

        // Uji unduh proposal
        $downloadResponse = $this->actingAs($mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.proposal.download', $proposal->id));
        $downloadResponse->assertOk();
    }
}
