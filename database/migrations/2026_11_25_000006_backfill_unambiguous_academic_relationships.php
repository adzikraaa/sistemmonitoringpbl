<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /** Relasi dosen, mata kuliah, dan kelas harus dipilih koordinator melalui Penugasan Dosen. */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /** Data yang sudah terhubung tidak dilepas saat rollback untuk mencegah kehilangan relasi akademik. */
    }
};
