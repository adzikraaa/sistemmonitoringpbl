<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('anggota_kelompok') && ! Schema::hasColumn('anggota_kelompok', 'role')) {
            Schema::table('anggota_kelompok', function (Blueprint $table) {
                $table->string('role')->nullable()->after('mahasiswa_id');
            });
        }

        if (Schema::hasTable('kelompok_pbl_mahasiswa') && ! Schema::hasColumn('kelompok_pbl_mahasiswa', 'role')) {
            Schema::table('kelompok_pbl_mahasiswa', function (Blueprint $table) {
                $table->string('role')->nullable()->after('mahasiswa_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('anggota_kelompok') && Schema::hasColumn('anggota_kelompok', 'role')) {
            Schema::table('anggota_kelompok', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        if (Schema::hasTable('kelompok_pbl_mahasiswa') && Schema::hasColumn('kelompok_pbl_mahasiswa', 'role')) {
            Schema::table('kelompok_pbl_mahasiswa', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};