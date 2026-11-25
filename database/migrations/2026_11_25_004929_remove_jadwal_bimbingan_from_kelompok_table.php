<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kelompok', function (Blueprint $table): void {
            $table->dropColumn('jadwal_bimbingan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelompok', function (Blueprint $table): void {
            $table->string('jadwal_bimbingan')->nullable();
        });
    }
};
