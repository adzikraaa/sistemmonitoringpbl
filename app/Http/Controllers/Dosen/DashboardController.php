<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Periode;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $dosen = Auth::guard('dosen')->user();
        $periode = Periode::where('is_aktif', true)->first();

        // ─── Kelompok bimbingan ────────────────────────────────────────
        $kelompokQuery = Kelompok::where('dosen_id', $dosen->id);

        if ($periode) {
            $kelompokQuery->where('periode_id', $periode->id);
        }

        $kelompokList = $kelompokQuery
            ->with(['kelas', 'proposal', 'milestoneKelompok', 'logbookMilestone', 'anggotaKelompok.mahasiswa'])
            ->get();

        $totalKelompok = $kelompokList->count();

        // ─── Laporan masuk (logbook belum direview) ───────────────────
        $kelompokIds = $kelompokList->pluck('id');
        $laporanMasuk = LogbookMilestone::whereIn('kelompok_id', $kelompokIds)
            ->whereIn('status', ['menunggu', 'diajukan'])
            ->count();

        // ─── Perlu revisi (proposal status revisi) ────────────────────
        $perluRevisi = Proposal::whereIn('kelompok_id', $kelompokIds)
            ->where('status', 'revisi')
            ->count();

        // ─── Kelompok terlambat ───────────────────────────────────────
        $kelompokTerlambat = 0;
        foreach ($kelompokList as $kelompok) {
            $hasMilestoneOverdue = $kelompok->milestoneKelompok
                ->where('status', 'terlambat')
                ->isNotEmpty();

            if ($hasMilestoneOverdue) {
                $kelompokTerlambat++;
            }
        }

        // ─── Progress per kelompok ────────────────────────────────────
        $kelompokData = $kelompokList->map(function ($kelompok) {
            // Calculate progress from milestone_kelompok
            $totalMilestones = $kelompok->milestoneKelompok->count();
            $completedMilestones = $kelompok->milestoneKelompok
                ->whereIn('status', ['selesai', 'tercapai'])
                ->count();
            $progress = $totalMilestones > 0
                ? round(($completedMilestones / $totalMilestones) * 100)
                : 0;

            // Determine status
            $hasTerlambat = $kelompok->milestoneKelompok
                ->where('status', 'terlambat')
                ->isNotEmpty();
            $allSelesai = $totalMilestones > 0 && $completedMilestones === $totalMilestones;

            if ($allSelesai) {
                $status = 'selesai';
            } elseif ($hasTerlambat) {
                $status = 'terlambat';
            } else {
                $status = 'berjalan';
            }

            // Get latest proposal title as project name
            $latestProposal = $kelompok->proposal
                ->where('status', '!=', 'ditolak')
                ->sortByDesc('tanggal_pengajuan')
                ->first();

            // Last update (latest logbook or milestone update)
            $lastLogbook = $kelompok->logbookMilestone
                ->sortByDesc('updated_at')
                ->first();
            $lastUpdate = $lastLogbook?->updated_at ?? $kelompok->updated_at;

            return [
                'id' => $kelompok->id,
                'nama' => $kelompok->nama_kelompok,
                'kelas' => $kelompok->kelas?->nama_kelas ?? '-',
                'proyek' => $latestProposal?->judul ?? 'Belum ada proposal',
                'progress' => $progress,
                'status' => $status,
                'last_update' => $lastUpdate,
            ];
        });

        // ─── Sinyal minggu ini – kelompok yg sudah lama tidak update ──
        $sinyalKelompok = $kelompokList->filter(function ($kelompok) {
            $lastLogbook = $kelompok->logbookMilestone
                ->sortByDesc('updated_at')
                ->first();
            $lastActivity = $lastLogbook?->updated_at ?? $kelompok->updated_at;

            return $lastActivity && $lastActivity->lt(now()->subDays(3));
        })->first();

        // ─── Yang menunggu review ─────────────────────────────────────
        $pendingReviews = collect();

        // Logbook menunggu review
        $pendingLogbooks = LogbookMilestone::whereIn('kelompok_id', $kelompokIds)
            ->whereIn('status', ['menunggu', 'diajukan'])
            ->with(['kelompok', 'mahasiswa'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($logbook) {
                return [
                    'type' => 'Logbook',
                    'label' => 'Logbook - '.($logbook->kelompok?->nama_kelompok ?? 'Kelompok'),
                    'name' => $logbook->mahasiswa?->nama ?? '',
                    'date' => $logbook->created_at,
                ];
            });
        $pendingReviews = $pendingReviews->merge($pendingLogbooks);

        // Proposal menunggu review
        $pendingProposals = Proposal::whereIn('kelompok_id', $kelompokIds)
            ->where('status', 'menunggu')
            ->with('kelompok')
            ->latest('tanggal_pengajuan')
            ->take(5)
            ->get()
            ->map(function ($proposal) {
                return [
                    'type' => 'Proposal',
                    'label' => 'Proposal - '.($proposal->kelompok?->nama_kelompok ?? 'Kelompok'),
                    'name' => '',
                    'date' => $proposal->tanggal_pengajuan,
                ];
            });
        $pendingReviews = $pendingReviews->merge($pendingProposals)
            ->sortByDesc('date')
            ->take(3)
            ->values();

        return view('dosen.dashboard', compact(
            'dosen',
            'periode',
            'totalKelompok',
            'laporanMasuk',
            'perluRevisi',
            'kelompokTerlambat',
            'kelompokData',
            'sinyalKelompok',
            'pendingReviews',
        ));
    }

    public function laporanAktivitas()
    {
        return view('dosen.aktifitas-laporan');
    }
}
