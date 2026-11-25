<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoordinatorMilestoneGlobalTest extends TestCase
{
    use RefreshDatabase;

    protected Periode $periode;

    protected Koordinator $koordinator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-08-31',
            'tanggal_selesai' => '2027-01-15',
            'is_aktif' => true,
        ]);

        $this->koordinator = Koordinator::create([
            'nama' => 'Nadia Kusuma',
            'email' => 'koordinator@test.com',
            'password' => bcrypt('password'),
            'nidn' => '0000000001',
        ]);
    }

    public function test_guest_cannot_access_milestone_global_page(): void
    {
        $response = $this->get(route('koordinator.milestone-global.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_koordinator_can_view_milestone_global_page(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->get(route('koordinator.milestone-global.index'));

        $response->assertStatus(200);
        $response->assertSee('Milestone Global');
        $response->assertSee('Tambah Milestone');
    }

    public function test_koordinator_can_create_milestone_global(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->post(route('koordinator.milestone-global.store'), [
                'judul' => 'Prototype & UI awal',
                'deskripsi' => 'Tahun ajaran baru.',
                'tanggal_mulai' => '2026-11-01',
                'deadline' => '2026-11-06',
                'urutan' => 1,
            ]);

        $response->assertRedirect(route('koordinator.milestone-global.index'));
        $this->followRedirects($response)
            ->assertSee('window.setTimeout(dismiss, 5000)', false);
        $this->assertDatabaseHas('milestone', [
            'periode_id' => $this->periode->id,
            'judul' => 'Prototype & UI awal',
            'urutan' => 1,
            'tanggal_mulai' => '2026-11-01 00:00:00',
            'deadline' => '2026-11-06 00:00:00',
        ]);

        $page = $this->actingAs($this->koordinator, 'koordinator')
            ->get(route('koordinator.milestone-global.index'));

        $page->assertSee('Prototype & UI awal');
    }

    public function test_koordinator_can_update_milestone_global(): void
    {
        $milestone = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M1',
            'judul' => 'Prototype awal',
            'deskripsi' => 'Deskripsi lama',
            'urutan' => 1,
            'tanggal_mulai' => '2026-11-01',
            'deadline' => '2026-11-06',
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->put(route('koordinator.milestone-global.update', $milestone->id), [
                'judul' => 'Prototype & UI awal revisi',
                'deskripsi' => 'Deskripsi baru.',
                'tanggal_mulai' => '2026-11-08',
                'deadline' => '2026-11-15',
                'urutan' => 1,
            ]);

        $response->assertRedirect(route('koordinator.milestone-global.index'));
        $this->assertDatabaseHas('milestone', [
            'id' => $milestone->id,
            'judul' => 'Prototype & UI awal revisi',
            'deskripsi' => 'Deskripsi baru.',
            'tanggal_mulai' => '2026-11-08 00:00:00',
            'deadline' => '2026-11-15 00:00:00',
        ]);
    }

    public function test_jadwal_milestone_memberi_notifikasi_kepada_mahasiswa_dan_dosen_terkait(): void
    {
        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3A',
            'prodi' => 'D4 Sistem Informasi Bisnis',
            'periode_id' => $this->periode->id,
            'is_aktif' => true,
        ]);
        $mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa Jadwal',
            'email' => 'mahasiswa-jadwal@test.com',
            'password' => 'password123',
            'nim' => '244107000001',
            'kelas_id' => $kelas->id,
            'is_aktif' => true,
        ]);
        $dosen = Dosen::create([
            'nama' => 'Dosen Jadwal',
            'email' => 'dosen-jadwal@test.com',
            'password' => 'password123',
            'nidn' => '900000030',
            'is_aktif' => true,
        ]);
        $mataKuliah = MataKuliah::create([
            'kode' => 'MKJ01',
            'nama_matkul' => 'Mata Kuliah Jadwal',
            'sks' => 3,
            'semester' => 5,
            'is_aktif' => true,
        ]);
        DosenPengampu::create([
            'dosen_id' => $dosen->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'kelas_id' => $kelas->id,
        ]);

        $this->actingAs($this->koordinator, 'koordinator')
            ->post(route('koordinator.milestone-global.store'), [
                'judul' => 'Implementasi Sistem',
                'deskripsi' => 'Implementasi fitur utama.',
                'tanggal_mulai' => '2026-11-10',
                'deadline' => '2026-11-20',
                'urutan' => 1,
            ])
            ->assertRedirect(route('koordinator.milestone-global.index'));

        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => $mahasiswa->getMorphClass(),
            'penerima_id' => $mahasiswa->id,
            'judul' => 'Jadwal Implementasi Sistem ditetapkan',
        ]);
        $this->assertDatabaseHas('notifikasi', [
            'penerima_type' => $dosen->getMorphClass(),
            'penerima_id' => $dosen->id,
            'judul' => 'Jadwal Implementasi Sistem ditetapkan',
        ]);
    }

    public function test_tenggat_tidak_boleh_sebelum_tanggal_mulai(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->post(route('koordinator.milestone-global.store'), [
                'judul' => 'Jadwal Tidak Valid',
                'tanggal_mulai' => '2026-11-20',
                'deadline' => '2026-11-10',
                'urutan' => 1,
            ]);

        $response->assertSessionHasErrors('deadline');
        $this->assertDatabaseMissing('milestone', ['judul' => 'Jadwal Tidak Valid']);
    }

    public function test_koordinator_can_delete_unused_milestone_and_remaining_order_is_normalized(): void
    {
        $mataKuliah = MataKuliah::create([
            'kode' => 'SIB301',
            'nama_matkul' => 'Pemrograman Web Lanjut',
            'is_aktif' => true,
        ]);
        $milestoneSatu = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M01',
            'judul' => 'Milestone pertama',
            'urutan' => 1,
        ]);
        $milestoneDua = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M02',
            'judul' => 'Milestone kedua',
            'urutan' => 2,
        ]);
        $milestoneTiga = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M03',
            'judul' => 'Milestone ketiga',
            'urutan' => 3,
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->delete(route('koordinator.milestone-global.destroy', $milestoneDua));

        $response->assertRedirect(route('koordinator.milestone-global.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('milestone', ['id' => $milestoneDua->id]);
        $this->assertDatabaseHas('milestone', [
            'id' => $milestoneSatu->id,
            'kode' => 'M01',
            'urutan' => 1,
        ]);
        $this->assertDatabaseHas('milestone', [
            'id' => $milestoneTiga->id,
            'kode' => 'M02',
            'urutan' => 2,
        ]);
        $this->assertDatabaseCount('milestone_mata_kuliah', 2);
        $this->assertDatabaseHas('milestone_mata_kuliah', [
            'milestone_id' => $milestoneTiga->id,
            'mata_kuliah_id' => $mataKuliah->id,
        ]);
    }

    public function test_new_milestone_is_inserted_at_selected_order_and_existing_milestones_are_shifted(): void
    {
        $milestoneLama = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M01',
            'judul' => 'Prototype awal',
            'deskripsi' => 'Deskripsi lama',
            'urutan' => 1,
            'deadline' => '2026-11-06',
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->post(route('koordinator.milestone-global.store'), [
                'judul' => 'Tambah milestone baru',
                'deskripsi' => 'Jadwal dari form tambah.',
                'deadline' => '2026-11-20',
                'urutan' => 1,
            ]);

        $response->assertRedirect(route('koordinator.milestone-global.index'));

        // Milestone baru menempati urutan yang dipilih.
        $this->assertDatabaseHas('milestone', [
            'periode_id' => $this->periode->id,
            'judul' => 'Tambah milestone baru',
            'urutan' => 1,
            'kode' => 'M01',
        ]);

        // Milestone lama bergeser ke urutan berikutnya.
        $this->assertDatabaseHas('milestone', [
            'id' => $milestoneLama->id,
            'judul' => 'Prototype awal',
            'urutan' => 2,
            'kode' => 'M02',
        ]);
    }
}
