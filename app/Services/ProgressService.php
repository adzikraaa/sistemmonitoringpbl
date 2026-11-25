<?php

namespace App\Services;

use App\Models\Kelompok;
use App\Models\MataKuliah;
use App\Models\PenyelesaianMilestone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    public function untukKelompok(Kelompok $kelompok, ?MataKuliah $mataKuliah = null): array
    {
        if ($mataKuliah) {
            return $this->untukDaftarMataKuliah($kelompok, [$mataKuliah])[$mataKuliah->id]
                ?? $this->kosong();
        }

        $kelompok->load(['proposal', 'logbookMilestone']);

        return $this->untukDaftar([$kelompok])[$kelompok->id] ?? $this->kosong();
    }

    public function untukMataKuliah(Kelompok $kelompok, MataKuliah $mataKuliah): array
    {
        return $this->untukKelompok($kelompok, $mataKuliah);
    }

    /**
     * @return array<int, array{persen: float, selesai: int, total: int, punya_data: bool, detail?: array}>
     */
    public function untukDaftar(iterable $daftarKelompok): array
    {
        $kelompok = collect($daftarKelompok)->filter()->keyBy('id');

        if ($kelompok->isEmpty()) {
            return [];
        }

        $totalPeriode = $this->mappingCountPerPeriode(
            $kelompok->pluck('periode_id')->filter()->unique()->values()->all()
        );

        $selesaiPerKelompok = $this->selesaiCountPerKelompok($kelompok->keys()->all());

        $hasEagerLoaded = $kelompok->contains(
            fn ($item) => $item instanceof Kelompok && ($item->relationLoaded('proposal') || $item->relationLoaded('logbookMilestone'))
        );

        $proposalMap = collect();
        $logbookMap = collect();

        if ($hasEagerLoaded) {
            foreach ($kelompok as $id => $item) {
                if ($item instanceof Kelompok) {
                    if ($item->relationLoaded('proposal') && $item->proposal->isNotEmpty()) {
                        $proposalMap[$id] = $item->proposal->last();
                    }
                    if ($item->relationLoaded('logbookMilestone') && $item->logbookMilestone->isNotEmpty()) {
                        $logbookMap[$id] = $item->logbookMilestone;
                    }
                }
            }
        }

        $hasil = [];

        foreach ($kelompok as $id => $item) {
            $total = (int) ($totalPeriode[$item->periode_id] ?? 0);
            $selesai = (int) ($selesaiPerKelompok[$id.':'.$item->periode_id] ?? 0);

            $propModel = $proposalMap->get($id);
            $lbCollection = $logbookMap->get($id, collect());

            $propStatus = $propModel?->status;
            $lbDisetujuiCount = $lbCollection->filter(
                fn ($lb) => in_array(strtolower(trim((string) $lb->status)), ['disetujui', 'approved'], true)
            )->count();

            $hasil[$id] = $this->formatHasilKomposit(
                $selesai,
                $total,
                $propStatus,
                $lbDisetujuiCount,
                $propModel !== null,
                $lbCollection->isNotEmpty()
            );
        }

        return $hasil;
    }

    /**
     * @return array<int, array{persen: float, selesai: int, total: int, punya_data: bool}>
     */
    public function untukDaftarMataKuliah(Kelompok $kelompok, iterable $daftarMataKuliah): array
    {
        $mataKuliah = collect($daftarMataKuliah)->filter()->keyBy('id');

        if ($mataKuliah->isEmpty()) {
            return [];
        }

        $mataKuliahIds = $mataKuliah->keys()->all();

        $totalPerMataKuliah = DB::table('milestone_mata_kuliah as mmk')
            ->join('milestone as m', 'm.id', '=', 'mmk.milestone_id')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'mmk.mata_kuliah_id')
            ->whereIn('mmk.mata_kuliah_id', $mataKuliahIds)
            ->where('m.periode_id', $kelompok->periode_id)
            ->where('mk.is_aktif', true)
            ->selectRaw('mmk.mata_kuliah_id, COUNT(*) as total')
            ->groupBy('mmk.mata_kuliah_id')
            ->pluck('total', 'mata_kuliah_id');

        $statusSelesai = PenyelesaianMilestone::STATUS_SELESAI;

        $selesaiPerMataKuliah = DB::table('penyelesaian_milestone as pm')
            ->join('milestone as m', 'm.id', '=', 'pm.milestone_id')
            ->join('milestone_mata_kuliah as mmk', function ($join) {
                $join->on('mmk.milestone_id', '=', 'pm.milestone_id')
                    ->on('mmk.mata_kuliah_id', '=', 'pm.mata_kuliah_id');
            })
            ->join('mata_kuliah as mk', 'mk.id', '=', 'pm.mata_kuliah_id')
            ->join('pengumpulan_milestone as pg', function ($join) {
                $join->on('pg.kelompok_id', '=', 'pm.kelompok_id')
                    ->on('pg.milestone_id', '=', 'pm.milestone_id')
                    ->on('pg.mata_kuliah', '=', 'mk.slug');
            })
            ->whereIn('pm.mata_kuliah_id', $mataKuliahIds)
            ->where('pm.kelompok_id', $kelompok->id)
            ->where('m.periode_id', $kelompok->periode_id)
            ->where('mk.is_aktif', true)
            ->whereRaw(
                'LOWER(TRIM(pm.status)) = ?',
                [$statusSelesai]
            )
            ->selectRaw('pm.mata_kuliah_id, COUNT(DISTINCT pm.id) as selesai')
            ->groupBy('pm.mata_kuliah_id')
            ->pluck('selesai', 'mata_kuliah_id');

        $hasil = [];

        foreach ($mataKuliah as $id => $item) {
            $total = (int) ($totalPerMataKuliah[$id] ?? 0);
            $selesai = (int) ($selesaiPerMataKuliah[$id] ?? 0);

            $hasil[$id] = $this->formatHasil($selesai, $total);
        }

        return $hasil;
    }

    /**
     * @param  array<int, int>  $periodeIds
     * @return Collection<int|string, int>
     */
    private function mappingCountPerPeriode(array $periodeIds): Collection
    {
        if ($periodeIds === []) {
            return collect();
        }

        return DB::table('milestone_mata_kuliah as mmk')
            ->join('milestone as m', 'm.id', '=', 'mmk.milestone_id')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'mmk.mata_kuliah_id')
            ->whereIn('m.periode_id', $periodeIds)
            ->where('mk.is_aktif', true)
            ->selectRaw('m.periode_id, COUNT(*) as total')
            ->groupBy('m.periode_id')
            ->pluck('total', 'periode_id');
    }

    /**
     * @param  array<int, int>  $kelompokIds
     * @return Collection<string, int>
     */
    private function selesaiCountPerKelompok(array $kelompokIds): Collection
    {
        $statusSelesai = PenyelesaianMilestone::STATUS_SELESAI;
        $placeholders = '?';

        return DB::table('penyelesaian_milestone as pm')
            ->join('milestone as m', 'm.id', '=', 'pm.milestone_id')
            ->join('milestone_mata_kuliah as mmk', function ($join) {
                $join->on('mmk.milestone_id', '=', 'pm.milestone_id')
                    ->on('mmk.mata_kuliah_id', '=', 'pm.mata_kuliah_id');
            })
            ->join('mata_kuliah as mk', 'mk.id', '=', 'pm.mata_kuliah_id')
            ->join('pengumpulan_milestone as pg', function ($join) {
                $join->on('pg.kelompok_id', '=', 'pm.kelompok_id')
                    ->on('pg.milestone_id', '=', 'pm.milestone_id')
                    ->on('pg.mata_kuliah', '=', 'mk.slug');
            })
            ->whereIn('pm.kelompok_id', $kelompokIds)
            ->where('mk.is_aktif', true)
            ->whereRaw(
                'LOWER(TRIM(pm.status)) IN ('.$placeholders.')',
                [$statusSelesai]
            )
            ->selectRaw('pm.kelompok_id, m.periode_id, COUNT(DISTINCT pm.id) as selesai')
            ->groupBy('pm.kelompok_id', 'm.periode_id')
            ->get()
            ->mapWithKeys(fn ($baris) => [
                $baris->kelompok_id.':'.$baris->periode_id => (int) $baris->selesai,
            ]);
    }

    /**
     * Format hasil perhitungan komposit PBL:
     * - Proposal Proyek: 15%
     * - Logbook Kegiatan: 15% (target 8 bimbingan per semester)
     * - Milestone Mata Kuliah: 70%
     *
     * @return array{persen: float, selesai: int, total: int, punya_data: bool, detail?: array}
     */
    public function formatHasilKomposit(
        int $selesai,
        int $total,
        ?string $propStatus,
        int $lbDisetujuiCount,
        bool $hasProposal = false,
        bool $hasLogbook = false
    ): array {
        if (! $hasProposal && ! $hasLogbook) {
            return $this->formatHasil($selesai, $total);
        }

        $normPropStatus = strtolower(trim((string) $propStatus));
        $propPersen = match ($normPropStatus) {
            'disetujui', 'approved' => 15.0,
            'revisi', 'perlu-revisi' => 7.5,
            'diajukan', 'menunggu-review', 'menunggu review' => 5.0,
            default => 0.0,
        };

        $targetLogbook = 8;
        $lbPersen = round(min(1.0, $lbDisetujuiCount / $targetLogbook) * 15.0, 2);

        $msPersen = $total > 0 ? round(($selesai / $total) * 70.0, 2) : 0.0;

        $totalPersen = min(100.0, round($propPersen + $lbPersen + $msPersen, 2));

        return [
            'persen' => $totalPersen,
            'selesai' => $selesai,
            'total' => $total,
            'punya_data' => $total > 0 || $hasProposal || $hasLogbook,
            'detail' => [
                'proposal_persen' => $propPersen,
                'logbook_persen' => $lbPersen,
                'milestone_persen' => $msPersen,
                'logbook_disetujui' => $lbDisetujuiCount,
                'target_logbook' => $targetLogbook,
            ],
        ];
    }

    /**
     * @return array{persen: float, selesai: int, total: int, punya_data: bool}
     */
    private function formatHasil(int $selesai, int $total): array
    {
        return [
            'persen' => $total > 0 ? round($selesai / $total * 100, 2) : 0.0,
            'selesai' => $selesai,
            'total' => $total,
            'punya_data' => $total > 0,
        ];
    }

    /**
     * @return array{persen: float, selesai: int, total: int, punya_data: bool}
     */
    private function kosong(): array
    {
        return ['persen' => 0.0, 'selesai' => 0, 'total' => 0, 'punya_data' => false];
    }
}
