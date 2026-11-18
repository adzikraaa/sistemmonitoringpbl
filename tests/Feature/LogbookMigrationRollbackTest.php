<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Mahasiswa;
use App\Models\Milestone;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LogbookMigrationRollbackTest extends TestCase
{
    use RefreshDatabase;

    private Kelompok $kelompok;

    private Mahasiswa $mahasiswa;

    private Periode $periode;

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

        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3B',
            'periode_id' => $this->periode->id,
        ]);

        $dosen = Dosen::create([
            'nama' => 'Dosen H2',
            'email' => 'dosen.h2@test.com',
            'password' => 'password123',
            'nidn' => '000111229',
            'is_aktif' => true,
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'nama' => 'Mahasiswa H2',
            'email' => 'mahasiswa.h2@test.com',
            'password' => 'password123',
            'nim' => '2026801',
            'kelas_id' => $kelas->id,
            'is_aktif' => true,
        ]);

        $this->kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok H2',
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id,
            'periode_id' => $this->periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);

        $this->kelompok->mahasiswa()->attach($this->mahasiswa->id, [
            'role_label' => 'Ketua',
            'is_ketua' => true,
        ]);
    }

    public function test_rollback_ditolak_jika_ada_logbook_null(): void
    {
        $logbook = $this->buatLogbook(null, 'Logbook tanpa milestone');

        try {
            $this->migration()->down();
            $this->fail('Rollback seharusnya ditolak.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Rollback dibatalkan', $exception->getMessage());
            $this->assertStringContainsString('1 logbook', $exception->getMessage());
        }

        $this->assertTrue($this->milestoneColumn()['nullable']);
        $this->assertSame('SET NULL', $this->foreignKeyOnDelete());
        $this->assertNull($logbook->fresh()->milestone_id);
        $this->assertSame('Logbook tanpa milestone', $logbook->fresh()->judul);
    }

    public function test_rollback_berhasil_jika_tidak_ada_logbook_null(): void
    {
        $milestone = $this->buatMilestone();
        $logbook = $this->buatLogbook($milestone, 'Logbook dengan milestone');

        try {
            $this->migration()->down();

            $this->assertFalse($this->milestoneColumn()['nullable']);
            $this->assertSame('RESTRICT', $this->foreignKeyOnDelete());
            $this->assertDatabaseHas('logbook_milestone', [
                'id' => $logbook->id,
                'milestone_id' => $milestone->id,
                'judul' => 'Logbook dengan milestone',
            ]);
        } finally {
            if (! $this->milestoneColumn()['nullable']) {
                $this->migration()->up();
            }
        }
    }

    public function test_guard_tidak_menjalankan_ddl_sebelum_exception(): void
    {
        $this->buatLogbook(null, 'Logbook tanpa milestone');

        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $this->migration()->down();
            $this->fail('Rollback seharusnya ditolak.');
        } catch (\RuntimeException) {
            //
        } finally {
            $queries = collect(DB::getQueryLog())
                ->pluck('query')
                ->map(fn ($query) => strtolower($query));

            DB::disableQueryLog();
        }

        foreach ($queries as $query) {
            $this->assertStringNotContainsString('alter table', $query);
            $this->assertStringNotContainsString('drop table', $query);
            $this->assertStringNotContainsString('drop index', $query);
            $this->assertStringNotContainsString('create table', $query);
        }

        $this->assertTrue($this->milestoneColumn()['nullable']);
        $this->assertSame('SET NULL', $this->foreignKeyOnDelete());
    }

    public function test_store_logbook_null_dan_halaman_tetap_berjalan(): void
    {
        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->postJson(route('mahasiswa.logbook.store'), [
                'tanggal' => '2026-10-20',
                'judul' => 'Logbook H2',
                'deskripsi' => 'Catatan tanpa milestone.',
                'progress' => 25,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('logbook_milestone', [
            'mahasiswa_id' => $this->mahasiswa->id,
            'kelompok_id' => $this->kelompok->id,
            'milestone_id' => null,
            'judul' => 'Logbook H2',
        ]);

        $this->actingAs($this->mahasiswa, 'mahasiswa')
            ->get(route('mahasiswa.logbook'))
            ->assertOk()
            ->assertSee('Logbook H2');
    }

    public function test_hapus_milestone_membuat_logbook_null_dan_rollback_ditolak(): void
    {
        $milestone = $this->buatMilestone();
        $logbook = $this->buatLogbook($milestone, 'Logbook historis');
        $milestone->delete();

        $this->assertNull($logbook->fresh()->milestone_id);

        try {
            $this->migration()->down();
            $this->fail('Rollback seharusnya ditolak.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Rollback dibatalkan', $exception->getMessage());
        }

        $this->assertTrue($this->milestoneColumn()['nullable']);
        $this->assertSame('SET NULL', $this->foreignKeyOnDelete());
        $this->assertDatabaseHas('logbook_milestone', [
            'id' => $logbook->id,
            'milestone_id' => null,
            'judul' => 'Logbook historis',
        ]);
    }

    private function buatLogbook(?Milestone $milestone, string $judul): LogbookMilestone
    {
        return LogbookMilestone::create([
            'milestone_id' => $milestone?->id,
            'kelompok_id' => $this->kelompok->id,
            'mahasiswa_id' => $this->mahasiswa->id,
            'tanggal' => '2026-10-20',
            'judul' => $judul,
            'deskripsi' => 'Deskripsi logbook.',
            'kegiatan' => 'Kegiatan logbook.',
            'progress' => 25,
            'status' => 'diajukan',
        ]);
    }

    private function buatMilestone(): Milestone
    {
        return Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'MH2',
            'judul' => 'Milestone H2',
            'urutan' => 1,
        ]);
    }

    private function migration(): object
    {
        return require database_path(
            'migrations/2026_11_17_000002_make_milestone_id_nullable_on_logbook_milestone_table.php'
        );
    }

    private function milestoneColumn(): array
    {
        return collect(Schema::getColumns('logbook_milestone'))
            ->firstWhere('name', 'milestone_id');
    }

    private function foreignKeyOnDelete(): string
    {
        $foreignKey = collect(Schema::getForeignKeys('logbook_milestone'))
            ->first(fn ($fk) => $fk['columns'] === ['milestone_id']);

        return strtoupper((string) ($foreignKey['on_delete'] ?? ''));
    }
}
