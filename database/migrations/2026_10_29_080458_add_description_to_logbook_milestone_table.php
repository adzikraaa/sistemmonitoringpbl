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
        Schema::table('logbook_milestone', function (Blueprint $table) {
            $table->text('deskripsi')->after('judul')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbook_milestone', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
        });
    }
};
