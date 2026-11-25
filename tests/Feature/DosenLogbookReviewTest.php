<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Koordinator;
use App\Models\LogbookMilestone;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DosenLogbookReviewTest extends TestCase
{
    use RefreshDatabase;

    private function createSetup(): array
    {
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
            'nama_kelompok' => 'Kelompok Uji 244107060044',
            'judul_proyek' => 'Sistem Monitoring Otomatis',
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id,
            'periode_id' => $periode->id,
            'status' => 'Aktif',
            'tanggal_dibuat' => '2026-09-01',
        ]);

        $mhs = Mahasiswa::create([
            'nama' => 'Mahasiswa 244107060044',
            'email' => '244107060044@student.polinema.ac.id',
            'password' => 'secret123',
            'nim' => '244107060044',
            'kelas_id' => $kelas->id,
            'is_aktif' => true,
        ]);

        AnggotaKelompok::create([
            'kelompok_id' => $kelompok->id,
            'mahasiswa_id' => $mhs->id,
            'role_label' => 'Ketua',
            'is_ketua' => true,
        ]);

        $logbook = LogbookMilestone::create([
            'kelompok_id' => $kelompok->id,
            'mahasiswa_id' => $mhs->id,
            'tanggal' => '2026-11-22',
            'judul' => 'Implementasi autentikasi & perancangan token JWT',
            'deskripsi' => 'Fokus utama pada minggu ketujuh adalah penyelesaian modul otentikasi peran ganda.',
            'kegiatan' => 'Penyelesaian modul otentikasi peran ganda',
            'progress' => 80,
            'hasil' => "Berhasil mengonfigurasi RBAC\nOptimasi pemanggilan hook otentikasi",
            'kendala' => 'Terdapat kendala race condition saat refresh beberapa tab bersamaan.',
            'rencana' => "Menyelesaikan validasi form\nMelakukan unit testing",
            'status' => 'diajukan',
        ]);

        return compact('dosen', 'kelompok', 'mhs', 'logbook');
    }

    public function test_dosen_can_view_logbook_detail(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas.logbook', $setup['logbook']->id));

        $response->assertOk()
            ->assertSee('Logbook : Implementasi autentikasi &amp; perancangan token JWT', false)
            ->assertSee('KELOMPOK UJI 244107060044')
            ->assertSee('Fokus utama pada minggu ketujuh adalah penyelesaian modul otentikasi')
            ->assertSee('Berhasil mengonfigurasi RBAC')
            ->assertSee('Terdapat kendala race condition')
            ->assertSee('Menyelesaikan validasi form')
            ->assertSee('Catatan Pembimbing')
            ->assertSee('Simpan &amp; Setujui Logbook', false)
            ->assertSee('Minta Revisi Logbook');
    }

    public function test_dosen_can_approve_logbook(): void
    {
        $setup = $this->createSetup();
        $koordinator = Koordinator::create([
            'nama' => 'Koordinator Pengawas',
            'email' => 'koordinator.pengawas@example.com',
            'password' => 'secret123',
            'nidn' => '0099887711',
        ]);

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->post(route('dosen.laporan-aktivitas.logbook.review', $setup['logbook']->id), [
                'action' => 'setujui',
                'feedback' => 'Sangat bagus, lanjutkan implementasi token refresh.',
            ]);

        $response->assertRedirect(route('dosen.laporan-aktivitas.logbook', $setup['logbook']->id));
        $response->assertSessionHas('success');

        $setup['logbook']->refresh();
        $this->assertSame('disetujui', $setup['logbook']->status);
        $this->assertSame('Sangat bagus, lanjutkan implementasi token refresh.', $setup['logbook']->feedback);
        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => $setup['mhs']->getMorphClass(),
            'penerima_id' => $setup['mhs']->id,
            'judul' => 'Logbook Disetujui',
            'dibaca_pada' => null,
        ]);
        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => $koordinator->getMorphClass(),
            'penerima_id' => $koordinator->id,
            'judul' => 'Dosen Menyetujui Logbook',
            'dibaca_pada' => null,
        ]);
    }

    public function test_dosen_can_request_revision_with_feedback(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->post(route('dosen.laporan-aktivitas.logbook.review', $setup['logbook']->id), [
                'action' => 'revisi',
                'feedback' => 'Tolong perbaiki penanganan kendala race condition pada session.',
            ]);

        $response->assertRedirect(route('dosen.laporan-aktivitas.logbook', $setup['logbook']->id));
        $response->assertSessionHas('success');

        $setup['logbook']->refresh();
        $this->assertSame('revisi', $setup['logbook']->status);
        $this->assertSame('Tolong perbaiki penanganan kendala race condition pada session.', $setup['logbook']->feedback);
        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => $setup['mhs']->getMorphClass(),
            'penerima_id' => $setup['mhs']->id,
            'judul' => 'Logbook Perlu Revisi',
            'dibaca_pada' => null,
        ]);
    }

    public function test_dosen_cannot_request_revision_without_feedback(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->post(route('dosen.laporan-aktivitas.logbook.review', $setup['logbook']->id), [
                'action' => 'revisi',
                'feedback' => '',
            ]);

        $response->assertSessionHasErrors('feedback');

        $setup['logbook']->refresh();
        $this->assertSame('diajukan', $setup['logbook']->status);
    }

    public function test_dosen_can_filter_and_access_logbook_from_laporan_aktivitas(): void
    {
        $setup = $this->createSetup();

        // Mengakses laporan aktivitas dengan filter jenis=logbook
        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas', ['jenis' => 'logbook']));

        $response->assertOk()
            ->assertSee(route('dosen.laporan-aktivitas.logbook', $setup['logbook']->id))
            ->assertSee('Implementasi autentikasi &amp; perancangan token JWT', false);
    }

    public function test_dosen_can_download_logbook_attachment_file(): void
    {
        Storage::fake('local');
        $setup = $this->createSetup();

        $filePath = 'logbook-attachments/test/bukti-testing.pdf';
        Storage::disk('local')->put($filePath, 'konten file logbook');

        $setup['logbook']->update([
            'file_bukti' => $filePath,
            'link_bukti' => 'https://github.com/test/repo',
        ]);

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas.logbook.download', $setup['logbook']->id));

        $response->assertOk();
        $response->assertDownload();
    }

    public function test_dosen_cannot_download_missing_logbook_attachment(): void
    {
        Storage::fake('local');
        $setup = $this->createSetup();

        $setup['logbook']->update([
            'file_bukti' => null,
        ]);

        $response = $this->actingAs($setup['dosen'], 'dosen')
            ->get(route('dosen.laporan-aktivitas.logbook.download', $setup['logbook']->id));

        $response->assertNotFound();
    }
}
