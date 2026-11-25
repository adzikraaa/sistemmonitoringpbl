<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $now = now();
            $rows = [];
            $mataKuliahIds = DB::table('mata_kuliah')
                ->where('is_aktif', true)
                ->orderBy('id')
                ->pluck('id')
                ->all();
            $milestonesByPeriode = DB::table('milestone')
                ->orderBy('periode_id')
                ->orderBy('urutan')
                ->orderBy('id')
                ->get(['id', 'periode_id'])
                ->groupBy('periode_id');

            foreach ($milestonesByPeriode as $milestones) {
                foreach ($mataKuliahIds as $mataKuliahIndex => $mataKuliahId) {
                    $milestones->slice($mataKuliahIndex * 3, 3)->each(function ($milestone) use (&$rows, $mataKuliahId, $now) {
                        $rows[] = [
                            'milestone_id' => $milestone->id,
                            'mata_kuliah_id' => $mataKuliahId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    });
                }
            }

            DB::table('milestone_mata_kuliah')->delete();

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('milestone_mata_kuliah')->insert($chunk);
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $now = now();
            $rows = [];
            $milestoneIds = DB::table('milestone')->pluck('id')->all();
            $mataKuliahIds = DB::table('mata_kuliah')
                ->where('is_aktif', true)
                ->pluck('id')
                ->all();

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

            DB::table('milestone_mata_kuliah')->delete();

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('milestone_mata_kuliah')->insert($chunk);
            }
        });
    }
};
