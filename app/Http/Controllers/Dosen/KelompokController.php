<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Deliverable;
use App\Models\Kelompok;
use App\Models\PengumpulanDeliverable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class KelompokController extends Controller
{
    /**
     * Menampilkan daftar kelompok bimbingan dosen pengampu
     */
    public function index(Request $request): View
    {
        $dosenId = auth('dosen')->id();

        $query = Kelompok::with([
            'anggotaKelompok.mahasiswa',
            'logbookMilestone',
            'kelas',
            'proposal',
            'milestoneKelompok',
        ])->where('dosen_id', $dosenId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', "%{$search}%")
                    ->orWhereHas('proposal', function ($p) use ($search) {
                        $p->where('judul', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('kelas')) {
            $kelasNama = $request->kelas;
            $query->whereHas('kelas', function ($q) use ($kelasNama) {
                $q->where('nama_kelas', $kelasNama);
            });
        }

        $allKelompok = $query->get()->map(function (Kelompok $item) {
            $latestProposal = $item->proposal->sortByDesc('tanggal_pengajuan')->first();
            $item->judul_proyek = $latestProposal?->judul ?? 'Belum ada proposal';

            $totalMilestones = $item->milestoneKelompok->count();
            $completedMilestones = $item->milestoneKelompok
                ->whereIn('status', ['selesai', 'tercapai'])
                ->count();

            $totalDeliverablesInPeriod = Deliverable::whereHas('milestone', function ($m) use ($item) {
                $m->where('periode_id', $item->periode_id);
            })->count();

            $deliverableApproved = PengumpulanDeliverable::where('kelompok_id', $item->id)
                ->where('status', 'disetujui')
                ->count();

            $calculatedProgress = $totalDeliverablesInPeriod > 0
                ? round(($deliverableApproved / $totalDeliverablesInPeriod) * 100)
                : ($totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0);

            $hasTerlambat = $item->milestoneKelompok->where('status', 'terlambat')->isNotEmpty();
            $hasMenunggu = $item->logbookMilestone->whereIn('status', ['diajukan', 'menunggu'])->isNotEmpty();

            if ($totalMilestones > 0 && $completedMilestones === $totalMilestones) {
                $status = 'Selesai';
            } elseif ($hasTerlambat) {
                $status = 'Terlambat';
            } elseif ($hasMenunggu) {
                $status = 'Review';
            } else {
                $status = 'Berjalan';
            }

            $item->status = $status;
            $item->progress_dummy = $calculatedProgress;

            return $item;
        });

        if ($request->filled('status')) {
            $statusFilter = strtolower($request->status);
            $allKelompok = $allKelompok->filter(function ($item) use ($statusFilter) {
                return strtolower($item->status) === $statusFilter;
            })->values();
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 6;
        $paginatedItems = $allKelompok->slice(($page - 1) * $perPage, $perPage)->values();

        $kelompokList = new LengthAwarePaginator(
            $paginatedItems,
            $allKelompok->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('dosen.kelompok.index', compact('kelompokList'));
    }

    public function show(int|string $id): View
    {
        $dosenId = auth('dosen')->id();

        $kelompok = Kelompok::with(['anggotaKelompok.mahasiswa', 'logbookMilestone', 'proposal', 'kelas'])
            ->where('dosen_id', $dosenId)
            ->findOrFail($id);

        $latestProposal = $kelompok->proposal->sortByDesc('tanggal_pengajuan')->first();
        $kelompok->judul_proyek = $latestProposal?->judul ?? 'Belum ada proposal';
        $kelompok->status = 'Berjalan';

        return view('dosen.kelompok.show', compact('kelompok'));
    }
}
