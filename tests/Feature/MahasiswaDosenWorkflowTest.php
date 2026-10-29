<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\Deliverable;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Mahasiswa;
use App\Models\Milestone;
use App\Models\Periode;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaDosenWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Periode $periode;

    private Kelas $kelas;

    private Dosen $dosen;

    private Mahasiswa $mahasiswa;

    private Kelompok $kelompok;

    private Milestone $milestone;

    private Deliverable $deliverable;

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
            'prodi' => 'Teknologi Informasi',
            'periode_id' => $this->periode->id,
            'is_aktif' => true,
        ]);

        $this->dosen = Dosen::create([
            'nama' => 'Dosen Pembimbing',
            'email' => 'dosen@test.ac.id',
            'password' => bcrypt('password'),
            'nidn' => '1234567890',
            'is_aktif' => true,
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'nama' => 'Mochammad Tanggaq',
            'email' => 'tanggaq@student.polinema.ac.id',
            'password' => bcrypt('secret'),
            'nim' => '244107060105',
            'kelas_id' => $this->kelas->id,
            'is_aktif' => true,
        ]);

        $this->kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok 01',
            'dosen_id' => $this->dosen->id,
            'kelas_id' => $this->kelas->id,
            'periode_id' => $this->periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);

        AnggotaKelompok::create([
            'kelompok_id' => $this->kelompok->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'role_label' => 'PM',
            'is_ketua' => true,
        ]);

        $this->milestone = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M1',
            'judul' => 'Inisiasi Proyek & Proposal',
            'deskripsi' => 'Pengajuan proposal',
            'urutan' => 1,
            'deadline' => now()->addDays(14)->toDateString(),
        ]);

        $this->deliverable = Deliverable::create([
            'milestone_id' => $this->milestone->id,
            'nama' => 'Draf Proposal',
            'urutan' => 1,
        ]);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/mahasiswa/dashboard');
        $response->assertRedirect('/login');

        $responseDosen = $this->get('/dosen/dashboard');
        $responseDosen->assertRedirect('/login');
    }

    public function test_mahasiswa_can_store_proposal(): void
    {
        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')->postJson('/mahasiswa/proposal', [
            'judul' => 'Sistem Monitoring Berbasis IoT',
            'deskripsi' => 'Deskripsi proyek',
            'latar_belakang' => 'Latar belakang masalah',
            'tujuan' => 'Tujuan proyek',
            'metode' => 'Metode Agile',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('proposal', [
            'kelompok_id' => $this->kelompok->id,
            'judul' => 'Sistem Monitoring Berbasis IoT',
            'status' => 'diajukan',
        ]);
    }

    public function test_mahasiswa_can_store_logbook(): void
    {
        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')->postJson('/mahasiswa/logbook', [
            'tanggal' => now()->toDateString(),
            'judul' => 'Perancangan Wireframe Modul Mahasiswa',
            'deskripsi' => 'Membuat wireframe responsif di Figma',
            'progress' => 45,
            'hasil' => 'Figma UI selesai',
            'kendala' => 'Jadwal anggota bentrok',
            'rencana' => 'Implementasi Blade view',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('logbook_milestone', [
            'kelompok_id' => $this->kelompok->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'judul' => 'Perancangan Wireframe Modul Mahasiswa',
            'progress' => 45,
            'status' => 'diajukan',
        ]);
    }

    public function test_mahasiswa_can_upload_deliverable_file(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->post("/mahasiswa/milestone/{$this->milestone->id}/upload", [
                'berkas' => $file,
                'item_id' => (string) $this->deliverable->id,
            ]);

        $response->assertRedirect('/mahasiswa/milestone');

        $this->assertDatabaseHas('pengumpulan_deliverable', [
            'deliverable_id' => $this->deliverable->id,
            'kelompok_id' => $this->kelompok->id,
            'file_name' => 'proposal.pdf',
            'status' => 'diajukan',
        ]);
    }

    public function test_dosen_can_view_kelompok_list_and_detail(): void
    {
        Proposal::create([
            'kelompok_id' => $this->kelompok->id,
            'judul' => 'Sistem Monitoring PBL',
            'deskripsi' => 'Deskripsi proposal',
            'latar_belakang' => 'Latar belakang',
            'tujuan' => 'Tujuan',
            'metode' => 'Scrum',
            'status' => 'diajukan',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        LogbookMilestone::create([
            'milestone_id' => $this->milestone->id,
            'kelompok_id' => $this->kelompok->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'tanggal' => now()->toDateString(),
            'judul' => 'Logbook satu',
            'kegiatan' => 'Kegiatan satu',
            'progress' => 30,
            'status' => 'diajukan',
        ]);

        $responseList = $this->actingAs($this->dosen, 'dosen')->get('/dosen/kelompok');
        $responseList->assertOk()
            ->assertSee('Kelompok 01')
            ->assertSee('Sistem Monitoring PBL');

        $responseDetail = $this->actingAs($this->dosen, 'dosen')->get("/dosen/kelompok/{$this->kelompok->id}");
        $responseDetail->assertOk()
            ->assertSee('Kelompok 01')
            ->assertSee('Mochammad Tanggaq');
    }

    public function test_dosen_cannot_access_other_dosen_kelompok_detail(): void
    {
        $dosenLain = Dosen::create([
            'nama' => 'Dosen Lain',
            'email' => 'dosenlain@test.ac.id',
            'password' => bcrypt('password'),
            'nidn' => '9999999999',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($dosenLain, 'dosen')->get("/dosen/kelompok/{$this->kelompok->id}");
        $response->assertNotFound();
    }
}
