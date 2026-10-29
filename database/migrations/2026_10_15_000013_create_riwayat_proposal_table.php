<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_proposal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposal')->cascadeOnDelete();
            $table->unsignedSmallInteger('versi');
            $table->string('judul');
            $table->string('file_proposal')->nullable();
            $table->string('status');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['proposal_id', 'versi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_proposal');
    }
};
