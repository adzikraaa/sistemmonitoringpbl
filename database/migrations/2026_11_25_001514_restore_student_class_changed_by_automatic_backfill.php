<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('mahasiswa')
            ->where('nim', '244107060003')
            ->where('updated_at', '2026-11-25 00:01:30')
            ->update([
                'kelas_id' => null,
                'updated_at' => '2026-10-14 15:32:54',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /** Relasi kelas yang salah tidak dikembalikan saat rollback. */
    }
};
