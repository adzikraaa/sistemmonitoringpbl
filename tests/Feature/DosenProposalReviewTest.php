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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DosenProposalReviewTest extends TestCase
{
    use RefreshDatabase;

    private function createSetup(): array
    {
        Storage::fake('public');

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
            'nama_kelas' => 'SIB 3B',
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

        $mhs = Mahasiswa::create([
            'nama' => 'Muhammad Raihan',
            'email' => 'raihan@student.polinema.ac.id',
            'password' => 'secret123',
            'nim' => '2141720012',
            'kelas_id' => $kelas->id,
            'is_aktif' => true,
        ]);

        AnggotaKelompok::create([
            'kelompok_id' => $kelompok->id,
            'mahasiswa_id' => $mhs->id,
            'role_label' => 'Ketua',
            'is_ketua' => true,
        ]);

        $file = UploadedFile::fake()->create('proposal-test.pdf', 500, 'application/pdf');
        $filePath = $file->store('proposals', 'public');

        $proposal = Proposal::create([
            'kelompok_id' => $kelompok->id,
            'judul' => 'Sistem Informasi Pengelolaan Aset & Inventaris Terpadu Berbasis Web',
            'deskripsi' => 'Deskripsi umum pengembangan sistem magang dan aset.',
            'latar_belakang' => 'Pengelolaan inventaris laboratorium saat ini masih manual.',
            'tujuan' => 'Membangun portal web terpusat untuk katalogisasi aset kampus.',
            'metode' => 'Modul master data alat laboratorium dan otorisasi persetujuan.',
            'file_proposal' => $filePath,
            'status' => 'diajukan',
            'tanggal_pengajuan' => '2026-09-26',
        ]);

        return compact('dosen', 'kelompok', 'mhs', 'proposal');
    }

    public function test_dosen_can_view_proposal_detail(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas.proposal', $setup['proposal']->id));

        $response->assertOk()
            ->assertSee('Proposal : Sistem Informasi Pengelolaan Aset &amp; Inventaris Terpadu Berbasis Web', false)
            ->assertSee('KELOMPOK 01')
            ->assertSee('Pengelolaan inventaris laboratorium saat ini masih manual')
            ->assertSee('Membangun portal web terpusat untuk katalogisasi aset kampus')
            ->assertSee('Catatan Pembimbing')
            ->assertSee('Setujui Laporan')
            ->assertSee('Revisi Laporan')
            ->assertSee('Muhammad Raihan')
            ->assertSee('2141720012');
    }

    public function test_dosen_can_approve_proposal(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->post(route('dosen.laporan-aktivitas.proposal.review', $setup['proposal']->id), [
                'action' => 'setujui',
                'catatan_revisi' => 'Proposal sangat lengkap dan siap dilanjutkan ke tahap milestone berikutnya.',
            ]);

        $response->assertRedirect(route('dosen.laporan-aktivitas.proposal', $setup['proposal']->id));
        $response->assertSessionHas('success');

        $setup['proposal']->refresh();
        $this->assertSame('disetujui', $setup['proposal']->status);
        $this->assertSame('Proposal sangat lengkap dan siap dilanjutkan ke tahap milestone berikutnya.', $setup['proposal']->catatan_revisi);
    }

    public function test_dosen_can_request_revision_proposal(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->post(route('dosen.laporan-aktivitas.proposal.review', $setup['proposal']->id), [
                'action' => 'revisi',
                'catatan_revisi' => 'Harap lengkapi arsitektur sistem dan detail diagram alur kerja.',
            ]);

        $response->assertRedirect(route('dosen.laporan-aktivitas.proposal', $setup['proposal']->id));
        $response->assertSessionHas('success');

        $setup['proposal']->refresh();
        $this->assertSame('revisi', $setup['proposal']->status);
        $this->assertSame('Harap lengkapi arsitektur sistem dan detail diagram alur kerja.', $setup['proposal']->catatan_revisi);
    }

    public function test_dosen_cannot_request_revision_without_notes(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->post(route('dosen.laporan-aktivitas.proposal.review', $setup['proposal']->id), [
                'action' => 'revisi',
                'catatan_revisi' => '',
            ]);

        $response->assertSessionHasErrors('catatan_revisi');

        $setup['proposal']->refresh();
        $this->assertSame('diajukan', $setup['proposal']->status);
    }

    public function test_dosen_can_filter_and_access_proposal_from_laporan_aktivitas(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas', ['jenis' => 'proposal']));

        $response->assertOk()
            ->assertSee(route('dosen.laporan-aktivitas.proposal', $setup['proposal']->id))
            ->assertSee('Sistem Informasi Pengelolaan Aset &amp; Inventaris Terpadu Berbasis Web', false);
    }

    public function test_dosen_can_download_proposal_file(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas.proposal.download', $setup['proposal']->id));

        $response->assertOk();
    }

    public function test_dosen_can_download_proposal_file_from_local_storage(): void
    {
        Storage::fake('local');
        $setup = $this->createSetup();

        $localPath = 'proposals/local-test.pdf';
        Storage::disk('local')->put($localPath, 'isi proposal privat');

        $setup['proposal']->update(['file_proposal' => $localPath]);

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas.proposal.download', $setup['proposal']->id));

        $response->assertOk();
        $response->assertDownload();
    }

    public function test_dosen_cannot_download_missing_proposal_file(): void
    {
        $setup = $this->createSetup();
        $setup['proposal']->update(['file_proposal' => null]);

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas.proposal.download', $setup['proposal']->id));

        $response->assertNotFound();
    }
}
