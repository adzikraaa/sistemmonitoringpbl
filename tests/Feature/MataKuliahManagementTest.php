<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Koordinator;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\PengumpulanMilestone;
use App\Models\Periode;
use App\Services\MilestoneMataKuliahSync;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MataKuliahManagementTest extends TestCase
{
    use RefreshDatabase;

    private Koordinator $koordinator;

    private Periode $periode;

    private Kelas $kelas;

    private Dosen $dosen;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->koordinator = Koordinator::create([
            'nama' => 'Koordinator Mata Kuliah',
            'email' => 'koordinator.mk@test.com',
            'password' => 'password123',
            'nidn' => '000111226',
        ]);

        $this->periode = Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => 'SIB 3B',
            'prodi' => 'D4 Sistem Informasi Bisnis',
            'periode_id' => $this->periode->id,
            'is_aktif' => true,
        ]);

        $this->dosen = Dosen::create([
            'nama' => 'Dosen Pengampu',
            'email' => 'dosen.mk@test.com',
            'password' => 'password123',
            'nidn' => '000111227',
            'is_aktif' => true,
        ]);
    }

    private function makeMataKuliah(array $attributes = []): MataKuliah
    {
        $this->sequence++;

        return MataKuliah::create(array_merge([
            'kode' => 'SIB9'.str_pad((string) $this->sequence, 2, '0', STR_PAD_LEFT),
            'nama_matkul' => 'Mata Kuliah '.$this->sequence,
            'sks' => 3,
            'semester' => 5,
            'is_aktif' => true,
        ], $attributes));
    }

    public function test_index_handles_null_kode_and_semester_without_invalid_javascript(): void
    {
        $this->makeMataKuliah([
            'kode' => null,
            'semester' => null,
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->get(route('koordinator.master-data.mata-kuliah'));

        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('pilihMkEdit', $content);

        preg_match('/pilihMkEdit\((.*?)\)/s', $content, $matches);

        $this->assertNotEmpty($matches);
        $this->assertStringContainsString('null', $matches[1]);
        $this->assertDoesNotMatchRegularExpression('/,\s*,/', $matches[1]);
    }

    public function test_store_mata_kuliah_does_not_create_null_dosen_pengampu(): void
    {
        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->post(route('koordinator.master-data.mata-kuliah.store'), [
                'kode' => 'SIB777',
                'nama_matkul' => 'Mata Kuliah Baru',
                'sks' => 3,
                'semester' => 5,
                'is_aktif' => 1,
                'kelas_id' => [$this->kelas->id],
            ]);

        $response->assertRedirect(route('koordinator.master-data.mata-kuliah'));

        $mataKuliah = MataKuliah::where('kode', 'SIB777')->firstOrFail();

        $this->assertDatabaseMissing('dosen_pengampu', [
            'mata_kuliah_id' => $mataKuliah->id,
        ]);
    }

    public function test_update_mata_kuliah_preserves_existing_dosen_pengampu(): void
    {
        $mataKuliah = $this->makeMataKuliah(['kode' => null, 'semester' => null]);

        $pengampu = DosenPengampu::create([
            'dosen_id' => $this->dosen->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'kelas_id' => $this->kelas->id,
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->put(route('koordinator.master-data.mata-kuliah.update', $mataKuliah), [
                'kode' => 'SIB888',
                'nama_matkul' => 'Mata Kuliah Diperbarui',
                'sks' => 4,
                'semester' => 6,
                'is_aktif' => 1,
                'kelas_id' => [$this->kelas->id],
            ]);

        $response->assertRedirect(route('koordinator.master-data.mata-kuliah'));

        $this->assertDatabaseHas('mata_kuliah', [
            'id' => $mataKuliah->id,
            'kode' => 'SIB888',
            'nama_matkul' => 'Mata Kuliah Diperbarui',
            'sks' => 4,
            'semester' => 6,
        ]);

        $this->assertDatabaseHas('dosen_pengampu', [
            'id' => $pengampu->id,
            'dosen_id' => $this->dosen->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'kelas_id' => $this->kelas->id,
        ]);
    }

    public function test_update_without_kelas_id_preserves_existing_dosen_pengampu(): void
    {
        $mataKuliah = $this->makeMataKuliah();

        $pengampu = DosenPengampu::create([
            'dosen_id' => $this->dosen->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'kelas_id' => $this->kelas->id,
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->put(route('koordinator.master-data.mata-kuliah.update', $mataKuliah), [
                'kode' => $mataKuliah->kode,
                'nama_matkul' => 'Mata Kuliah Tanpa Kelas',
                'sks' => 3,
                'semester' => 5,
                'is_aktif' => 1,
            ]);

        $response->assertRedirect(route('koordinator.master-data.mata-kuliah'));

        $this->assertDatabaseHas('dosen_pengampu', [
            'id' => $pengampu->id,
            'dosen_id' => $this->dosen->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'kelas_id' => $this->kelas->id,
        ]);
    }

    public function test_failed_update_does_not_delete_existing_dosen_pengampu(): void
    {
        $mataKuliah = $this->makeMataKuliah();
        $other = $this->makeMataKuliah();

        $pengampu = DosenPengampu::create([
            'dosen_id' => $this->dosen->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'kelas_id' => $this->kelas->id,
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->put(route('koordinator.master-data.mata-kuliah.update', $mataKuliah), [
                'kode' => $other->kode,
                'nama_matkul' => 'Kode Duplikat',
                'sks' => 3,
                'semester' => 5,
                'is_aktif' => 1,
                'kelas_id' => [$this->kelas->id],
            ]);

        $response->assertSessionHasErrors('kode');

        $this->assertDatabaseHas('dosen_pengampu', [
            'id' => $pengampu->id,
            'dosen_id' => $this->dosen->id,
            'mata_kuliah_id' => $mataKuliah->id,
            'kelas_id' => $this->kelas->id,
        ]);
    }

    public function test_mata_kuliah_dengan_submission_tidak_dapat_dihapus(): void
    {
        $mataKuliah = $this->makeMataKuliah();

        $kelompok = Kelompok::create([
            'nama_kelompok' => 'Kelompok Submission',
            'dosen_id' => $this->dosen->id,
            'kelas_id' => $this->kelas->id,
            'periode_id' => $this->periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);

        $milestone = Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M01',
            'judul' => 'Milestone Submission',
            'urutan' => 1,
        ]);

        PengumpulanMilestone::create([
            'milestone_id' => $milestone->id,
            'kelompok_id' => $kelompok->id,
            'mata_kuliah' => $mataKuliah->slug,
            'file_name' => 'laporan.pdf',
            'file_path' => 'test/laporan.pdf',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->delete(route('koordinator.master-data.mata-kuliah.destroy', $mataKuliah));

        $response->assertRedirect(route('koordinator.master-data.mata-kuliah'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('mata_kuliah', ['id' => $mataKuliah->id]);
        $this->assertDatabaseHas('pengumpulan_milestone', [
            'kelompok_id' => $kelompok->id,
            'milestone_id' => $milestone->id,
            'mata_kuliah' => $mataKuliah->slug,
        ]);
    }

    public function test_mata_kuliah_tanpa_submission_tetap_dapat_dihapus(): void
    {
        $mataKuliah = $this->makeMataKuliah();

        $response = $this->actingAs($this->koordinator, 'koordinator')
            ->delete(route('koordinator.master-data.mata-kuliah.destroy', $mataKuliah));

        $response->assertRedirect(route('koordinator.master-data.mata-kuliah'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('mata_kuliah', ['id' => $mataKuliah->id]);
    }

    public function test_gagal_sinkronisasi_pivot_membatalkan_pembuatan_mata_kuliah(): void
    {
        $this->withoutExceptionHandling();

        $this->mock(MilestoneMataKuliahSync::class, function ($mock) {
            $mock->shouldReceive('forMataKuliah')
                ->once()
                ->andThrow(new \RuntimeException('sync gagal'));
        });

        $thrown = null;

        try {
            $this->actingAs($this->koordinator, 'koordinator')
                ->post(route('koordinator.master-data.mata-kuliah.store'), [
                    'kode' => 'SIB778',
                    'nama_matkul' => 'Mata Kuliah Gagal Sync',
                    'sks' => 3,
                    'semester' => 5,
                    'is_aktif' => 1,
                ]);
        } catch (\RuntimeException $exception) {
            $thrown = $exception;
        }

        $this->assertInstanceOf(\RuntimeException::class, $thrown);
        $this->assertDatabaseMissing('mata_kuliah', ['kode' => 'SIB778']);
    }

    public function test_gagal_sinkronisasi_pivot_membatalkan_aktivasi_mata_kuliah(): void
    {
        $this->withoutExceptionHandling();

        $mataKuliah = $this->makeMataKuliah([
            'kode' => 'SIB779',
            'is_aktif' => false,
        ]);

        $this->mock(MilestoneMataKuliahSync::class, function ($mock) {
            $mock->shouldReceive('forMataKuliah')
                ->once()
                ->andThrow(new \RuntimeException('sync gagal'));
        });

        $thrown = null;

        try {
            $this->actingAs($this->koordinator, 'koordinator')
                ->patch(route('koordinator.master-data.mata-kuliah.toggle', $mataKuliah));
        } catch (\RuntimeException $exception) {
            $thrown = $exception;
        }

        $this->assertInstanceOf(\RuntimeException::class, $thrown);
        $this->assertDatabaseHas('mata_kuliah', [
            'id' => $mataKuliah->id,
            'is_aktif' => false,
        ]);
    }

    public function test_race_submission_saat_delete_ditangani_sebagai_pesan_error(): void
    {
        $mataKuliah = $this->makeMataKuliah();
        $kelompok = $this->buatKelompok();
        $milestone = $this->buatMilestone();

        $eventName = 'eloquent.deleting: '.MataKuliah::class;

        Event::listen($eventName, function () use ($mataKuliah, $kelompok, $milestone) {
            DB::table('pengumpulan_milestone')->insert([
                'kelompok_id' => $kelompok->id,
                'milestone_id' => $milestone->id,
                'mata_kuliah' => $mataKuliah->slug,
                'file_name' => 'race.pdf',
                'file_path' => 'test/race.pdf',
                'status' => 'diajukan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        try {
            $response = $this->actingAs($this->koordinator, 'koordinator')
                ->delete(route('koordinator.master-data.mata-kuliah.destroy', $mataKuliah));
        } finally {
            Event::forget($eventName);
        }

        $response->assertRedirect(route('koordinator.master-data.mata-kuliah'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('mata_kuliah', ['id' => $mataKuliah->id]);
        $this->assertDatabaseHas('pengumpulan_milestone', [
            'kelompok_id' => $kelompok->id,
            'milestone_id' => $milestone->id,
            'mata_kuliah' => $mataKuliah->slug,
        ]);
    }

    public function test_query_exception_non_foreign_key_diteruskan(): void
    {
        $this->withoutExceptionHandling();

        $mataKuliah = $this->makeMataKuliah();
        $eventName = 'eloquent.deleting: '.MataKuliah::class;

        Event::listen($eventName, function () {
            throw new QueryException(
                'sqlite',
                'delete from mata_kuliah ...',
                [],
                new \RuntimeException('database is locked')
            );
        });

        $thrown = null;

        try {
            $this->actingAs($this->koordinator, 'koordinator')
                ->delete(route('koordinator.master-data.mata-kuliah.destroy', $mataKuliah));
        } catch (QueryException $exception) {
            $thrown = $exception;
        } finally {
            Event::forget($eventName);
        }

        $this->assertInstanceOf(QueryException::class, $thrown);
        $this->assertDatabaseHas('mata_kuliah', ['id' => $mataKuliah->id]);
    }

    private function buatKelompok(): Kelompok
    {
        return Kelompok::create([
            'nama_kelompok' => 'Kelompok '.uniqid(),
            'dosen_id' => $this->dosen->id,
            'kelas_id' => $this->kelas->id,
            'periode_id' => $this->periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);
    }

    private function buatMilestone(): Milestone
    {
        return Milestone::create([
            'periode_id' => $this->periode->id,
            'kode' => 'M'.substr(uniqid(), -4),
            'judul' => 'Milestone '.uniqid(),
            'urutan' => random_int(1, 99),
        ]);
    }
}
