<?php

namespace Tests\Feature;

use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\Periode;
use App\Services\MilestoneMataKuliahSync;
use Database\Seeders\MilestoneMataKuliahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class MilestoneMataKuliahTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_dibuat_otomatis_dan_tidak_berubah_saat_nama_diubah(): void
    {
        $mataKuliah = MataKuliah::create([
            'kode' => 'SIB301',
            'nama_matkul' => 'Pemrograman Web Lanjut',
            'is_aktif' => true,
        ]);

        $this->assertSame(Str::slug('Pemrograman Web Lanjut'), $mataKuliah->slug);

        $mataKuliah->update(['nama_matkul' => 'Pemrograman Web']);

        $this->assertSame('pemrograman-web-lanjut', $mataKuliah->fresh()->slug);
    }

    public function test_slug_duplikat_mendapat_suffix_unik(): void
    {
        $pertama = MataKuliah::create([
            'kode' => 'SIB301',
            'nama_matkul' => 'Mata Kuliah Sama',
            'is_aktif' => true,
        ]);

        $kedua = MataKuliah::create([
            'kode' => 'SIB302',
            'nama_matkul' => 'Mata Kuliah Sama',
            'is_aktif' => true,
        ]);

        $this->assertNotSame($pertama->slug, $kedua->slug);
        $this->assertSame('mata-kuliah-sama', $pertama->slug);
        $this->assertSame('mata-kuliah-sama-2', $kedua->slug);
    }

    public function test_relasi_milestone_dan_mata_kuliah_dua_arah(): void
    {
        $periode = $this->buatPeriode();
        $milestone = $this->buatMilestone($periode, 1, 'M01');
        $mataKuliah = $this->buatMataKuliah('SIB301', 'Pemrograman Web Lanjut');

        $this->assertTrue($milestone->mataKuliah->contains($mataKuliah));
        $this->assertTrue($mataKuliah->milestone->contains($milestone));
    }

    public function test_milestone_baru_otomatis_memetakan_semua_matkul_aktif(): void
    {
        $periode = $this->buatPeriode();
        $aktifSatu = $this->buatMataKuliah('SIB301', 'Pemrograman Web Lanjut');
        $aktifDua = $this->buatMataKuliah('SIB302', 'Manajemen Proyek Sistem Informasi');
        $tidakAktif = $this->buatMataKuliah('SIB303', 'Mata Kuliah Nonaktif', false);

        $milestone = $this->buatMilestone($periode, 1, 'M01');

        $this->assertDatabaseCount('milestone_mata_kuliah', 2);
        $this->assertDatabaseHas('milestone_mata_kuliah', [
            'milestone_id' => $milestone->id,
            'mata_kuliah_id' => $aktifSatu->id,
        ]);
        $this->assertDatabaseHas('milestone_mata_kuliah', [
            'milestone_id' => $milestone->id,
            'mata_kuliah_id' => $aktifDua->id,
        ]);
        $this->assertDatabaseMissing('milestone_mata_kuliah', [
            'milestone_id' => $milestone->id,
            'mata_kuliah_id' => $tidakAktif->id,
        ]);
    }

    public function test_mata_kuliah_baru_otomatis_memetakan_semua_milestone(): void
    {
        $periode = $this->buatPeriode();
        $milestoneSatu = $this->buatMilestone($periode, 1, 'M01');
        $milestoneDua = $this->buatMilestone($periode, 2, 'M02');

        $mataKuliah = $this->buatMataKuliah('SIB301', 'Pemrograman Web Lanjut');

        $this->assertDatabaseCount('milestone_mata_kuliah', 2);
        $this->assertDatabaseHas('milestone_mata_kuliah', [
            'milestone_id' => $milestoneSatu->id,
            'mata_kuliah_id' => $mataKuliah->id,
        ]);
        $this->assertDatabaseHas('milestone_mata_kuliah', [
            'milestone_id' => $milestoneDua->id,
            'mata_kuliah_id' => $mataKuliah->id,
        ]);
    }

    public function test_mapping_idempotent_saat_service_dipanggil_ulang(): void
    {
        $periode = $this->buatPeriode();
        $this->buatMilestone($periode, 1, 'M01');
        $mataKuliah = $this->buatMataKuliah('SIB301', 'Pemrograman Web Lanjut');

        $sync = app(MilestoneMataKuliahSync::class);
        $sync->forMataKuliah($mataKuliah);
        $sync->forMataKuliah($mataKuliah);

        $this->assertDatabaseCount('milestone_mata_kuliah', 1);
    }

    public function test_nonaktif_mempertahankan_mapping_dan_aktivasi_melengkapi(): void
    {
        $periode = $this->buatPeriode();
        $mataKuliah = $this->buatMataKuliah('SIB301', 'Pemrograman Web Lanjut');
        $milestoneSatu = $this->buatMilestone($periode, 1, 'M01');

        $mataKuliah->update(['is_aktif' => false]);

        $this->assertDatabaseHas('milestone_mata_kuliah', [
            'milestone_id' => $milestoneSatu->id,
            'mata_kuliah_id' => $mataKuliah->id,
        ]);

        $milestoneDua = $this->buatMilestone($periode, 2, 'M02');

        $this->assertDatabaseMissing('milestone_mata_kuliah', [
            'milestone_id' => $milestoneDua->id,
            'mata_kuliah_id' => $mataKuliah->id,
        ]);

        $mataKuliah->update(['is_aktif' => true]);

        $this->assertDatabaseHas('milestone_mata_kuliah', [
            'milestone_id' => $milestoneSatu->id,
            'mata_kuliah_id' => $mataKuliah->id,
        ]);
        $this->assertDatabaseHas('milestone_mata_kuliah', [
            'milestone_id' => $milestoneDua->id,
            'mata_kuliah_id' => $mataKuliah->id,
        ]);
    }

    public function test_seeder_memetakan_semua_milestone_ke_semua_matkul_aktif(): void
    {
        $periode = $this->buatPeriode();
        $now = now();

        foreach ([1, 2] as $urutan) {
            DB::table('milestone')->insert([
                'periode_id' => $periode->id,
                'kode' => 'M0'.$urutan,
                'judul' => 'Milestone '.$urutan,
                'urutan' => $urutan,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ([
            ['SIB301', 'Mata Kuliah Aktif 1', 'mata-kuliah-aktif-1', true],
            ['SIB302', 'Mata Kuliah Aktif 2', 'mata-kuliah-aktif-2', true],
            ['SIB303', 'Mata Kuliah Nonaktif', 'mata-kuliah-nonaktif', false],
        ] as [$kode, $nama, $slug, $aktif]) {
            DB::table('mata_kuliah')->insert([
                'kode' => $kode,
                'nama_matkul' => $nama,
                'slug' => $slug,
                'is_aktif' => $aktif,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->seed(MilestoneMataKuliahSeeder::class);
        $this->seed(MilestoneMataKuliahSeeder::class);

        $this->assertDatabaseCount('milestone_mata_kuliah', 4);
        $this->assertDatabaseMissing('milestone_mata_kuliah', [
            'mata_kuliah_id' => DB::table('mata_kuliah')->where('kode', 'SIB303')->value('id'),
        ]);
    }

    private function buatPeriode(): Periode
    {
        return Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);
    }

    private function buatMilestone(Periode $periode, int $urutan, string $kode): Milestone
    {
        return Milestone::create([
            'periode_id' => $periode->id,
            'kode' => $kode,
            'judul' => 'Milestone '.$urutan,
            'urutan' => $urutan,
        ]);
    }

    private function buatMataKuliah(string $kode, string $nama, bool $aktif = true): MataKuliah
    {
        return MataKuliah::create([
            'kode' => $kode,
            'nama_matkul' => $nama,
            'is_aktif' => $aktif,
        ]);
    }
}
