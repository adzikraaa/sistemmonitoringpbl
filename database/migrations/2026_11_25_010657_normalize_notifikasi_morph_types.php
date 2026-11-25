<?php

use App\Models\Dosen;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->replaceRecipientType(Mahasiswa::class, 'mahasiswa');
        $this->replaceRecipientType(Dosen::class, 'dosen');
        $this->replaceRecipientType(Koordinator::class, 'koordinator');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->replaceRecipientType('mahasiswa', Mahasiswa::class);
        $this->replaceRecipientType('dosen', Dosen::class);
        $this->replaceRecipientType('koordinator', Koordinator::class);
    }

    private function replaceRecipientType(string $from, string $to): void
    {
        DB::table('notifikasi')
            ->where('penerima_type', $from)
            ->update(['penerima_type' => $to]);
    }
};
