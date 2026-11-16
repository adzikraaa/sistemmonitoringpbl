<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Koordinator;
use App\Models\MataKuliah;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenugasanDosenTest extends TestCase
{
    use RefreshDatabase;

    protected Periode $periode;
    protected Koordinator $koordinator;
    protected Kelas $kelas;
    protected MataKuliah $mkPwl;
    protected MataKuliah $mkMpsi;
    protected MataKuliah $mkPmpl;
    protected Dosen $dosen;

    protected function setUp(): void
    {
        parent::setUp();

        $this->periode = Periode::firstOrCreate(
            ['nama_periode' => 'Ganjil 2026/2027'],
            [
                'tahun_akademik'  => '2026/2027',
                'tanggal_mulai'   => '2026-08-31',
                'tanggal_selesai' => '2027-01-15',
                'is_aktif'        => true,
            ]
        );

        $this->koordinator = Koordinator::firstOrCreate(
            ['nidn' => '0099000001'],
            [
                'nama'     => 'Nadia Kusuma',
                'email'    => 'koordinator_test_unit@test.com',
                'password' => 'password',
            ]
        );

        $this->kelas = Kelas::firstOrCreate(
            ['nama_kelas' => 'SIB 3A'],
            [
                'prodi'      => 'D4 Sistem Informasi Bisnis',
                'periode_id' => $this->periode->id,
                'is_aktif'   => true,
            ]
        );

        $this->mkPwl = MataKuliah::firstOrCreate(
            ['nama_matkul' => 'Pemrograman Web Lanjut'],
            ['prodi' => 'D4 Sistem Informasi Bisnis', 'sks' => 3, 'is_aktif' => true]
        );

        $this->mkMpsi = MataKuliah::firstOrCreate(
            ['nama_matkul' => 'Manajemen Proyek Sistem Informasi'],
            ['prodi' => 'D4 Sistem Informasi Bisnis', 'sks' => 3, 'is_aktif' => true]
        );

        $this->mkPmpl = MataKuliah::firstOrCreate(
            ['nama_matkul' => 'Penjaminan Mutu Perangkat Lunak'],
            ['prodi' => 'D4 Sistem Informasi Bisnis', 'sks' => 3, 'is_aktif' => true]
        );

        $this->dosen = Dosen::firstOrCreate(
            ['nidn' => 'DSN-UNIT-01'],
            [
                'nama'     => 'Dosen Unit Test',
                'email'    => 'dosen_unit_test@test.com',
                'password' => 'password',
                'is_aktif' => true,
            ]
        );
    }

    public function test_guest_cannot_access_penugasan_dosen_page(): void
    {
        $response = $this->get(route('koordinator.penugasan-dosen.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_koordinator_can_view_penugasan_dosen_page(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->get(route('koordinator.penugasan-dosen.index'));

        $response->assertStatus(200);
        $response->assertSee('Penugasan Dosen Penilai PBL');
        $response->assertSee('Pemrograman Web Lanjut');
        $response->assertSee('Direktori dosen penilai');
        $response->assertSee('DOSEN PENILAI TERSEDIA');
        $response->assertSee('KELAS TERLIPUT');
    }

    public function test_koordinator_can_assign_lecturer_to_multiple_courses(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->post(route('koordinator.penugasan-dosen.store'), [
                'dosen_id'        => $this->dosen->id,
                'kelas_id'        => $this->kelas->id,
                'mata_kuliah_ids' => [$this->mkPwl->id, $this->mkMpsi->id],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dosen_pengampu', [
            'dosen_id'       => $this->dosen->id,
            'kelas_id'       => $this->kelas->id,
            'mata_kuliah_id' => $this->mkPwl->id,
        ]);
        $this->assertDatabaseHas('dosen_pengampu', [
            'dosen_id'       => $this->dosen->id,
            'kelas_id'       => $this->kelas->id,
            'mata_kuliah_id' => $this->mkMpsi->id,
        ]);
    }

    public function test_cannot_assign_lecturer_exceeding_capacity_limit_of_three(): void
    {
        // Berikan 3 penugasan terlebih dahulu
        $kelasLain1 = Kelas::firstOrCreate(['nama_kelas' => 'SIB 3B', 'periode_id' => $this->periode->id], ['prodi' => 'D4 SIB', 'is_aktif' => true]);
        $kelasLain2 = Kelas::firstOrCreate(['nama_kelas' => 'SIB 3C', 'periode_id' => $this->periode->id], ['prodi' => 'D4 SIB', 'is_aktif' => true]);

        DosenPengampu::updateOrCreate(['kelas_id' => $this->kelas->id, 'mata_kuliah_id' => $this->mkPwl->id], ['dosen_id' => $this->dosen->id]);
        DosenPengampu::updateOrCreate(['kelas_id' => $kelasLain1->id, 'mata_kuliah_id' => $this->mkPwl->id], ['dosen_id' => $this->dosen->id]);
        DosenPengampu::updateOrCreate(['kelas_id' => $kelasLain2->id, 'mata_kuliah_id' => $this->mkPwl->id], ['dosen_id' => $this->dosen->id]);

        // Coba berikan penugasan ke-4 di mata kuliah lain
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->post(route('koordinator.penugasan-dosen.store'), [
                'dosen_id'        => $this->dosen->id,
                'kelas_id'        => $this->kelas->id,
                'mata_kuliah_ids' => [$this->mkPmpl->id],
            ]);

        $response->assertSessionHasErrors('dosen_id');
    }

    public function test_koordinator_can_update_course_evaluator(): void
    {
        $pengampu = DosenPengampu::updateOrCreate(
            ['kelas_id' => $this->kelas->id, 'mata_kuliah_id' => $this->mkPwl->id],
            ['dosen_id' => $this->dosen->id]
        );

        $dosenBaru = Dosen::firstOrCreate(
            ['nidn' => 'DSN-UNIT-02'],
            ['nama' => 'Dosen Pengganti', 'email' => 'pengganti@test.com', 'password' => 'password', 'is_aktif' => true]
        );

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->put(route('koordinator.penugasan-dosen.update', $pengampu->id), [
                'dosen_id' => $dosenBaru->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dosen_pengampu', [
            'id'       => $pengampu->id,
            'dosen_id' => $dosenBaru->id,
        ]);
    }

    public function test_koordinator_can_delete_course_evaluator(): void
    {
        $pengampu = DosenPengampu::updateOrCreate(
            ['kelas_id' => $this->kelas->id, 'mata_kuliah_id' => $this->mkPwl->id],
            ['dosen_id' => $this->dosen->id]
        );

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->delete(route('koordinator.penugasan-dosen.destroy', $pengampu->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('dosen_pengampu', [
            'id' => $pengampu->id,
        ]);
    }

    public function test_api_data_returns_valid_json(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->get(route('koordinator.penugasan-dosen.api-data'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'dosen',
            'kelas',
            'mata_kuliah',
        ]);
    }
}
