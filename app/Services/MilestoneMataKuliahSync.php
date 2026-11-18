<?php

namespace App\Services;

use App\Models\MataKuliah;
use App\Models\Milestone;
use Illuminate\Support\Facades\DB;

class MilestoneMataKuliahSync
{
    public function forMilestone(Milestone $milestone): void
    {
        $this->sync(
            [(int) $milestone->id],
            MataKuliah::where('is_aktif', true)->pluck('id')->all()
        );
    }

    public function forMataKuliah(MataKuliah $mataKuliah): void
    {
        $this->sync(
            Milestone::pluck('id')->all(),
            [(int) $mataKuliah->id]
        );
    }

    public function forSemua(): void
    {
        $this->sync(
            Milestone::pluck('id')->all(),
            MataKuliah::where('is_aktif', true)->pluck('id')->all()
        );
    }

    /**
     * @param  array<int, int>  $milestoneIds
     * @param  array<int, int>  $mataKuliahIds
     */
    private function sync(array $milestoneIds, array $mataKuliahIds): void
    {
        if ($milestoneIds === [] || $mataKuliahIds === []) {
            return;
        }

        DB::transaction(function () use ($milestoneIds, $mataKuliahIds) {
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
        });
    }
}
