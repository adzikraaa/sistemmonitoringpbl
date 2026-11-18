<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_milestone', function (Blueprint $table) {
            $table->dropForeign(['milestone_id']);
        });

        Schema::table('logbook_milestone', function (Blueprint $table) {
            $table->unsignedBigInteger('milestone_id')->nullable()->change();
        });

        Schema::table('logbook_milestone', function (Blueprint $table) {
            $table->foreign('milestone_id')->references('id')->on('milestone')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $nullCount = DB::table('logbook_milestone')
            ->whereNull('milestone_id')
            ->count();

        if ($nullCount > 0) {
            throw new RuntimeException(
                "Rollback dibatalkan: ada {$nullCount} logbook tanpa milestone "
                .'(milestone_id NULL). Lengkapi data tersebut secara manual sebelum rollback; '
                .'migration tidak akan menghapus, mengubah, atau mengisi data logbook.'
            );
        }

        Schema::table('logbook_milestone', function (Blueprint $table) {
            $table->dropForeign(['milestone_id']);
        });

        Schema::table('logbook_milestone', function (Blueprint $table) {
            $table->unsignedBigInteger('milestone_id')->nullable(false)->change();
        });

        Schema::table('logbook_milestone', function (Blueprint $table) {
            $table->foreign('milestone_id')->references('id')->on('milestone')->restrictOnDelete();
        });
    }
};
