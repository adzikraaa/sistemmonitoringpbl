<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Milestone;
use App\Models\PenyelesaianMilestone;
use App\Models\Periode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $periodeAktif = Periode::query()->where('is_aktif', true)
            ->orderByDesc('tanggal_mulai')->orderByDesc('id')->first();

        $kelasList = Kelas::query()
            ->where('is_aktif', true)
            ->when($periodeAktif, fn (Builder $query) => $query->where('periode_id', $periodeAktif->id))
            ->when(! $periodeAktif, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->orderBy('nama_kelas')->get();

        $selectedKelasId = $request->integer('kelas_id') ?: null;
        if ($selectedKelasId !== null && ! $kelasList->contains('id', $selectedKelasId)) {
            $selectedKelasId = null;
        }

        $kelompokQuery = Kelompok::query()
            ->when($periodeAktif, fn (Builder $query) => $query->where('periode_id', $periodeAktif->id))
            ->when(! $periodeAktif, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->when($selectedKelasId, fn (Builder $query) => $query->where('kelas_id', $selectedKelasId));
        $kelompokIds = (clone $kelompokQuery)->pluck('id');
        $totalKelompok = $kelompokIds->count();
        $kelasCount = $selectedKelasId === null ? $kelasList->count() : 1;
        $kelasIdsDalamLingkup = $selectedKelasId ? collect([$selectedKelasId]) : $kelasList->pluck('id');
        $mahasiswaCount = DB::table('mahasiswa')->whereIn('kelas_id', $kelasIdsDalamLingkup)
            ->where('is_aktif', true)->count();
        $dosenPembimbingCount = (clone $kelompokQuery)->whereNotNull('dosen_id')
            ->distinct()->count('dosen_id');
        $kelompokDitugaskanCount = (clone $kelompokQuery)->whereNotNull('dosen_id')->count();

        $milestones = Milestone::query()
            ->when($periodeAktif, fn (Builder $query) => $query->where('periode_id', $periodeAktif->id))
            ->when(! $periodeAktif, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->withCount(['mataKuliah' => fn ($query) => $query->where('is_aktif', true)])
            ->orderBy('urutan')->orderBy('id')->get();
        $selesaiPerMilestone = $this->selesaiPerMilestone($kelompokIds, $milestones->pluck('id'));
        $palette = ['#4A3222', '#8B6240', '#B08060', '#C49A78', '#D6C4B2'];
        $milestoneChartData = $milestones->map(function (Milestone $milestone, int $index) use ($selesaiPerMilestone, $totalKelompok, $palette): array {
            $target = $totalKelompok * (int) $milestone->mata_kuliah_count;
            $selesai = (int) ($selesaiPerMilestone[$milestone->id] ?? 0);

            return [
                'id' => $milestone->id,
                'label' => $milestone->judul,
                'val' => $selesai,
                'max' => $target,
                'pct' => $target > 0 ? round($selesai / $target * 100, 1) : 0.0,
                'remaining' => max(0, $target - $selesai),
                'deadline' => $milestone->deadline,
                'mata_kuliah_count' => (int) $milestone->mata_kuliah_count,
                'color' => $palette[$index % count($palette)],
            ];
        })->all();

        $totalSelesaiSemua = (int) collect($milestoneChartData)->sum('val');
        $totalTargetSemua = (int) collect($milestoneChartData)->sum('max');
        $pctCapaian = $totalTargetSemua > 0 ? round($totalSelesaiSemua / $totalTargetSemua * 100, 1) : 0.0;

        $rekapQuery = Kelas::query()
            ->with([
                'dosenPengampu.dosen:id,nama',
                'kelompok:id,kelas_id',
                'mahasiswa' => fn ($query) => $query->select('id', 'kelas_id')->where('is_aktif', true),
            ])
            ->where('is_aktif', true)
            ->when($periodeAktif, fn (Builder $query) => $query->where('periode_id', $periodeAktif->id))
            ->when(! $periodeAktif, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->when($selectedKelasId, fn (Builder $query) => $query->whereKey($selectedKelasId))
            ->orderBy('nama_kelas');
        $rekapKelas = $rekapQuery->paginate(5)->withQueryString();
        $mappingCount = (int) $milestones->sum('mata_kuliah_count');
        $selesaiPerKelas = $this->selesaiPerKelas($rekapKelas->getCollection()->pluck('id'), $milestones->pluck('id'));

        $rekapKelas->getCollection()->transform(function (Kelas $kelas) use ($mappingCount, $selesaiPerKelas): object {
            $dosenNames = $kelas->dosenPengampu->pluck('dosen.nama')->filter()->unique()->values();
            $kelompokCount = $kelas->kelompok->count();
            $targetCount = $kelompokCount * $mappingCount;
            $selesaiCount = (int) ($selesaiPerKelas[$kelas->id] ?? 0);
            $capaianPct = $targetCount > 0 ? round($selesaiCount / $targetCount * 100, 1) : 0.0;

            return (object) [
                'id' => $kelas->id,
                'nama_kelas' => $kelas->nama_kelas,
                'pic' => $dosenNames->isNotEmpty() ? $dosenNames->join(', ') : 'Belum ditentukan',
                'mhs' => $kelas->mahasiswa->count(),
                'kel' => $kelompokCount,
                'ms_selesai' => $selesaiCount.' / '.$targetCount.' target',
                'capaian_pct' => $capaianPct,
                'status' => $targetCount > 0 && $capaianPct >= 50 ? 'Sesuai jadwal' : 'Perlu perhatian',
                'status_type' => $targetCount > 0 && $capaianPct >= 50 ? 'ok' : 'warn',
            ];
        });

        $overdueAlert = collect($milestoneChartData)
            ->filter(fn (array $row): bool => $row['deadline']?->isPast() && $row['remaining'] > 0)
            ->sortBy('deadline')->first();
        $namaKelasList = ($selectedKelasId ? $kelasList->where('id', $selectedKelasId) : $kelasList)
            ->pluck('nama_kelas')->join(' · ');
        $totalKelasCount = $kelasList->count();

        return view('koordinator.dashboard', compact(
            'periodeAktif', 'kelasList', 'totalKelasCount', 'selectedKelasId', 'kelasCount',
            'totalKelompok', 'mahasiswaCount', 'dosenPembimbingCount', 'kelompokDitugaskanCount',
            'milestoneChartData', 'totalSelesaiSemua', 'totalTargetSemua', 'pctCapaian',
            'rekapKelas', 'namaKelasList', 'overdueAlert'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $periodeAktif = Periode::query()->where('is_aktif', true)
            ->orderByDesc('tanggal_mulai')->orderByDesc('id')->first();
        $selectedKelasId = $request->integer('kelas_id') ?: null;
        $kelasList = Kelas::query()
            ->with([
                'dosenPengampu.dosen:id,nama',
                'kelompok:id,kelas_id',
                'mahasiswa' => fn ($query) => $query->select('id', 'kelas_id')->where('is_aktif', true),
            ])
            ->where('is_aktif', true)
            ->when($periodeAktif, fn (Builder $query) => $query->where('periode_id', $periodeAktif->id))
            ->when(! $periodeAktif, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->when($selectedKelasId, fn (Builder $query) => $query->whereKey($selectedKelasId))
            ->orderBy('nama_kelas')->get();
        $milestoneIds = Milestone::query()
            ->when($periodeAktif, fn (Builder $query) => $query->where('periode_id', $periodeAktif->id))
            ->when(! $periodeAktif, fn (Builder $query) => $query->whereRaw('1 = 0'))->pluck('id');
        $mappingCount = DB::table('milestone_mata_kuliah')
            ->join('mata_kuliah', 'mata_kuliah.id', '=', 'milestone_mata_kuliah.mata_kuliah_id')
            ->where('mata_kuliah.is_aktif', true)
            ->whereIn('milestone_mata_kuliah.milestone_id', $milestoneIds)->count();
        $selesaiPerKelas = $this->selesaiPerKelas($kelasList->pluck('id'), $milestoneIds);

        $periodeSlug = $periodeAktif
            ? preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($periodeAktif->nama_periode.'-'.$periodeAktif->tahun_akademik))
            : 'tanpa-periode-aktif';
        $filename = 'rekap-pbl-'.$periodeSlug.($selectedKelasId ? '-kelas-'.$selectedKelasId : '-semua-kelas').'.csv';

        return response()->stream(function () use ($kelasList, $mappingCount, $selesaiPerKelas): void {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Nama Kelas', 'PIC / Dosen Pengampu', 'Jumlah Mahasiswa', 'Jumlah Kelompok', 'Milestone Selesai', 'Persentase Capaian (%)', 'Status']);

            foreach ($kelasList as $kelas) {
                $pic = $kelas->dosenPengampu->pluck('dosen.nama')->filter()->unique()->join(', ') ?: 'Belum ditentukan';
                $kelompokCount = $kelas->kelompok->count();
                $target = $kelompokCount * $mappingCount;
                $selesai = (int) ($selesaiPerKelas[$kelas->id] ?? 0);
                $persentase = $target > 0 ? round($selesai / $target * 100, 1) : 0.0;
                fputcsv($file, [
                    $kelas->nama_kelas, $pic, $kelas->mahasiswa->count(), $kelompokCount,
                    $selesai.' / '.$target.' target', $persentase.'%',
                    $target > 0 && $persentase >= 50 ? 'Sesuai jadwal' : 'Perlu perhatian',
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /** @param Collection<int, int> $kelompokIds @param Collection<int, int> $milestoneIds @return Collection<int, int> */
    private function selesaiPerMilestone(Collection $kelompokIds, Collection $milestoneIds): Collection
    {
        if ($kelompokIds->isEmpty() || $milestoneIds->isEmpty()) {
            return collect();
        }

        return $this->penyelesaianValidQuery()->whereIn('pm.kelompok_id', $kelompokIds)
            ->whereIn('pm.milestone_id', $milestoneIds)->groupBy('pm.milestone_id')
            ->selectRaw('pm.milestone_id, COUNT(DISTINCT pm.id) as selesai')->pluck('selesai', 'pm.milestone_id');
    }

    /** @param Collection<int, int> $kelasIds @param Collection<int, int> $milestoneIds @return Collection<int, int> */
    private function selesaiPerKelas(Collection $kelasIds, Collection $milestoneIds): Collection
    {
        if ($kelasIds->isEmpty() || $milestoneIds->isEmpty()) {
            return collect();
        }

        return $this->penyelesaianValidQuery()->join('kelompok as k', 'k.id', '=', 'pm.kelompok_id')
            ->whereIn('k.kelas_id', $kelasIds)->whereIn('pm.milestone_id', $milestoneIds)
            ->groupBy('k.kelas_id')->selectRaw('k.kelas_id, COUNT(DISTINCT pm.id) as selesai')
            ->pluck('selesai', 'k.kelas_id');
    }

    private function penyelesaianValidQuery(): QueryBuilder
    {
        return DB::table('penyelesaian_milestone as pm')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'pm.mata_kuliah_id')
            ->join('milestone_mata_kuliah as mmk', function ($join): void {
                $join->on('mmk.milestone_id', '=', 'pm.milestone_id')->on('mmk.mata_kuliah_id', '=', 'pm.mata_kuliah_id');
            })
            ->join('pengumpulan_milestone as pg', function ($join): void {
                $join->on('pg.kelompok_id', '=', 'pm.kelompok_id')->on('pg.milestone_id', '=', 'pm.milestone_id')->on('pg.mata_kuliah', '=', 'mk.slug');
            })
            ->where('pm.status', PenyelesaianMilestone::STATUS_SELESAI)->where('mk.is_aktif', true);
    }
}
