<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestone_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained('milestone')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['milestone_id', 'mata_kuliah_id'],
                'milestone_mata_kuliah_pair_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestone_mata_kuliah');
    }
};
