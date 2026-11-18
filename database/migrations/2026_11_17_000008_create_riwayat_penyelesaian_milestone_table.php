<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_penyelesaian_milestone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyelesaian_milestone_id')
                ->constrained('penyelesaian_milestone')
                ->cascadeOnDelete();
            $table->foreignId('dosen_id')->nullable()
                ->constrained('dosen')->nullOnDelete();
            $table->string('status_lama', 20)->nullable();
            $table->string('status_baru', 20);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(
                ['penyelesaian_milestone_id', 'created_at'],
                'riwayat_penyelesaian_milestone_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_penyelesaian_milestone');
    }
};
