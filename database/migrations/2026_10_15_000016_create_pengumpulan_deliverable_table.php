<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulan_deliverable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deliverable_id')->constrained('deliverable')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->constrained('kelompok')->cascadeOnDelete();
            $table->foreignId('diunggah_oleh')->nullable()
                ->constrained('mahasiswa')->nullOnDelete();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status')->default('belum dimulai');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['deliverable_id', 'kelompok_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_deliverable');
    }
};
