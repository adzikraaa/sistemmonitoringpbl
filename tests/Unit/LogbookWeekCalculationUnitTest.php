<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LogbookWeekCalculationUnitTest extends TestCase
{
    /**
     * Helper menghitung minggu logbook sesuai algoritma MahasiswaController.
     */
    private function hitungMinggu(string $tanggalLogbook, ?int $earliestTimestamp): int
    {
        $diffDays = ($earliestTimestamp !== null)
            ? (strtotime($tanggalLogbook) - $earliestTimestamp) / 86400
            : 0;

        return (int) floor($diffDays / 7) + 1;
    }

    /**
     * Skenario hari pertama (hari ke-0) berada pada Minggu ke-1.
     */
    public function test_tanggal_sama_dengan_earliest_menghasilkan_minggu_kesatu(): void
    {
        $earliest = strtotime('2026-04-28');
        $minggu = $this->hitungMinggu('2026-04-28', $earliest);

        $this->assertSame(1, $minggu);
    }

    /**
     * Skenario rentang hari ke 1-6 masih berada pada Minggu ke-1.
     */
    public function test_hari_ke_enam_masih_berada_pada_minggu_kesatu(): void
    {
        $earliest = strtotime('2026-04-28');
        // 6 hari setelah 28 April = 4 Mei
        $minggu = $this->hitungMinggu('2026-05-04', $earliest);

        $this->assertSame(1, $minggu);
    }

    /**
     * Skenario tepat hari ke-7 berganti ke Minggu ke-2.
     */
    public function test_tepat_tujuh_hari_berganti_ke_minggu_kedua(): void
    {
        $earliest = strtotime('2026-04-28');
        // 7 hari setelah 28 April = 5 Mei
        $minggu = $this->hitungMinggu('2026-05-05', $earliest);

        $this->assertSame(2, $minggu);
    }

    /**
     * Skenario 14 hari kemudian berganti ke Minggu ke-3.
     */
    public function test_empat_belas_hari_berganti_ke_minggu_ketiga(): void
    {
        $earliest = strtotime('2026-04-28');
        // 14 hari setelah 28 April = 12 Mei
        $minggu = $this->hitungMinggu('2026-05-12', $earliest);

        $this->assertSame(3, $minggu);
    }

    /**
     * Skenario batas: Jika earliestTimestamp null, default ke Minggu ke-1.
     */
    public function test_earliest_null_menghasilkan_minggu_kesatu(): void
    {
        $minggu = $this->hitungMinggu('2026-05-12', null);

        $this->assertSame(1, $minggu);
    }
}
