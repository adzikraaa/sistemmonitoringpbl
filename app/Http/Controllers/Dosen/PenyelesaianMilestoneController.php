<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelompok;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\PenyelesaianMilestone;
use App\Services\PenyelesaianMilestoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PenyelesaianMilestoneController extends Controller
{
    public function index()
    {
        /** @var Dosen|null $dosen */
        $dosen = Auth::guard('dosen')->user();

        if (! $dosen) {
            abort(403);
        }

        $pengampuList = DosenPengampu::query()
            ->with(['mataKuliah', 'kelas'])
            ->where('dosen_id', $dosen->id)
            ->whereHas('kelas', fn ($query) => $query->where('is_aktif', true))
            ->whereHas('mataKuliah', fn ($query) => $query->where('is_aktif', true))
            ->get()
            ->filter(fn ($pengampu) => $pengampu->mataKuliah && $pengampu->kelas)
            ->values();

        $kelasIds = $pengampuList->pluck('kelas_id')->unique()->values()->all();
        $mataKuliahIds = $pengampuList->pluck('mata_kuliah_id')->unique()->values()->all();

        $kelompokByKelas = Kelompok::query()
            ->with('periode')
            ->whereIn('kelas_id', $kelasIds)
            ->orderBy('nama_kelompok')
            ->get()
            ->groupBy('kelas_id');

        $kelompokIds = $kelompokByKelas->flatMap(fn ($items) => $items)->pluck('id')->all();
        $periodeIds = $kelompokByKelas->flatMap(fn ($items) => $items)
            ->pluck('periode_id')->filter()->unique()->values()->all();

        $milestoneByPeriode = $mataKuliahIds === []
            ? collect()
            : Milestone::query()
                ->with(['mataKuliah' => fn ($query) => $query->whereIn('mata_kuliah.id', $mataKuliahIds)])
                ->whereIn('periode_id', $periodeIds)
                ->orderBy('urutan')
                ->get()
                ->groupBy('periode_id');

        $statusMap = $kelompokIds === []
            ? collect()
            : PenyelesaianMilestone::query()
                ->whereIn('kelompok_id', $kelompokIds)
                ->whereIn('mata_kuliah_id', $mataKuliahIds)
                ->get()
                ->keyBy(fn ($row) => $row->kelompok_id.':'.$row->milestone_id.':'.$row->mata_kuliah_id);

        return view('dosen.penyelesaian-milestone.index', compact(
            'pengampuList',
            'kelompokByKelas',
            'milestoneByPeriode',
            'statusMap'
        ));
    }

    public function update(
        Request $request,
        Kelompok $kelompok,
        Milestone $milestone,
        MataKuliah $mataKuliah,
        PenyelesaianMilestoneService $service
    ) {
        /** @var Dosen|null $dosen */
        $dosen = Auth::guard('dosen')->user();

        if (! $dosen) {
            abort(403);
        }

        $pengampu = DosenPengampu::query()
            ->where('dosen_id', $dosen->id)
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->where('kelas_id', $kelompok->kelas_id)
            ->exists();

        if (! $pengampu) {
            abort(403, 'Anda tidak mengampu mata kuliah ini pada kelas kelompok tersebut.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(PenyelesaianMilestone::STATUSES)],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $service->ubahStatus(
                $kelompok,
                $milestone,
                $mataKuliah,
                $dosen,
                $validated['status'],
                $validated['catatan'] ?? null
            );
        } catch (\InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['status' => $exception->getMessage()]);
        }

        return back()->with('success', 'Status penyelesaian milestone berhasil diperbarui.');
    }
}
