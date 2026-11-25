<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $assignmentIdsToKeep = DB::table('dosen_pengampu')
            ->selectRaw('MIN(id) AS id')
            ->groupBy('dosen_id')
            ->pluck('id');

        DB::table('dosen_pengampu')
            ->when($assignmentIdsToKeep->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $assignmentIdsToKeep))
            ->delete();

        Schema::table('dosen_pengampu', function (Blueprint $table): void {
            $table->unique('dosen_id', 'dosen_pengampu_dosen_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dosen_pengampu', function (Blueprint $table): void {
            $table->dropUnique('dosen_pengampu_dosen_unique');
        });
    }
};
