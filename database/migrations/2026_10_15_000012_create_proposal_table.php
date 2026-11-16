<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kelompok')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->text('latar_belakang');
            $table->text('tujuan');
            $table->text('metode');
            $table->string('file_proposal')->nullable();
            $table->string('status')->default('diajukan');
            $table->text('catatan_revisi')->nullable();
            $table->date('tanggal_pengajuan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal');
    }
};
