<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Koordinator;
use App\Models\MataKuliah;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatPenugasanDosenTest extends TestCase
{
    use RefreshDatabase;

    protected Koordinator $koordinator;

    protected Kelas $kelas;

    protected Periode $periode;

    protected Dosen $dosenAwal;

    protected Dosen $dosenBaru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->koordinator = Koordinator::create([
            'nama' => 'Koordinator Riwayat',
            'email' => 'riwayat.koordinator@test.com',
            'password' => 'password123',
            'nidn' => 'RIW-KOOR-01',
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => 'SIB Riwayat',
            'status' => 'Aktif',
        ]);

        $this->periode = Periode::create([
            'nama_periode' => 'Periode Riwayat',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2026-12-31',
            'is_aktif' => true,
        ]);

        $this->dosenAwal = Dosen::create([
            'nama' => 'Dosen Awal',
            'nidn' => 'RIW-DSN-01',
            'email' => 'dosen.awal@test.com',
            'password' => 'password123',
            'is_aktif' => true,
        ]);

        $this->dosenBaru = Dosen::create([
            'nama' => 'Dosen Pengganti',
            'nidn' => 'RIW-DSN-02',
            'email' => 'dosen.baru@test.com',
            'password' => 'password123',
            'is_aktif' => true,
        ]);

        $this->kelas->update(['periode_id' => $this->periode->id]);
        $this->kelas->dosenPengampu()->create([
            'dosen_id' => $this->dosenAwal->id,
            'mata_kuliah_id' => MataKuliah::create(['nama_matkul' => 'Pemrograman Web'])->id,
        ]);
    }

    private function dataKelompok(array $tambahan = []): array
    {
        return array_merge([
            'nama_kelompok' => 'Kelompok Riwayat',
            'judul_proyek' => 'Proyek Pengujian Riwayat',
            'kelas_id' => $this->kelas->id,
            'tahun_akademik' => $this->periode->tahun_akademik,
            'dosen_id' => $this->dosenBaru->id,
        ], $tambahan);
    }

    public function test_penugasan_awal_dosen_dicatat_saat_kelompok_dibuat(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->post(
                route('koordinator.kelompok.store'),
                $this->dataKelompok()
            );

        $response->assertRedirect(route('koordinator.kelompok.index'));

        $kelompok = Kelompok::where(
            'nama_kelompok',
            'Kelompok Riwayat'
        )->firstOrFail();

        $this->assertDatabaseHas('riwayat_penugasan_dosen', [
            'kelompok_id' => $kelompok->id,
            'dosen_id' => $this->dosenAwal->id,
            'dosen_sebelumnya_id' => null,
            'periode_id' => $this->periode->id,
            'koordinator_id' => $this->koordinator->id,
            'aksi' => 'penugasan_baru',
        ]);
    }

    public function test_pergantian_dosen_dicatat_dengan_dosen_lama_dan_baru(): void
    {
        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Pergantian',
            'judul_proyek' => 'Proyek Pergantian',
            'kelas_id' => $this->kelas->id,
            'periode_id' => $this->periode->id,
            'dosen_id' => $this->dosenAwal->id,
            'tanggal_dibuat' => now()->toDateString(),
            'status' => 'Aktif',
        ]);

        $this->kelas->dosenPengampu()->update(['dosen_id' => $this->dosenBaru->id]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->put(
                route('koordinator.kelompok.update', $kelompok->id),
                $this->dataKelompok([
                    'nama_kelompok' => 'Kelompok Pergantian',
                    'judul_proyek' => 'Proyek Pergantian',
                    'dosen_id' => $this->dosenBaru->id,
                ])
            );

        $response->assertRedirect(route('koordinator.kelompok.index'));

        $this->assertDatabaseHas('kelompok', [
            'id' => $kelompok->id,
            'dosen_id' => $this->dosenBaru->id,
        ]);

        $this->assertDatabaseHas('riwayat_penugasan_dosen', [
            'kelompok_id' => $kelompok->id,
            'dosen_id' => $this->dosenBaru->id,
            'dosen_sebelumnya_id' => $this->dosenAwal->id,
            'periode_id' => $this->periode->id,
            'koordinator_id' => $this->koordinator->id,
            'aksi' => 'perubahan_dosen',
        ]);
    }

    public function test_edit_kelompok_tanpa_pergantian_dosen_tidak_membuat_riwayat(): void
    {
        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Tetap',
            'judul_proyek' => 'Proyek Tetap',
            'kelas_id' => $this->kelas->id,
            'periode_id' => $this->periode->id,
            'dosen_id' => $this->dosenAwal->id,
            'tanggal_dibuat' => now()->toDateString(),
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->put(
                route('koordinator.kelompok.update', $kelompok->id),
                $this->dataKelompok([
                    'nama_kelompok' => 'Kelompok Tetap Diperbarui',
                    'judul_proyek' => 'Proyek Tetap Diperbarui',
                    'dosen_id' => $this->dosenAwal->id,
                ])
            );

        $response->assertRedirect(route('koordinator.kelompok.index'));

        $this->assertDatabaseCount('riwayat_penugasan_dosen', 0);

        $this->assertDatabaseHas('kelompok', [
            'id' => $kelompok->id,
            'dosen_id' => $this->dosenAwal->id,
            'nama_kelompok' => 'Kelompok Tetap Diperbarui',
        ]);
    }
}
