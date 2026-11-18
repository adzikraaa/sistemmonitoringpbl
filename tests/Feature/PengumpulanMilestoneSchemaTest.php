<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\Periode;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PengumpulanMilestoneSchemaTest extends TestCase
{
    use RefreshDatabase;

    private Kelompok $kelompok;

    private Milestone $milestone;

    private MataKuliah $mataKuliah;

    protected function setUp(): void
    {
        parent::setUp();

        $periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3B',
            'periode_id' => $periode->id,
        ]);

        $dosen = Dosen::create([
            'nama' => 'Dosen Schema',
            'email' => 'schema@test.com',
            'password' => 'password123',
            'nidn' => '0099000999',
            'is_aktif' => true,
        ]);

        $this->kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Schema',
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id,
            'periode_id' => $periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);

        $this->milestone = Milestone::create([
            'periode_id' => $periode->id,
            'kode' => 'M01',
            'judul' => 'Milestone Schema',
            'urutan' => 1,
        ]);

        $this->mataKuliah = MataKuliah::create([
            'kode' => 'SIB301',
            'nama_matkul' => 'Pemrograman Web Lanjut',
            'is_aktif' => true,
        ]);
    }

    public function test_kolom_mata_kuliah_not_null_dan_unique_tiga_kolom(): void
    {
        $kolom = collect(Schema::getColumns('pengumpulan_milestone'))
            ->firstWhere('name', 'mata_kuliah');

        $this->assertNotNull($kolom);
        $this->assertFalse($kolom['nullable']);
        $this->assertSame('varchar', $kolom['type_name']);

        $index = collect(Schema::getIndexes('pengumpulan_milestone'))
            ->firstWhere('name', 'pengumpulan_milestone_identitas_unique');

        $this->assertNotNull($index);
        $this->assertTrue($index['unique']);
        $this->assertSame(
            ['kelompok_id', 'milestone_id', 'mata_kuliah'],
            $index['columns']
        );
    }

    public function test_foreign_key_mata_kuliah_mengarah_ke_slug(): void
    {
        $foreignKey = collect(Schema::getForeignKeys('pengumpulan_milestone'))
            ->first(fn ($fk) => $fk['columns'] === ['mata_kuliah']);

        $this->assertNotNull($foreignKey);
        $this->assertSame('mata_kuliah', $foreignKey['foreign_table']);
        $this->assertSame(['slug'], $foreignKey['foreign_columns']);
    }

    public function test_unique_pasangan_pivot_dan_unique_slug_matkul(): void
    {
        $pivot = collect(Schema::getIndexes('milestone_mata_kuliah'))
            ->firstWhere('name', 'milestone_mata_kuliah_pair_unique');

        $this->assertNotNull($pivot);
        $this->assertTrue($pivot['unique']);
        $this->assertSame(['milestone_id', 'mata_kuliah_id'], $pivot['columns']);

        $slug = collect(Schema::getIndexes('mata_kuliah'))
            ->firstWhere('name', 'mata_kuliah_slug_unique');

        $this->assertNotNull($slug);
        $this->assertTrue($slug['unique']);
        $this->assertSame(['slug'], $slug['columns']);
    }

    public function test_duplikat_triple_ditolak_database(): void
    {
        $row = $this->barisSubmission('laporan.pdf');

        DB::table('pengumpulan_milestone')->insert($row);

        $this->expectException(QueryException::class);

        DB::table('pengumpulan_milestone')->insert($row);
    }

    public function test_slug_mata_kuliah_tidak_dikenal_ditolak_foreign_key(): void
    {
        $row = $this->barisSubmission('laporan.pdf');
        $row['mata_kuliah'] = 'slug-tidak-ada';

        $this->expectException(QueryException::class);

        DB::table('pengumpulan_milestone')->insert($row);
    }

    private function barisSubmission(string $fileName): array
    {
        return [
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => $this->milestone->id,
            'mata_kuliah' => $this->mataKuliah->slug,
            'diunggah_oleh' => null,
            'file_name' => $fileName,
            'file_path' => 'test/'.$fileName,
            'link_bukti' => null,
            'catatan' => 'Catatan',
            'status' => 'diajukan',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
