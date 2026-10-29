<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->string('prodi')->nullable()->after('nama_kelas');
            $table->foreignId('periode_id')->nullable()->after('prodi')
                ->constrained('periode')->nullOnDelete();
            $table->boolean('is_aktif')->default(true)->after('periode_id');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['periode_id']);
            $table->dropColumn(['prodi', 'periode_id', 'is_aktif']);
        });
    }
};
