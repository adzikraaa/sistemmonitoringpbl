<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KelompokIdentityTest extends TestCase
{
    use RefreshDatabase;

    private Periode $periode;

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

        $this->kelompok = $this->buatKelompok($this->periode);
    }

    public function test_ketua_ditempatkan_di_awal_dan_anggota_lain_terurut(): void
    {
        $m1 = $this->buatMahasiswa('2024001', 'Anggota Satu');
        $m2 = $this->buatMahasiswa('2024002', 'Anggota Dua');
        $ketua = $this->buatMahasiswa('2024003', 'Ketua Kelompok');
        $m4 = $this->buatMahasiswa('2024004', 'Anggota Empat');
        $m5 = $this->buatMahasiswa('2024005', 'Anggota Lima');

        $this->lampirkan($m1);
        $this->lampirkan($m2);
        $this->lampirkan($ketua, true);
        $this->lampirkan($m4);
        $this->lampirkan($m5);

        $urutan = $this->kelompok->anggotaTerurut();
        $nim = $urutan->map(fn ($anggota) => $anggota->mahasiswa->nim)->all();

        $this->assertSame(
            ['2024003', '2024001', '2024002', '2024004', '2024005'],
            $nim
        );
        $this->assertSame('2024003', $this->kelompok->nimKetua());
    }

    public function test_beberapa_ketua_dipilih_deterministik_dari_id_terkecil(): void
    {
        $anggota = $this->buatMahasiswa('2024010', 'Anggota');
        $ketuaPertama = $this->buatMahasiswa('2024011', 'Ketua Pertama');
        $ketuaKedua = $this->buatMahasiswa('2024012', 'Ketua Kedua');

        $this->lampirkan($anggota);
        $this->lampirkan($ketuaPertama, true);
        $this->lampirkan($ketuaKedua, true);

        $this->assertSame('2024011', $this->kelompok->nimKetua());
        $this->assertSame('2024011', $this->kelompok->anggotaTerurut()->first()->mahasiswa->nim);
    }

    public function test_tanpa_ketua_tidak_menebak_anggota_pertama(): void
    {
        $this->lampirkan($this->buatMahasiswa('2024020', 'Anggota Satu'));
        $this->lampirkan($this->buatMahasiswa('2024021', 'Anggota Dua'));

        $this->assertNull($this->kelompok->ketua());
        $this->assertNull($this->kelompok->nimKetua());
    }

    public function test_with_identitas_tidak_menghasilkan_n_plus_1(): void
    {
        foreach (['2024030', '2024031', '2024032', '2024033'] as $index => $nim) {
            $this->lampirkan($this->buatMahasiswa($nim, 'Anggota '.$nim), $index === 0);
        }

        foreach ([1, 2] as $index) {
            $kelompok = $this->buatKelompok($this->periode, $index);

            foreach (['40', '41', '42', '43'] as $anggotaIndex => $suffix) {
                $nim = '20240'.$index.$suffix;
                $this->lampirkan(
                    $this->buatMahasiswa($nim, 'Anggota '.$nim),
                    $anggotaIndex === 0,
                    $kelompok
                );
            }
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $daftar = Kelompok::withIdentitas()->get();

        foreach ($daftar as $item) {
            $item->nimKetua();
        }

        $this->assertCount(3, $daftar);
        $this->assertLessThanOrEqual(3, count(DB::getQueryLog()));

        DB::disableQueryLog();
    }

    public function test_scope_periode_aktif_hanya_mengambil_kelompok_periode_aktif(): void
    {
        $periodeLain = Periode::create([
            'nama_periode' => 'Genap 2025/2026',
            'tahun_akademik' => '2025/2026',
            'tanggal_mulai' => '2026-02-01',
            'tanggal_selesai' => '2026-07-31',
            'is_aktif' => false,
        ]);

        $kelompokLain = $this->buatKelompok($periodeLain, 9);

        $ids = Kelompok::periodeAktif()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->assertSame([$this->kelompok->id], $ids);
        $this->assertNotContains($kelompokLain->id, $ids);
    }

    public function test_scope_periode_aktif_kosong_jika_tidak_ada_periode_aktif(): void
    {
        Periode::query()->update(['is_aktif' => false]);

        $this->assertSame(0, Kelompok::periodeAktif()->count());
    }

    private function buatKelompok(Periode $periode, int $nomor = 1): Kelompok
    {
        $kelas = Kelas::create([
            'nama_kelas' => 'SIB 3'.$nomor,
            'periode_id' => $periode->id,
        ]);

        $dosen = Dosen::create([
            'nama' => 'Dosen Identitas '.$nomor,
            'email' => 'dosen.identitas.'.$periode->id.'.'.$nomor.'.'.uniqid().'@test.com',
            'password' => 'password123',
            'nidn' => '8'.substr(uniqid(), -8),
            'is_aktif' => true,
        ]);

        return Kelompok::create([
            'nama_kelompok' => 'Kelompok '.$periode->id.'-'.$nomor,
            'dosen_id' => $dosen->id,
            'kelas_id' => $kelas->id,
            'periode_id' => $periode->id,
            'tanggal_dibuat' => now()->toDateString(),
        ]);
    }

    private function buatMahasiswa(string $nim, string $nama): Mahasiswa
    {
        return Mahasiswa::create([
            'nama' => $nama,
            'email' => $nim.'@student.test.com',
            'password' => 'password123',
            'nim' => $nim,
            'is_aktif' => true,
        ]);
    }

    private function lampirkan(Mahasiswa $mahasiswa, bool $ketua = false, ?Kelompok $kelompok = null): void
    {
        $kelompok ??= $this->kelompok;

        $kelompok->mahasiswa()->attach($mahasiswa->id, [
            'role_label' => $ketua ? 'Ketua' : 'Anggota',
            'is_ketua' => $ketua,
        ]);
    }
}
