<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulan_milestone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained('milestone')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->constrained('kelompok')->cascadeOnDelete();
            $table->foreignId('diunggah_oleh')->nullable()
                ->constrained('mahasiswa')->nullOnDelete();
            $table->string('mata_kuliah')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('link_bukti')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status')->default('diajukan');
            $table->timestamps();

            $table->unique(['kelompok_id', 'milestone_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_milestone');
    }
};
