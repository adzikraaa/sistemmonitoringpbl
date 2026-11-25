<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->rebuildMapping(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->rebuildMapping(true);
    }

    private function rebuildMapping(bool $limitToThreePerCourse): void
    {
        DB::transaction(function () use ($limitToThreePerCourse): void {
            $now = now();
            $rows = [];
            $mataKuliahIds = DB::table('mata_kuliah')
                ->where('is_aktif', true)
                ->orderBy('id')
                ->pluck('id')
                ->values();

            $milestoneIds = DB::table('milestone')
                ->orderBy('periode_id')
                ->orderBy('urutan')
                ->orderBy('id')
                ->pluck('id')
                ->values();

            foreach ($mataKuliahIds as $mataKuliahIndex => $mataKuliahId) {
                $mappedMilestoneIds = $limitToThreePerCourse
                    ? $milestoneIds->slice($mataKuliahIndex * 3, 3)
                    : $milestoneIds;

                foreach ($mappedMilestoneIds as $milestoneId) {
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
