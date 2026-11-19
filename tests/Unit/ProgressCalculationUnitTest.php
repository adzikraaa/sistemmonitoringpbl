<?php

namespace Tests\Unit;

use App\Services\ProgressService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ProgressCalculationUnitTest extends TestCase
{
    private ProgressService $service;
    private ReflectionMethod $formatHasilMethod;
    private ReflectionMethod $kosongMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProgressService();

        $this->formatHasilMethod = new ReflectionMethod(ProgressService::class, 'formatHasil');
        $this->kosongMethod = new ReflectionMethod(ProgressService::class, 'kosong');
    }

    private function invokeFormatHasil(int $selesai, int $total): array
    {
        return $this->formatHasilMethod->invoke($this->service, $selesai, $total);
    }

    private function invokeKosong(): array
    {
        return $this->kosongMethod->invoke($this->service);
    }

    /**
     * Skenario normal: Perhitungan persentase bilangan bulat.
     */
    public function test_format_hasil_menghitung_persentase_normal(): void
    {
        $hasil = $this->invokeFormatHasil(5, 10);

        $this->assertSame(50.0, $hasil['persen']);
        $this->assertSame(5, $hasil['selesai']);
        $this->assertSame(10, $hasil['total']);
        $this->assertTrue($hasil['punya_data']);
    }

    /**
     * Skenario 100%: Seluruh target selesai.
     */
    public function test_format_hasil_selesai_semua_menghasilkan_seratus_persen(): void
    {
        $hasil = $this->invokeFormatHasil(8, 8);

        $this->assertSame(100.0, $hasil['persen']);
        $this->assertSame(8, $hasil['selesai']);
        $this->assertSame(8, $hasil['total']);
        $this->assertTrue($hasil['punya_data']);
    }

    /**
     * Skenario 0%: Belum ada yang selesai dari target yang tersedia.
     */
    public function test_format_hasil_nol_selesai_dari_target_tersedia(): void
    {
        $hasil = $this->invokeFormatHasil(0, 5);

        $this->assertSame(0.0, $hasil['persen']);
        $this->assertSame(0, $hasil['selesai']);
        $this->assertSame(5, $hasil['total']);
        $this->assertTrue($hasil['punya_data']);
    }

    /**
     * Skenario batas: Total 0 tidak boleh menyebabkan division by zero.
     */
    public function test_format_hasil_total_nol_menghasilkan_nol_persen_dan_tanpa_data(): void
    {
        $hasil = $this->invokeFormatHasil(0, 0);

        $this->assertSame(0.0, $hasil['persen']);
        $this->assertSame(0, $hasil['selesai']);
        $this->assertSame(0, $hasil['total']);
        $this->assertFalse($hasil['punya_data']);
    }

    /**
     * Skenario pembulatan: Pecahan dibulatkan 2 desimal (1 dari 3 = 33.33%).
     */
    public function test_format_hasil_pembulatan_dua_desimal(): void
    {
        $hasil1 = $this->invokeFormatHasil(1, 3);
        $this->assertSame(33.33, $hasil1['persen']);

        $hasil2 = $this->invokeFormatHasil(2, 3);
        $this->assertSame(66.67, $hasil2['persen']);

        $hasil3 = $this->invokeFormatHasil(1, 7);
        $this->assertSame(14.29, $hasil3['persen']);
    }

    /**
     * Skenario kosong: Struktur default saat tidak ada data kelompok atau mata kuliah.
     */
    public function test_kosong_mengembalikan_struktur_default_yang_benar(): void
    {
        $hasil = $this->invokeKosong();

        $this->assertSame(0.0, $hasil['persen']);
        $this->assertSame(0, $hasil['selesai']);
        $this->assertSame(0, $hasil['total']);
        $this->assertFalse($hasil['punya_data']);
    }
}
