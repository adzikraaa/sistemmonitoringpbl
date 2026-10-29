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
            $table->string('judul')->after('tanggal')->default('Kegiatan');
            $table->unsignedTinyInteger('progress')->after('kegiatan')->default(0);
            $table->text('hasil')->after('progress')->nullable();
            $table->text('rencana')->after('kendala')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbook_milestone', function (Blueprint $table) {
            $table->dropColumn(['judul', 'progress', 'hasil', 'rencana']);
        });
    }
};
