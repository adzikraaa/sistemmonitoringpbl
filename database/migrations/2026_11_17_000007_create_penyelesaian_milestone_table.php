<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyelesaian_milestone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kelompok')->cascadeOnDelete();
            $table->foreignId('milestone_id')->constrained('milestone')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->restrictOnDelete();
            $table->string('status', 20)->default('belum_dimulai');
            $table->text('catatan')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()
                ->constrained('dosen')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();

            $table->unique(
                ['kelompok_id', 'milestone_id', 'mata_kuliah_id'],
                'penyelesaian_milestone_identitas_unique'
            );
            $table->index(
                ['mata_kuliah_id', 'status'],
                'penyelesaian_milestone_matkul_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyelesaian_milestone');
    }
};
