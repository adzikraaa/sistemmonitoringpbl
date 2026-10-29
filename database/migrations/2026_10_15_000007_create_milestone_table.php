<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode')->restrictOnDelete();
            $table->string('kode', 10);
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->unsignedTinyInteger('urutan');
            $table->date('deadline')->nullable();
            $table->timestamps();

            $table->unique(['periode_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone');
    }
};
