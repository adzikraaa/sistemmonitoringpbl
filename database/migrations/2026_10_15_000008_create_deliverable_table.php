<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliverable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained('milestone')->cascadeOnDelete();
            $table->string('nama');
            $table->unsignedTinyInteger('urutan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliverable');
    }
};
