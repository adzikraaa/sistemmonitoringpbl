<?php

namespace Tests\Feature;

use App\Models\Koordinator;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoordinatorPeriodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_periode_baru_dari_riwayat_dapat_diaktifkan(): void
    {
        $koordinator = $this->buatKoordinator();
        $periodeLama = $this->buatPeriode('Ganjil', '2025/2026', true);

        $this->actingAs($koordinator, 'koordinator')
            ->post(route('koordinator.periode.store'), [
                'nama_periode' => 'Genap',
                'tahun_akademik' => '2026/2027',
                'tanggal_mulai' => '2027-02-01',
                'tanggal_selesai' => '2027-07-31',
            ])
            ->assertRedirect(route('koordinator.periode.index'));

        $periodeBaru = Periode::query()->where('tahun_akademik', '2026/2027')->firstOrFail();
        $this->assertFalse($periodeBaru->is_aktif);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->post(route('koordinator.periode.activate', $periodeBaru));

        $response->assertRedirect(route('koordinator.periode.index'));
        $response->assertSessionHas('success');
        $this->assertTrue($periodeBaru->fresh()->is_aktif);
        $this->assertFalse($periodeLama->fresh()->is_aktif);
        $this->assertSame(1, Periode::query()->where('is_aktif', true)->count());
    }

    public function test_periode_aktif_dapat_dinonaktifkan(): void
    {
        $koordinator = $this->buatKoordinator();
        $periode = $this->buatPeriode('Ganjil', '2026/2027', true);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->post(route('koordinator.periode.deactivate', $periode));

        $response->assertRedirect(route('koordinator.periode.index'));
        $this->assertFalse($periode->fresh()->is_aktif);
    }

    public function test_halaman_tidak_menampilkan_kartu_acuan_dan_angka_fallback(): void
    {
        $koordinator = $this->buatKoordinator();
        $this->buatPeriode('Ganjil', '2026/2027', true);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->get(route('koordinator.periode.index'));

        $response->assertOk();
        $response->assertDontSee('Satu periode, satu acuan');
        $response->assertDontSee('72 mahasiswa');
        $response->assertDontSee('Tidak ada periode aktif yang tumpang tindih');
    }

    public function test_nama_semester_lama_tidak_menggandakan_tahun_akademik(): void
    {
        $koordinator = $this->buatKoordinator();
        Periode::create([
            'nama_periode' => 'Ganjil 2026/2027',
            'tahun_akademik' => '2026/2027',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->get(route('koordinator.periode.index'));

        $response->assertOk();
        $response->assertSee('Ganjil 2026/2027');
        $response->assertDontSee('Ganjil 2026/2027 2026/2027');
        $response->assertSee('<option value="Ganjil" selected>Ganjil</option>', false);
    }

    public function test_form_aktifkan_periode_mengirim_method_post_tanpa_method_spoofing(): void
    {
        $koordinator = $this->buatKoordinator();
        $this->buatPeriode('Ganjil', '2025/2026', true);
        $periodeNonaktif = $this->buatPeriode('Genap', '2026/2027', false);

        $response = $this->actingAs($koordinator, 'koordinator')
            ->get(route('koordinator.periode.index'));

        $response->assertOk();
        $response->assertSeeInOrder([
            route('koordinator.periode.activate', $periodeNonaktif),
            'Aktifkan',
        ], false);
        $response->assertDontSee('name="_method" value="PATCH"', false);
    }

    public function test_menu_utama_master_data_mengarah_ke_mata_kuliah(): void
    {
        $koordinator = $this->buatKoordinator();

        $response = $this->actingAs($koordinator, 'koordinator')
            ->get(route('koordinator.periode.index'));

        $response->assertOk();
        $response->assertSeeInOrder([
            'href="'.route('koordinator.master-data.mata-kuliah').'"',
            '<span>Master Data</span>',
        ], false);
    }

    private function buatKoordinator(): Koordinator
    {
        return Koordinator::create([
            'nama' => 'Koordinator Periode',
            'email' => 'koordinator-periode@test.com',
            'password' => 'password123',
            'nidn' => '900000010',
        ]);
    }

    private function buatPeriode(string $nama, string $tahun, bool $aktif): Periode
    {
        return Periode::create([
            'nama_periode' => $nama,
            'tahun_akademik' => $tahun,
            'tanggal_mulai' => $tahun === '2025/2026' ? '2025-09-01' : '2026-09-01',
            'tanggal_selesai' => $tahun === '2025/2026' ? '2026-01-31' : '2027-01-31',
            'is_aktif' => $aktif,
        ]);
    }
}
