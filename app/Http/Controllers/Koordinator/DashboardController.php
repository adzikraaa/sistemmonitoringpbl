<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Milestone;
use App\Models\Periode;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil periode aktif
        $periodeAktif = Periode::where('is_aktif', true)->first();

        // 2. Daftar kelas untuk filter
        $kelasList = Kelas::when($periodeAktif, function ($q) use ($periodeAktif) {
            $q->where('periode_id', $periodeAktif->id);
        })
        ->orderBy('nama_kelas')
        ->get();

        $totalKelasCount = $kelasList->count();
        $selectedKelasId = $request->get('kelas_id');

        // Validasi selectedKelasId ada di kelasList jika diisi
        if ($selectedKelasId && !$kelasList->contains('id', (int) $selectedKelasId)) {
            $selectedKelasId = null;
        }

        // 3. Hitung Statistik Ringkasan
        $kelasCount = $selectedKelasId ? 1 : $totalKelasCount;

        $kelompokQuery = Kelompok::when($periodeAktif, function ($q) use ($periodeAktif) {
            $q->where('periode_id', $periodeAktif->id);
        })
        ->when($selectedKelasId, function ($q) use ($selectedKelasId) {
            $q->where('kelas_id', $selectedKelasId);
        });

        $totalKelompok = (clone $kelompokQuery)->count();

        // Mahasiswa: hitung dari anggotaKelompok pada kelompok terfilter atau total mahasiswa kelas
        $kelompokIds = (clone $kelompokQuery)->pluck('id');
        $mahasiswaCount = DB::table('anggota_kelompok')
            ->whereIn('kelompok_id', $kelompokIds)
            ->count();

        if ($mahasiswaCount === 0) {
            if ($selectedKelasId) {
                $mahasiswaCount = Mahasiswa::where('kelas_id', $selectedKelasId)->count() ?: ($totalKelompok * 4);
            } else {
                $mahasiswaCount = Mahasiswa::count() ?: ($totalKelompok * 4);
            }
        }

        // Dosen pembimbing
        $dosenPembimbingCount = (clone $kelompokQuery)
            ->whereNotNull('dosen_id')
            ->distinct('dosen_id')
            ->count('dosen_id');

        $kelompokDitugaskanCount = (clone $kelompokQuery)
            ->whereNotNull('dosen_id')
            ->count();

        // 4. Milestone Global & Data Chart
        $milestones = Milestone::when($periodeAktif, function ($q) use ($periodeAktif) {
            $q->where('periode_id', $periodeAktif->id);
        })
        ->orderBy('urutan')
        ->get();

        $milestoneChartData = [];
        $totalSelesaiSemua = 0;
        $totalTargetSemua = $totalKelompok * max(1, $milestones->count());

        $palette = ['#4A3222', '#8B6240', '#B08060', '#C49A78', '#D6C4B2'];

        foreach ($milestones as $idx => $m) {
            // Hitung berapa kelompok yang telah menyelesaikan milestone ini
            $selesaiCount = 0;
            if ($kelompokIds->isNotEmpty()) {
                $selesaiCount = DB::table('penyelesaian_milestone')
                    ->where('milestone_id', $m->id)
                    ->whereIn('kelompok_id', $kelompokIds)
                    ->where('status', 'disetujui')
                    ->distinct('kelompok_id')
                    ->count('kelompok_id');

                if ($selesaiCount === 0) {
                    $selesaiCount = DB::table('milestone_kelompok')
                        ->where('milestone_id', $m->id)
                        ->whereIn('kelompok_id', $kelompokIds)
                        ->whereIn('status', ['selesai', 'disetujui'])
                        ->distinct('kelompok_id')
                        ->count('kelompok_id');
                }
            }

            $totalSelesaiSemua += $selesaiCount;
            $pct = $totalKelompok > 0 ? round(($selesaiCount / $totalKelompok) * 100, 1) : 0;

            $milestoneChartData[] = [
                'label' => $m->judul,
                'val' => $selesaiCount,
                'max' => $totalKelompok,
                'pct' => $pct,
                'color' => $palette[$idx % count($palette)],
            ];
        }

        // Jika tidak ada data milestone di DB, sediakan fallback default yang informatif
        if (empty($milestoneChartData)) {
            $defaultLabels = ['Tim & Topik', 'Proposal', 'Prototipe', 'Implementasi', 'Laporan Akhir'];
            foreach ($defaultLabels as $idx => $lbl) {
                $milestoneChartData[] = [
                    'label' => $lbl,
                    'val' => 0,
                    'max' => $totalKelompok,
                    'pct' => 0,
                    'color' => $palette[$idx % count($palette)],
                ];
            }
        }

        $pctCapaian = $totalTargetSemua > 0 ? round(($totalSelesaiSemua / $totalTargetSemua) * 100, 1) : 0;

        // 5. Rekapitulasi per kelas (Paginated)
        $rekapQuery = Kelas::with(['dosenPengampu.dosen', 'kelompok.anggotaKelompok', 'mahasiswa'])
            ->when($periodeAktif, function ($q) use ($periodeAktif) {
                $q->where('periode_id', $periodeAktif->id);
            })
            ->when($selectedKelasId, function ($q) use ($selectedKelasId) {
                $q->where('id', $selectedKelasId);
            })
            ->orderBy('nama_kelas');

        $rekapKelas = $rekapQuery->paginate(5)->withQueryString();

        // Transform collection items for view presentation
        $rekapKelas->getCollection()->transform(function ($kelas) use ($milestones) {
            $pic = $kelas->dosenPengampu->map(fn ($dp) => $dp->dosen?->nama)->filter()->first() ?? 'Belum ditentukan';
            $mhsCount = $kelas->mahasiswa->count();
            if ($mhsCount === 0) {
                $mhsCount = $kelas->kelompok->sum(fn ($k) => $k->anggotaKelompok->count()) ?: ($kelas->kelompok->count() * 4);
            }
            $kelCount = $kelas->kelompok->count();
            $targetCount = $kelCount * max(1, $milestones->count());

            $kIds = $kelas->kelompok->pluck('id');
            $selesaiCount = 0;
            if ($kIds->isNotEmpty()) {
                $selesaiCount = DB::table('penyelesaian_milestone')
                    ->whereIn('kelompok_id', $kIds)
                    ->where('status', 'disetujui')
                    ->count();

                if ($selesaiCount === 0) {
                    $selesaiCount = DB::table('milestone_kelompok')
                        ->whereIn('kelompok_id', $kIds)
                        ->whereIn('status', ['selesai', 'disetujui'])
                        ->count();
                }
            }

            $capaianPct = $targetCount > 0 ? round(($selesaiCount / $targetCount) * 100, 1) : 0;
            $statusType = $capaianPct >= 50 ? 'ok' : 'warn';
            $statusLabel = $capaianPct >= 50 ? 'Sesuai jadwal' : 'Perlu perhatian';

            return (object) [
                'id' => $kelas->id,
                'nama_kelas' => $kelas->nama_kelas,
                'pic' => $pic,
                'mhs' => $mhsCount,
                'kel' => $kelCount,
                'ms_selesai' => "{$selesaiCount} / {$targetCount} target",
                'capaian_pct' => $capaianPct,
                'status' => $statusLabel,
                'status_type' => $statusType,
            ];
        });

        // Subteks kelas
        $namaKelasList = $kelasList->pluck('nama_kelas')->join(' · ');

        return view('koordinator.dashboard', compact(
            'periodeAktif',
            'kelasList',
            'totalKelasCount',
            'selectedKelasId',
            'kelasCount',
            'totalKelompok',
            'mahasiswaCount',
            'dosenPembimbingCount',
            'kelompokDitugaskanCount',
            'milestoneChartData',
            'totalSelesaiSemua',
            'totalTargetSemua',
            'pctCapaian',
            'rekapKelas',
            'namaKelasList'
        ));
    }

    public function export(Request $request)
    {
        $periodeAktif = Periode::where('is_aktif', true)->first();
        $selectedKelasId = $request->get('kelas_id');

        $kelasQuery = Kelas::with(['dosenPengampu.dosen', 'kelompok.anggotaKelompok', 'mahasiswa'])
            ->when($periodeAktif, function ($q) use ($periodeAktif) {
                $q->where('periode_id', $periodeAktif->id);
            })
            ->when($selectedKelasId, function ($q) use ($selectedKelasId) {
                $q->where('id', $selectedKelasId);
            })
            ->orderBy('nama_kelas');

        $kelasList = $kelasQuery->get();
        $milestones = Milestone::when($periodeAktif, fn ($q) => $q->where('periode_id', $periodeAktif->id))->get();
        $milestoneCount = max(1, $milestones->count() ?: 5);

        $periodeSlug = $periodeAktif 
            ? preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($periodeAktif->nama_periode . '-' . $periodeAktif->tahun_akademik))
            : 'semua-periode';
        $kelasSlug = $selectedKelasId ? '-kelas-' . $selectedKelasId : '-semua-kelas';
        $filename = "rekap-pbl-{$periodeSlug}{$kelasSlug}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($kelasList, $milestoneCount) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM
            fputs($file, "\xEF\xBB\xBF");

            // Header baris CSV
            fputcsv($file, [
                'Nama Kelas',
                'PIC / Dosen Pengampu',
                'Jumlah Mahasiswa',
                'Jumlah Kelompok',
                'Milestone Selesai',
                'Persentase Capaian (%)',
                'Status',
            ]);

            foreach ($kelasList as $kelas) {
                $pic = $kelas->dosenPengampu->map(fn ($dp) => $dp->dosen?->nama)->filter()->first() ?? 'Belum ditentukan';
                $mhs = $kelas->mahasiswa->count();
                if ($mhs === 0) {
                    $mhs = $kelas->kelompok->sum(fn ($k) => $k->anggotaKelompok->count()) ?: ($kelas->kelompok->count() * 4);
                }
                $kel = $kelas->kelompok->count();
                $target = $kel * $milestoneCount;

                $kelompokIds = $kelas->kelompok->pluck('id');
                $selesai = 0;
                if ($kelompokIds->isNotEmpty()) {
                    $selesai = DB::table('penyelesaian_milestone')
                        ->whereIn('kelompok_id', $kelompokIds)
                        ->where('status', 'disetujui')
                        ->count();

                    if ($selesai === 0) {
                        $selesai = DB::table('milestone_kelompok')
                            ->whereIn('kelompok_id', $kelompokIds)
                            ->whereIn('status', ['selesai', 'disetujui'])
                            ->count();
                    }
                }

                $pct = $target > 0 ? round(($selesai / $target) * 100, 1) : 0;
                $status = $pct >= 50 ? 'Sesuai jadwal' : 'Perlu perhatian';

                fputcsv($file, [
                    $kelas->nama_kelas,
                    $pic,
                    $mhs,
                    $kel,
                    "{$selesai} / {$target} target",
                    "{$pct}%",
                    $status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
