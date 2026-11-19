<?php

namespace Tests\Unit;

use App\Models\PenyelesaianMilestone;
use PHPUnit\Framework\TestCase;

class PenyelesaianMilestoneUnitTest extends TestCase
{
    /**
     * Uji nilai konstanta status resmi sistem.
     */
    public function test_konstanta_status_memiliki_definisi_yang_benar(): void
    {
        $this->assertSame('belum_dimulai', PenyelesaianMilestone::STATUS_BELUM_DIMULAI);
        $this->assertSame('berjalan', PenyelesaianMilestone::STATUS_BERJALAN);
        $this->assertSame('selesai', PenyelesaianMilestone::STATUS_SELESAI);

        $this->assertEquals([
            'belum_dimulai',
            'berjalan',
            'selesai',
        ], PenyelesaianMilestone::STATUSES);
    }

    /**
     * Uji fungsi normalizeStatus dengan berbagai variasi input.
     */
    public function test_normalize_status_membersihkan_whitespace_dan_huruf_besar(): void
    {
        $this->assertSame('selesai', PenyelesaianMilestone::normalizeStatus('SELESAI'));
        $this->assertSame('selesai', PenyelesaianMilestone::normalizeStatus('  selesai  '));
        $this->assertSame('berjalan', PenyelesaianMilestone::normalizeStatus('BerJalan'));
        $this->assertSame('belum_dimulai', PenyelesaianMilestone::normalizeStatus(' Belum_Dimulai '));
    }

    /**
     * Uji normalizeStatus terhadap input null dan string kosong.
     */
    public function test_normalize_status_dengan_input_null_atau_kosong(): void
    {
        $this->assertSame('', PenyelesaianMilestone::normalizeStatus(null));
        $this->assertSame('', PenyelesaianMilestone::normalizeStatus(''));
        $this->assertSame('', PenyelesaianMilestone::normalizeStatus('   '));
    }

    /**
     * Uji method isSelesai ketika status adalah selesai.
     */
    public function test_is_selesai_bernilai_true_ketika_status_selesai(): void
    {
        $model = new PenyelesaianMilestone();
        $model->status = 'selesai';

        $this->assertTrue($model->isSelesai());
    }

    /**
     * Uji method isSelesai menangani status huruf besar / whitespace.
     */
    public function test_is_selesai_toleran_terhadap_huruf_besar_dan_spasi(): void
    {
        $model = new PenyelesaianMilestone();
        $model->status = '  SELESAI  ';

        $this->assertTrue($model->isSelesai());
    }

    /**
     * Uji method isSelesai bernilai false ketika status belum selesai.
     */
    public function test_is_selesai_bernilai_false_ketika_status_lain(): void
    {
        $model = new PenyelesaianMilestone();

        $model->status = 'belum_dimulai';
        $this->assertFalse($model->isSelesai());

        $model->status = 'berjalan';
        $this->assertFalse($model->isSelesai());

        $model->status = null;
        $this->assertFalse($model->isSelesai());

        $model->status = 'ditolak';
        $this->assertFalse($model->isSelesai());
    }
}
