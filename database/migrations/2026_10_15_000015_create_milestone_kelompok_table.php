<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_kelompok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained('milestone')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->constrained('kelompok')->cascadeOnDelete();
            $table->string('status')->default('belum dimulai');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['milestone_id', 'kelompok_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_kelompok');
    }
};
