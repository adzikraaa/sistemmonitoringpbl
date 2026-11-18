<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $milestoneIds = DB::table('milestone')->pluck('id');
        $mataKuliahIds = DB::table('mata_kuliah')
            ->where('is_aktif', true)
            ->pluck('id');

        if ($milestoneIds->isEmpty() || $mataKuliahIds->isEmpty()) {
            return;
        }

        $now = now();
        $rows = [];

        foreach ($milestoneIds as $milestoneId) {
            foreach ($mataKuliahIds as $mataKuliahId) {
                $rows[] = [
                    'milestone_id' => $milestoneId,
                    'mata_kuliah_id' => $mataKuliahId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('milestone_mata_kuliah')->insertOrIgnore($chunk);
        }
    }

    public function down(): void
    {
        //
    }
};
