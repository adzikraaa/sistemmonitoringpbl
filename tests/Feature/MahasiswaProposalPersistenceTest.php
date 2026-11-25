<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\Proposal;
use App\Models\RiwayatProposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaProposalPersistenceTest extends TestCase
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
            'nama' => 'Mahasiswa Proposal',
            'email' => 'proposal@test.com',
            'password' => 'password123',
            'nim' => '2026201',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->kelompok = $this->buatKelompok('Kelompok Proposal');
        $this->lampirkan($this->kelompok, $this->mahasiswa);
    }

    public function test_proposal_dan_riwayat_disimpan_dalam_satu_transaksi(): void
    {
        $this->fakeDisks();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), $this->payload('Proposal Awal'))
            ->assertRedirect(route('mahasiswa.proposal'));

        $proposal = Proposal::firstOrFail();

        $this->assertSame($this->kelompok->id, $proposal->kelompok_id);
        $this->assertSame('diajukan', $proposal->status);
        $this->assertSame('Proposal Awal', $proposal->judul);
        $this->assertStringStartsWith('proposals/', $proposal->file_proposal);
        Storage::disk('local')->assertExists($proposal->file_proposal);
        Storage::disk('public')->assertMissing($proposal->file_proposal);

        $riwayat = RiwayatProposal::firstOrFail();

        $this->assertSame($proposal->id, $riwayat->proposal_id);
        $this->assertSame(1, $riwayat->versi);
        $this->assertSame('diajukan', $riwayat->status);
        $this->assertSame($proposal->file_proposal, $riwayat->file_proposal);
    }

    public function test_pengajuan_ulang_menambah_versi_tanpa_menggandakan_proposal(): void
    {
        $this->fakeDisks();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), $this->payload('Proposal Awal'));

        $proposal = Proposal::firstOrFail();
        $firstPath = $proposal->file_proposal;

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), $this->payload('Proposal Revisi'));

        $this->assertDatabaseCount('proposal', 1);
        $this->assertDatabaseCount('riwayat_proposal', 2);

        $proposal->refresh();
        $revisiPath = $proposal->file_proposal;

        $this->assertSame('Proposal Revisi', $proposal->judul);
        $this->assertSame(2, RiwayatProposal::max('versi'));
        $this->assertNotSame($firstPath, $revisiPath);

        Storage::disk('local')->assertExists($firstPath);
        Storage::disk('local')->assertExists($revisiPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertMissing($revisiPath);

        $riwayat = RiwayatProposal::orderBy('versi')->get();

        $this->assertSame($firstPath, $riwayat[0]->file_proposal);
        $this->assertSame($revisiPath, $riwayat[1]->file_proposal);
    }

    public function test_mahasiswa_tanpa_kelompok_tidak_menulis_proposal(): void
    {
        $this->fakeDisks();

        $tanpaKelompok = Mahasiswa::create([
            'nama' => 'Tanpa Kelompok',
            'email' => 'tanpa.proposal@test.com',
            'password' => 'password123',
            'nim' => '2026299',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->actingAs($tanpaKelompok, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), $this->payload('Tidak boleh tersimpan'))
            ->assertSessionHasErrors('file_proposal');

        $this->assertDatabaseCount('proposal', 0);
        $this->assertDatabaseCount('riwayat_proposal', 0);
        Storage::disk('local')->assertDirectoryEmpty('/');
        Storage::disk('public')->assertDirectoryEmpty('/');
    }

    public function test_upload_gagal_transaksi_membersihkan_file_baru(): void
    {
        $this->fakeDisks();
        $this->withoutExceptionHandling();

        $eventName = 'eloquent.creating: '.Proposal::class;

        Event::listen($eventName, function () {
            throw new \RuntimeException('gagal simpan proposal');
        });

        $thrown = null;

        try {
            $this->actingAs($this->mahasiswa, 'mahasiswa')
                ->post(route('mahasiswa.proposal.store'), $this->payload('Proposal Gagal'));
        } catch (\RuntimeException $exception) {
            $thrown = $exception;
        } finally {
            Event::forget($eventName);
        }

        $this->assertInstanceOf(\RuntimeException::class, $thrown);
        $this->assertDatabaseCount('proposal', 0);
        $this->assertDatabaseCount('riwayat_proposal', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles());
    }

    public function test_halaman_proposal_kosong_menampilkan_belum_diajukan(): void
    {
        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.proposal'))
            ->assertOk()
            ->assertSee('Belum diajukan')
            ->assertDontSee('Disetujui');
    }

    public function test_hanya_pemilik_kelompok_yang_bisa_mengunduh_proposal(): void
    {
        $this->fakeDisks();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), $this->payload('Proposal Sendiri'));

        $proposal = Proposal::firstOrFail();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.proposal.download', $proposal->id))
            ->assertOk();

        $mahasiswaLain = Mahasiswa::create([
            'nama' => 'Mahasiswa Lain',
            'email' => 'lain.proposal@test.com',
            'password' => 'password123',
            'nim' => '2026202',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $kelompokLain = $this->buatKelompok('Kelompok Lain');
        $this->lampirkan($kelompokLain, $mahasiswaLain);

        $this->actingAs($mahasiswaLain, 'mahasiswa')
            ->get(route('mahasiswa.proposal.download', $proposal->id))
            ->assertNotFound();
    }

    public function test_guest_tidak_bisa_mengunduh_proposal(): void
    {
        $this->get(route('mahasiswa.proposal.download', 1))
            ->assertRedirect(route('login'));
    }

    public function test_file_tidak_ada_di_kedua_disk_menghasilkan_404(): void
    {
        $this->fakeDisks();
        $proposal = $this->buatProposalLangsung('proposals/tidak-ada.pdf');

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.proposal.download', $proposal->id))
            ->assertNotFound();
    }

    public function test_file_lama_di_public_dapat_diunduh_melalui_fallback(): void
    {
        $this->fakeDisks();
        $path = 'proposals/proposal-lama.pdf';
        Storage::disk('public')->put($path, 'isi file lama');

        $proposal = $this->buatProposalLangsung($path);

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.proposal.download', $proposal->id));

        $response->assertOk();
        $this->assertSame('isi file lama', $response->streamedContent());
        Storage::disk('local')->assertMissing($path);
    }

    public function test_private_diprioritaskan_daripada_public(): void
    {
        $this->fakeDisks();
        $path = 'proposals/prioritas.pdf';
        Storage::disk('local')->put($path, 'isi private');
        Storage::disk('public')->put($path, 'isi public');

        $proposal = $this->buatProposalLangsung($path);

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.proposal.download', $proposal->id));

        $response->assertOk();
        $this->assertSame('isi private', $response->streamedContent());
    }

    public function test_file_private_tidak_dapat_diakses_langsung_tanpa_signature(): void
    {
        $this->fakeDisks();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), $this->payload('Proposal Private'));

        $proposal = Proposal::firstOrFail();
        $response = $this->get('/storage/'.$proposal->file_proposal);

        $this->assertContains($response->getStatusCode(), [403, 404]);
    }

    public function test_upload_tidak_membuat_status_disetujui(): void
    {
        $this->fakeDisks();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), $this->payload('Proposal Baru'));

        $this->assertDatabaseMissing('proposal', ['status' => 'disetujui']);
        $this->assertDatabaseHas('proposal', ['status' => 'diajukan']);
    }

    public function test_mahasiswa_dapat_mengedit_proposal(): void
    {
        $this->fakeDisks();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), $this->payload('Proposal Awal'));

        $proposal = Proposal::firstOrFail();

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->put(route('mahasiswa.proposal.update', $proposal->id), [
                'judul' => 'Proposal Diperbarui',
                'deskripsi' => 'Deskripsi baru.',
                'tujuan' => 'Tujuan baru.',
                'latar_belakang' => 'Latar belakang baru.',
                'metode' => 'Metode baru.',
            ]);

        $response->assertRedirect(route('mahasiswa.proposal'));
        $proposal->refresh();

        $this->assertSame('Proposal Diperbarui', $proposal->judul);
        $this->assertSame('diajukan', $proposal->status);
        $this->assertCount(2, $proposal->riwayatProposal);
        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => $this->kelompok->dosen->getMorphClass(),
            'penerima_id' => $this->kelompok->dosen_id,
            'judul' => 'Pembaruan Proposal Proyek',
        ]);
    }

    public function test_mahasiswa_dapat_menghapus_proposal(): void
    {
        $this->fakeDisks();

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.proposal.store'), $this->payload('Proposal Akan Dihapus'));

        $proposal = Proposal::firstOrFail();
        $filePath = $proposal->file_proposal;

        Storage::disk('local')->assertExists($filePath);

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->delete(route('mahasiswa.proposal.destroy', $proposal->id));

        $response->assertRedirect(route('mahasiswa.proposal'));
        $this->assertDatabaseMissing('proposal', ['id' => $proposal->id]);
        $this->assertDatabaseMissing('riwayat_proposal', ['proposal_id' => $proposal->id]);
        Storage::disk('local')->assertMissing($filePath);
    }

    public function test_notifikasi_dapat_ditandai_sudah_dibaca(): void
    {
        $notif = Notifikasi::create([
            'penerima_type' => $this->mahasiswa->getMorphClass(),
            'penerima_id' => $this->mahasiswa->id,
            'judul' => 'Uji Notifikasi',
            'isi' => 'Isi notifikasi',
            'tautan' => route('mahasiswa.proposal'),
            'dibaca_pada' => null,
        ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.notifikasi.baca', $notif->id))
            ->assertRedirect();

        $this->assertNotNull($notif->fresh()->dibaca_pada);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post(route('mahasiswa.notifikasi.baca-semua'))
            ->assertRedirect();
    }

    private function payload(string $judul): array
    {
        return [
            'judul' => $judul,
            'deskripsi' => 'Deskripsi proposal.',
            'tujuan' => 'Tujuan proposal.',
            'latar_belakang' => 'Latar belakang proposal.',
            'metode' => 'Metode proposal.',
            'file_proposal' => UploadedFile::fake()->create('proposal.pdf', 100, 'application/pdf'),
        ];
    }

    private function buatProposalLangsung(string $filePath): Proposal
    {
        return Proposal::create([
            'kelompok_id' => $this->kelompok->id,
            'judul' => 'Proposal Langsung',
            'deskripsi' => 'Deskripsi.',
            'latar_belakang' => 'Latar belakang.',
            'tujuan' => 'Tujuan.',
            'metode' => 'Metode.',
            'file_proposal' => $filePath,
            'status' => 'diajukan',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);
    }

    private function fakeDisks(): void
    {
        Storage::fake('local');
        Storage::fake('public');
    }

    private function buatKelompok(string $nama): Kelompok
    {
        $dosen = Dosen::create([
            'nama' => 'Dosen '.$nama,
            'email' => strtolower(str_replace(' ', '.', $nama)).'.'.uniqid().'@test.com',
            'password' => 'password123',
            'nidn' => '5'.substr(uniqid(), -8),
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
