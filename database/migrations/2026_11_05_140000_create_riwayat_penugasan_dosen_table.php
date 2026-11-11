<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan dosen_id di tabel kelompok dapat bernilai null untuk kelompok yang belum ditugaskan
        Schema::table('kelompok', function (Blueprint $table) {
            $table->foreignId('dosen_id')->nullable()->change();
            if (!Schema::hasColumn('kelompok', 'judul_proyek')) {
                $table->string('judul_proyek')->nullable()->after('nama_kelompok');
            }
        });

        // Tabel riwayat penugasan dosen pembimbing ke kelompok PBL
        Schema::create('riwayat_penugasan_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kelompok')->cascadeOnDelete();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('dosen_sebelumnya_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->foreignId('koordinator_id')->nullable()->constrained('koordinator')->nullOnDelete();
            $table->string('aksi')->default('penugasan'); // 'penugasan_baru', 'perubahan_dosen', 'pembatalan'
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_penugasan_dosen');

        Schema::table('kelompok', function (Blueprint $table) {
            if (Schema::hasColumn('kelompok', 'judul_proyek')) {
                $table->dropColumn('judul_proyek');
            }
        });
    }
};

