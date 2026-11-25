<?php

namespace App\Services;

use App\Models\MataKuliah;
use App\Models\Milestone;
use Illuminate\Support\Facades\DB;

class MilestoneMataKuliahSync
{
    public function forMilestone(Milestone $milestone): void
    {
        $this->forSemua();
    }

    public function forMataKuliah(MataKuliah $mataKuliah): void
    {
        $this->forSemua();
    }

    public function forSemua(): void
    {
        DB::transaction(function () {
            $now = now();
            $rows = [];
            $mataKuliahIds = DB::table('mata_kuliah')
                ->where('is_aktif', true)
                ->orderBy('id')
                ->pluck('id')
                ->all();
            $milestoneIds = DB::table('milestone')->pluck('id')->all();

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
}
