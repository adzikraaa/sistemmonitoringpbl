<?php

namespace Tests\Unit;

use App\Models\AnggotaKelompok;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use Illuminate\Support\Collection;
use Tests\TestCase;

class KelompokBusinessLogicUnitTest extends TestCase
{
    /**
     * Uji penentuan ketua kelompok dari relasi in-memory.
     */
    public function test_ketua_ditemukan_dari_relasi_anggota_kelompok(): void
    {
        $kelompok = new Kelompok();

        $anggota1 = new AnggotaKelompok(['role_label' => 'Anggota']);
        $anggota1->id = 1;
        $anggota1->is_ketua = false;

        $anggota2 = new AnggotaKelompok(['role_label' => 'Ketua']);
        $anggota2->id = 2;
        $anggota2->is_ketua = true;

        $kelompok->setRelation('anggotaKelompok', collect([$anggota1, $anggota2]));

        $ketua = $kelompok->ketua();

        $this->assertNotNull($ketua);
        $this->assertSame(2, $ketua->id);
        $this->assertTrue((bool) $ketua->is_ketua);
    }

    /**
     * Uji jika kelompok belum memiliki ketua, ketua() mengembalikan null.
     */
    public function test_ketua_mengembalikan_null_jika_tidak_ada_yang_ditandai_ketua(): void
    {
        $kelompok = new Kelompok();

        $anggota1 = new AnggotaKelompok(['role_label' => 'Anggota']);
        $anggota1->id = 1;
        $anggota1->is_ketua = false;

        $kelompok->setRelation('anggotaKelompok', collect([$anggota1]));

        $this->assertNull($kelompok->ketua());
        $this->assertNull($kelompok->nimKetua());
    }

    /**
     * Uji pengambilan NIM ketua kelompok secara berantai (chaining).
     */
    public function test_nim_ketua_mengembalikan_nim_mahasiswa_ketua(): void
    {
        $kelompok = new Kelompok();

        $mahasiswa = new Mahasiswa(['nim' => '244107060044', 'nama' => 'Adzikra']);

        $anggota = new AnggotaKelompok();
        $anggota->id = 1;
        $anggota->is_ketua = true;
        $anggota->setRelation('mahasiswa', $mahasiswa);

        $kelompok->setRelation('anggotaKelompok', collect([$anggota]));

        $this->assertSame('244107060044', $kelompok->nimKetua());
    }

    /**
     * Uji pengurutan anggota kelompok: ketua selalu di posisi pertama, diikuti anggota berdasarkan ID ascending.
     */
    public function test_anggota_terurut_menempatkan_ketua_di_awal_lalu_berdasarkan_id(): void
    {
        $kelompok = new Kelompok();

        $anggotaB = new AnggotaKelompok(['role_label' => 'Anggota']);
        $anggotaB->id = 20;
        $anggotaB->is_ketua = false;

        $anggotaA = new AnggotaKelompok(['role_label' => 'Anggota']);
        $anggotaA->id = 5;
        $anggotaA->is_ketua = false;

        $ketua = new AnggotaKelompok(['role_label' => 'Ketua']);
        $ketua->id = 15;
        $ketua->is_ketua = true;

        // Pasang dalam urutan acak
        $kelompok->setRelation('anggotaKelompok', collect([$anggotaB, $anggotaA, $ketua]));

        $terurut = $kelompok->anggotaTerurut();

        $this->assertInstanceOf(Collection::class, $terurut);
        $this->assertCount(3, $terurut);

        // Posisi 0 harus ketua (is_ketua = true)
        $this->assertSame(15, $terurut[0]->id);
        $this->assertTrue((bool) $terurut[0]->is_ketua);

        // Posisi 1 harus anggota dengan ID lebih kecil (id = 5)
        $this->assertSame(5, $terurut[1]->id);

        // Posisi 2 harus anggota dengan ID lebih besar (id = 20)
        $this->assertSame(20, $terurut[2]->id);
    }
}
