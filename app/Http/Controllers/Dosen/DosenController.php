<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Notifikasi;
use App\Models\PengumpulanMilestone;
use App\Models\Periode;
use App\Models\Proposal;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    /**
     * Data bersama yang dipass ke semua view Dosen.
     * Ambil dari guard 'dosen' jika sudah login,
     * data dosen aktif.
     */
    private function sharedViewData(): array
    {
        $dosen = Auth::guard('dosen')->user();

        return [
            'userName' => $dosen?->nama ?? 'Dosen Pengampu',
            'userNidn' => $dosen?->nidn ?? '-',
            'userRole' => 'Dosen Pengampu',
            'tahunAkademik' => Periode::query()->where('is_aktif', true)->value('tahun_akademik') ?? '-',
            'unreadDosenNotifCount' => $dosen?->notifikasi()->whereNull('dibaca_pada')->count() ?? 0,
        ];
    }

    private function semesterData(): array
    {
       $periodes = Periode::query()
    ->where('is_aktif', true)
    ->orderByDesc('tanggal_mulai')
    ->get();
        if ($periodes->isEmpty()) {
            return [
                [
                    'id' => 'smt5',
                    'tingkat' => 'PBL TINGKAT III',
                    'nama' => 'Semester 5',
                    'tahun' => 'Tahun Akademik 2026/2027',
                    'status' => 'berjalan',
                    'deskripsi' => 'Periode PBL yang sedang berlangsung.',
                    'progress' => 56,
                    'minggu_ke' => 9,
                    'total_minggu' => 16,
                    'status_penilaian' => null,
                ],
            ];
        }

        return $periodes->map(function (Periode $periode): array {
            $tanggalMulai = $periode->tanggal_mulai;
            $tanggalSelesai = $periode->tanggal_selesai;
            $sedangBerjalan = $periode->is_aktif && $tanggalMulai?->lte(today()) && $tanggalSelesai?->gte(today());
            $totalMinggu = $tanggalMulai && $tanggalSelesai ? max(1, (int) ceil($tanggalMulai->diffInDays($tanggalSelesai) / 7)) : null;
            $mingguKe = $sedangBerjalan && $tanggalMulai ? min($totalMinggu, max(1, (int) ceil($tanggalMulai->diffInDays(today()) / 7) + 1)) : null;

            return [
                'id' => (string) $periode->id,
                'tingkat' => mb_strtoupper($periode->nama_periode),
                'nama' => $periode->nama_periode,
                'tahun' => 'Tahun Akademik '.$periode->tahun_akademik,
                'status' => $sedangBerjalan ? 'berjalan' : 'selesai',
                'deskripsi' => $tanggalMulai && $tanggalSelesai
                    ? 'Periode '.$periode->nama_periode.' berlangsung '.$tanggalMulai->translatedFormat('d F Y').' sampai '.$tanggalSelesai->translatedFormat('d F Y').'.'
                    : 'Periode akademik '.$periode->nama_periode.'.',
                'progress' => $sedangBerjalan && $mingguKe && $totalMinggu ? (int) round($mingguKe / $totalMinggu * 100) : null,
                'minggu_ke' => $mingguKe,
                'total_minggu' => $totalMinggu,
                'status_penilaian' => $sedangBerjalan ? null : 'Periode selesai',
            ];
        })->all();
    }

    /**
     * Halaman Pilih Semester Dosen Pengampu.
     */
    public function pilihSemester()
    {
        $semesters = $this->semesterData();

        return view('dosen.pilih-semester', array_merge(
            $this->sharedViewData(),
            ['semesters' => $semesters]
        ));
    }

    /**
     * Simpan semester yang dipilih ke session,
     * lalu arahkan langsung ke Dashboard Dosen Pengampu.
     */
    public function setSemester(Request $request)
    {
        $validIds = collect($this->semesterData())->pluck('id')->map(fn ($id) => (string) $id)->all();

        $validated = $request->validate([
            'semester_id' => [
                'required',
                function ($attribute, $value, $fail) use ($validIds) {
                    if (! in_array((string) $value, $validIds, true) && ! Periode::where('id', $value)->exists()) {
                        $fail('Semester yang dipilih tidak valid.');
                    }
                },
            ],
        ]);

        $request->session()->put('dosen.semester_id', $validated['semester_id']);

        return redirect()->route('dosen.dashboard');
    }

    /**
     * Dashboard Dosen Pengampu.
     * Membaca konteks semester dari session dan data kelompok nyata dari DB.
     */
    public function dashboard(Request $request)
    {
        $semesterId = session('dosen.semester_id');

        // Jika belum memilih semester, redirect balik ke pilih semester
        if (! $semesterId) {
            return redirect()->route('dosen.pilih-semester')
                ->with('info', 'Silakan pilih semester terlebih dahulu.');
        }

        // Cari data semester yang dipilih
        $semester = collect($this->semesterData())->firstWhere('id', (string) $semesterId);

        $periode = is_numeric($semesterId)
            ? Periode::query()->find($semesterId)
            : (Periode::query()->where('nama_periode', 'like', '%'.str_replace('smt', '', (string) $semesterId).'%')->first()
                ?? Periode::query()->where('is_aktif', true)->first()
                ?? Periode::query()->latest()->first());

        // Jika semester tidak ditemukan sama sekali, reset dan redirect
        if (! $semester && ! $periode) {
            $request->session()->forget('dosen.semester_id');

            return redirect()->route('dosen.pilih-semester')
                ->with('error', 'Semester tidak ditemukan. Silakan pilih ulang.');
        }

        if (! $semester && $periode) {
            $semester = [
                'id' => (string) $periode->id,
                'tingkat' => mb_strtoupper($periode->nama_periode),
                'nama' => $periode->nama_periode,
                'tahun' => 'Tahun Akademik '.$periode->tahun_akademik,
                'status' => $periode->is_aktif ? 'berjalan' : 'selesai',
            ];
        }

        // Ambil dosen yang sedang login
        $dosen = Auth::guard('dosen')->user();

        // Ambil kelompok dari DB: baik yang langsung dibimbing maupun yang berada di kelas pengampu dosen ini
        $kelasIdsPengampu = $dosen?->dosenPengampu()->pluck('kelas_id')->filter()->unique() ?? collect();

        $kelompokRelations = ['kelas', 'anggotaKelompok.mahasiswa', 'logbookMilestone', 'proposal', 'pengumpulanMilestone'];
        $kelompokDbQuery = Kelompok::with($kelompokRelations);

        if ($periode) {
            $kelompokDbQuery->where('periode_id', $periode->id);
        }

        if ($dosen) {
            $kelompokDbQuery->where(function ($q) use ($dosen, $kelasIdsPengampu) {
                $q->where('dosen_id', $dosen->id);
                if ($kelasIdsPengampu->isNotEmpty()) {
                    $q->orWhereIn('kelas_id', $kelasIdsPengampu);
                }
            });
        }

        $kelompokDb = $kelompokDbQuery->get();

        // Fallback jika belum ada kelompok yang terhubung langsung, tampilkan kelompok yang ada di periode ini
        if ($kelompokDb->isEmpty()) {
            $kelompokDb = Kelompok::with($kelompokRelations)
                ->when($periode, fn ($q) => $q->where('periode_id', $periode->id))
                ->get();
        }

        $progressByKelompok = app(ProgressService::class)->untukDaftar($kelompokDb);

        // Format kelompok untuk blade (kelompokData)
        $kelompokData = $kelompokDb->map(function ($k) use ($progressByKelompok) {
            $tanggalPembaruanTerakhir = collect([
                $k->logbookMilestone->max('updated_at'),
                $k->proposal->max('updated_at'),
                $k->pengumpulanMilestone->max('updated_at'),
            ])->filter()->sortByDesc(fn ($tanggal) => $tanggal->getTimestamp())->first();
            $tanggalAktivitasTerakhir = $tanggalPembaruanTerakhir
                ?? $k->tanggal_dibuat
                ?? $k->created_at;
            $statusAsli = strtolower($k->status ?? 'aktif');
            $hariTidakAktif = $tanggalAktivitasTerakhir?->diffInDays(now()) ?? 0;
            $status = $hariTidakAktif >= 7 && ! in_array($statusAsli, ['selesai', 'ditangguhkan'], true)
                ? 'terlambat'
                : match ($statusAsli) {
                    'aktif', 'berjalan' => 'berjalan',
                    default => $statusAsli,
                };

            return [
                'id' => $k->id,
                'nama' => $k->nama_kelompok,
                'kelas' => $k->kelas?->nama_kelas ?? '-',
                'proyek' => $k->judul_proyek ?? 'Belum ada judul proyek',
                'progress' => $progressByKelompok[$k->id]['persen'] ?? 0,
                'status' => $status,
                'last_update' => $tanggalAktivitasTerakhir,
                'has_update' => $tanggalPembaruanTerakhir !== null,
            ];
        })->values()->all();

        // Hitung statistik ringkasan
        $totalKelompok = count($kelompokData);

        // Laporan masuk: logbook + proposal + pengumpulan milestone
        $laporanMasuk = 0;
        $belumReview = 0;
        $perluRevisi = 0;
        try {
            $kelompokIds = $kelompokDb->pluck('id');
            $logbooks = LogbookMilestone::with('kelompok')->whereIn('kelompok_id', $kelompokIds)->get();
            $proposals = Proposal::with('kelompok')->whereIn('kelompok_id', $kelompokIds)->get();
            $submissions = PengumpulanMilestone::with(['kelompok', 'milestone'])->whereIn('kelompok_id', $kelompokIds)->get();

            $laporanMasuk = $logbooks->count() + $proposals->count() + $submissions->count();

            $belumReview = $logbooks->filter(fn ($item) => in_array(strtolower($item->status ?? ''), ['', 'menunggu_review', 'menunggu review', 'menunggu', 'diajukan'], true))->count()
                + $proposals->filter(fn ($item) => ! in_array(strtolower($item->status ?? ''), ['disetujui', 'ditolak', 'revisi'], true))->count()
                + $submissions->filter(fn ($item) => in_array(strtolower($item->status ?? ''), ['', 'diajukan', 'menunggu_review', 'menunggu review'], true))->count();

            $perluRevisi = $logbooks->filter(fn ($item) => in_array(strtolower($item->status ?? ''), ['revisi', 'perlu revisi', 'perlu_revisi'], true))->count()
                + $proposals->filter(fn ($item) => in_array(strtolower($item->status ?? ''), ['ditolak', 'revisi'], true))->count()
                + $submissions->filter(fn ($item) => in_array(strtolower($item->status ?? ''), ['ditolak', 'revisi'], true))->count();
        } catch (\Throwable $e) {
            // failsafe — tabel mungkin belum ada
        }

        $summaryData = [
            'total_kelompok' => $totalKelompok,
            'laporan_masuk' => $laporanMasuk,
            'belum_review' => $belumReview,
            'perlu_revisi' => $perluRevisi,
            'terlambat' => collect($kelompokData)->where('status', 'terlambat')->count(),
        ];

        // Sinyal Minggu Ini — kelompok paling lama tidak update
        $sinyalKelompok = null;
        if ($totalKelompok > 0) {
            $oldest = collect($kelompokData)->sortBy(fn ($kelompok) => $kelompok['last_update']?->getTimestamp() ?? 0)->first();
            $hariLalu = $oldest['last_update'] ? (int) $oldest['last_update']->diffInDays(now()) : 0;
            if ($hariLalu >= 3) {
                $sinyalKelompok = [
                    'id' => $oldest['id'],
                    'nama' => $oldest['nama'],
                    'hari_lalu' => $hariLalu,
                    'has_update' => $oldest['has_update'],
                ];
            }
        }

        // Pending reviews (logbook, proposal, dan milestone menunggu review)
        $pendingReviews = [];
        try {
            foreach ($logbooks ?? [] as $lb) {
                if (in_array(strtolower($lb->status ?? ''), ['', 'menunggu_review', 'menunggu review', 'menunggu', 'diajukan'], true)) {
                    $pendingReviews[] = [
                        'type' => 'Logbook',
                        'label' => $lb->created_at?->format('d M Y') ?? '-',
                        'name' => ($lb->kelompok?->nama_kelompok ?? 'Kelompok').' · '.($lb->judul ?? 'Catatan'),
                        'url' => route('dosen.laporan-aktivitas.logbook', $lb->id),
                    ];
                }
            }
            foreach ($proposals ?? [] as $prop) {
                if (! in_array(strtolower($prop->status ?? ''), ['disetujui', 'ditolak', 'revisi'], true)) {
                    $pendingReviews[] = [
                        'type' => 'Proposal',
                        'label' => $prop->tanggal_pengajuan?->format('d M Y') ?? '-',
                        'name' => ($prop->kelompok?->nama_kelompok ?? 'Kelompok').' · '.($prop->judul ?? 'Pengajuan Proposal'),
                        'url' => route('dosen.laporan-aktivitas.proposal', $prop->id),
                    ];
                }
            }
            foreach ($submissions ?? [] as $sub) {
                if (in_array(strtolower($sub->status ?? ''), ['', 'diajukan', 'menunggu_review', 'menunggu review'], true)) {
                    $pendingReviews[] = [
                        'type' => 'Milestone',
                        'label' => $sub->created_at?->format('d M Y') ?? '-',
                        'name' => ($sub->kelompok?->nama_kelompok ?? 'Kelompok').' · '.($sub->milestone?->judul ?? 'Laporan Milestone'),
                        'url' => route('dosen.penyelesaian-milestone.index'),
                    ];
                }
            }
        } catch (\Throwable $e) {
            // failsafe
        }

        return view('dosen.dashboard', array_merge(
            $this->sharedViewData(),
            [
                'semester' => $semester,
                'kelompokData' => $kelompokData,
                'summaryData' => $summaryData,
                'sinyalKelompok' => $sinyalKelompok,
                'pendingReviews' => $pendingReviews,
            ]
        ));
    }

    public function notifikasi()
    {
        $notifikasi = Auth::guard('dosen')->user()->notifikasi()->latest()->get()->map(function (Notifikasi $item): array {
            $judulLower = strtolower($item->judul ?? '');
            $tautanLower = strtolower($item->tautan ?? '');

            $jenis = 'logbook';
            if (str_contains($judulLower, 'proposal') || str_contains($tautanLower, 'proposal')) {
                $jenis = 'proposal';
            } elseif (str_contains($judulLower, 'milestone') || str_contains($tautanLower, 'milestone')) {
                $jenis = 'milestone';
            } elseif (str_contains($judulLower, 'bimbingan') || str_contains($tautanLower, 'bimbingan')) {
                $jenis = 'bimbingan';
            }

            $linkText = match ($jenis) {
                'proposal' => 'Periksa Proposal',
                'milestone' => 'Tinjau Milestone',
                'logbook' => 'Periksa Logbook',
                'bimbingan' => 'Lihat Bimbingan',
                default => 'Buka pembaruan',
            };

            return [
                'id' => $item->id,
                'jenis' => $jenis,
                'judul' => $item->judul,
                'pesan' => $item->isi,
                'waktu' => $item->created_at,
                'link' => $item->tautan ?? route('dosen.dashboard'),
                'link_text' => $linkText,
                'dibaca' => $item->dibaca_pada !== null,
            ];
        })->all();

        return view('dosen.notifikasi', array_merge(
            $this->sharedViewData(),
            ['notifikasi' => $notifikasi]
        ));
    }

    public function tandaiNotifikasiDibaca(Request $request, Notifikasi $notifikasi)
    {
        abort_unless(
            $notifikasi->penerima_type === Auth::guard('dosen')->user()->getMorphClass()
            && (int) $notifikasi->penerima_id === (int) Auth::guard('dosen')->id(),
            404
        );

        $notifikasi->update(['dibaca_pada' => now()]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca.']);
        }

        return back()->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    public function tandaiSemuaNotifikasiDibaca(Request $request)
    {
        Auth::guard('dosen')->user()->notifikasi()->whereNull('dibaca_pada')->update(['dibaca_pada' => now()]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sudah dibaca.']);
        }

        return back()->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }
}
