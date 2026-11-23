<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MahasiswaController;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\Notifikasi;
use App\Models\PengumpulanMilestone;
use App\Models\PenyelesaianMilestone;
use App\Models\Periode;
use App\Models\Proposal;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected function kelasList(): array
    {
        $dosen = Auth::guard('dosen')->user();
        $periodeId = session('dosen.semester_id') ?? Periode::where('is_aktif', true)->value('id');

        $kelasQuery = Kelas::with(['periode', 'kelompok.logbookMilestone', 'kelompok.proposal']);

        if ($periodeId) {
            $kelasQuery->where('periode_id', $periodeId);
        }

        if ($dosen) {
            $kelasIdsPengampu = $dosen->dosenPengampu()->pluck('kelas_id')->filter()->unique();
            if ($kelasIdsPengampu->isNotEmpty()) {
                $kelasQuery->where(function ($q) use ($kelasIdsPengampu, $dosen) {
                    $q->whereIn('id', $kelasIdsPengampu)
                        ->orWhereHas('kelompok', fn ($kq) => $kq->where('dosen_id', $dosen->id));
                });
            }
        }

        $kelasListDb = $kelasQuery->get();

        // Fallback jika belum ada penugasan kelas spesifik, ambil kelas pada periode aktif
        if ($kelasListDb->isEmpty()) {
            $kelasListDb = Kelas::with(['periode', 'kelompok.logbookMilestone', 'kelompok.proposal'])
                ->when($periodeId, fn ($q) => $q->where('periode_id', $periodeId))
                ->get();
        }

        if ($kelasListDb->isEmpty()) {
            $kelasListDb = Kelas::with(['periode', 'kelompok.logbookMilestone', 'kelompok.proposal'])->get();
        }

        $allKelompok = $kelasListDb->flatMap->kelompok;
        $progressByKelompok = app(ProgressService::class)->untukDaftar($allKelompok);

        return $kelasListDb->map(function ($kelas) use ($progressByKelompok) {
            $kelompokKelas = $kelas->kelompok ?? collect();
            $jumlahKelompok = $kelompokKelas->count();

            // Progress rata-rata kelompok di kelas ini
            $progressAvg = 0;
            if ($jumlahKelompok > 0) {
                $totalProgress = $kelompokKelas->sum(fn ($k) => $progressByKelompok[$k->id]['persen'] ?? 0);
                $progressAvg = (int) round($totalProgress / $jumlahKelompok);
            }

            // Menunggu review: logbook + proposal yang belum direview di kelas ini
            $menungguReview = 0;
            foreach ($kelompokKelas as $k) {
                $menungguReview += $k->logbookMilestone->whereIn('status', ['menunggu_review', 'menunggu review', ''])->count();
                $menungguReview += $k->proposal->whereNotIn('status', ['disetujui', 'ditolak'])->count();
            }

            return [
                'id' => $kelas->id,
                'nama' => $kelas->nama_kelas,
                'prodi' => $kelas->prodi ?? 'Sistem Informasi Bisnis',
                'semester' => $kelas->periode?->nama_periode ?? 'Semester Aktif',
                'jumlah_kelompok' => $jumlahKelompok,
                'progress' => $progressAvg,
                'menunggu_review' => $menungguReview,
                'periode' => $kelas->periode?->nama_periode ?? 'Periode Berjalan',
            ];
        })->values()->all();
    }

    public function index()
    {
        if (! session()->has('dosen.semester_id')) {
            return redirect()->route('dosen.pilih-semester');
        }

        return view('dosen.dashboard', [
            'kelas' => $this->currentKelas(),
        ]);
    }

    public function pilihKelas()
    {
        $selectedId = session('dosen_active_class_id');

        return view('dosen.pilih-kelas', [
            'kelasList' => $this->kelasList(),
            'selectedKelas' => collect($this->kelasList())->firstWhere('id', $selectedId),
        ]);
    }

    public function selectKelas(Request $request)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'integer', 'min:1'],
        ]);

        $kelas = collect($this->kelasList())->firstWhere('id', $validated['class_id']);

        if (! $kelas) {
            return back()->with('error', 'Kelas yang dipilih tidak ditemukan.');
        }

        session(['dosen_active_class_id' => $kelas['id']]);

        return back()->with('success', 'Kelas '.$kelas['nama'].' berhasil dipilih.');
    }

    public function kelompok(Request $request)
    {
        return redirect()->route('dosen.kelompok');
    }

    protected function currentKelas(): ?array
    {
        $id = session('dosen_active_class_id');

        if (! $id) {
            return null;
        }

        return collect($this->kelasList())->firstWhere('id', $id);
    }

    public function laporanAktivitas(Request $request)
    {
        $aktivitasDb = collect();

        // 1. Ambil logbook dari database jika tersedia
        try {
            $logbooks = LogbookMilestone::with(['kelompok.kelas', 'mahasiswa'])->latest()->get();
            foreach ($logbooks as $lb) {
                $statusSlug = match (strtolower($lb->status ?? '')) {
                    'disetujui' => 'disetujui',
                    'revisi', 'perlu revisi' => 'perlu-revisi',
                    default => 'menunggu-review',
                };
                $aktivitasDb->push([
                    'id' => 'db-lb-'.$lb->id,
                    'item_id' => $lb->id,
                    'kelompok_id' => $lb->kelompok_id ?? 1,
                    'kelompok_nama' => $lb->kelompok?->nama_kelompok ?? 'Kelompok',
                    'kelas' => $lb->kelompok?->kelas?->nama_kelas ?? 'SIB 3B',
                    'proyek_nama' => $lb->kelompok?->judul_proyek ?? 'Proyek PBL',
                    'jenis_laporan' => 'logbook',
                    'judul' => 'Logbook: '.($lb->judul ?? 'Catatan Mingguan'),
                    'pengunggah' => $lb->mahasiswa?->nama ?? 'Mahasiswa',
                    'waktu' => $lb->created_at ? $lb->created_at->translatedFormat('d F Y, H:i') : now()->translatedFormat('d F Y, H:i'),
                    'status' => $statusSlug,
                    'url' => route('dosen.laporan-aktivitas.logbook', $lb->id),
                    'status_label' => match ($statusSlug) {
                        'disetujui' => 'Disetujui',
                        'perlu-revisi' => 'Perlu revisi',
                        default => 'Menunggu review',
                    },
                    'status_badge' => match ($statusSlug) {
                        'disetujui' => 'bg-[#ECFDF5] text-[#065F46]',
                        'perlu-revisi' => 'bg-[#FEE2E2] text-[#991B1B]',
                        default => 'bg-[#FEF3C7] text-[#92400E]',
                    },
                    'status_dot' => match ($statusSlug) {
                        'disetujui' => '#059669',
                        'perlu-revisi' => '#DC2626',
                        default => '#D97706',
                    },
                    'icon_bg' => '#FDF6EE',
                    'icon_color' => '#D97706',
                    'is_database' => true,
                ]);
            }
        } catch (\Throwable $e) {
            // Logbook query failsafe
        }

        // 2. Ambil proposal dari database jika tersedia
        try {
            $proposals = Proposal::with(['kelompok.kelas'])->latest()->get();
            foreach ($proposals as $prop) {
                $statusSlug = match (strtolower($prop->status ?? '')) {
                    'disetujui' => 'disetujui',
                    'ditolak', 'revisi' => 'perlu-revisi',
                    default => 'menunggu-review',
                };
                $aktivitasDb->push([
                    'id' => 'db-prop-'.$prop->id,
                    'item_id' => $prop->id,
                    'kelompok_id' => $prop->kelompok_id ?? 1,
                    'kelompok_nama' => $prop->kelompok?->nama_kelompok ?? 'Kelompok',
                    'kelas' => $prop->kelompok?->kelas?->nama_kelas ?? 'SIB 3B',
                    'proyek_nama' => $prop->judul ?? 'Pengajuan Proposal',
                    'jenis_laporan' => 'proposal',
                    'judul' => 'Proposal: '.($prop->judul ?? 'Pengajuan Draf'),
                    'pengunggah' => 'Ketua Kelompok',
                    'waktu' => $prop->tanggal_pengajuan ? $prop->tanggal_pengajuan->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                    'status' => $statusSlug,
                    'url' => route('dosen.laporan-aktivitas.proposal', $prop->id),
                    'status_label' => match ($statusSlug) {
                        'disetujui' => 'Disetujui',
                        'perlu-revisi' => 'Perlu revisi',
                        default => 'Menunggu review',
                    },
                    'status_badge' => match ($statusSlug) {
                        'disetujui' => 'bg-[#ECFDF5] text-[#065F46]',
                        'perlu-revisi' => 'bg-[#FEE2E2] text-[#991B1B]',
                        default => 'bg-[#FEF3C7] text-[#92400E]',
                    },
                    'status_dot' => match ($statusSlug) {
                        'disetujui' => '#059669',
                        'perlu-revisi' => '#DC2626',
                        default => '#D97706',
                    },
                    'icon_bg' => '#FEE2E2',
                    'icon_color' => '#DC2626',
                    'is_database' => true,
                ]);
            }
        } catch (\Throwable $e) {
            // Proposal query failsafe
        }

        // 3. Ambil berkas pengumpulan milestone dari database jika tersedia
        try {
            $submissions = PengumpulanMilestone::with(['kelompok.kelas', 'pengunggah', 'milestone'])->latest()->get();
            foreach ($submissions as $sub) {
                $statusSlug = match (strtolower($sub->status ?? '')) {
                    'disetujui', 'selesai' => 'disetujui',
                    'ditolak', 'revisi' => 'perlu-revisi',
                    default => 'menunggu-review',
                };
                $aktivitasDb->push([
                    'id' => 'db-sub-'.$sub->id,
                    'item_id' => $sub->id,
                    'kelompok_id' => $sub->kelompok_id ?? 1,
                    'kelompok_nama' => $sub->kelompok?->nama_kelompok ?? 'Kelompok',
                    'kelas' => $sub->kelompok?->kelas?->nama_kelas ?? 'SIB 3B',
                    'proyek_nama' => $sub->kelompok?->judul_proyek ?? 'Proyek PBL',
                    'jenis_laporan' => 'milestone',
                    'judul' => 'Milestone: '.($sub->milestone?->judul ?? 'Pengumpulan Berkas'),
                    'pengunggah' => $sub->pengunggah?->nama ?? 'Mahasiswa',
                    'waktu' => $sub->created_at ? $sub->created_at->translatedFormat('d F Y, H:i') : now()->translatedFormat('d F Y, H:i'),
                    'status' => $statusSlug,
                    'url' => ($sub->kelompok_id && $sub->milestone_id)
                        ? route('dosen.laporan-aktivitas.milestone', ['kelompok' => $sub->kelompok_id, 'milestone' => $sub->milestone_id])
                        : route('dosen.penyelesaian-milestone.index'),
                    'status_label' => match ($statusSlug) {
                        'disetujui' => 'Disetujui',
                        'perlu-revisi' => 'Perlu revisi',
                        default => 'Menunggu review',
                    },
                    'status_badge' => match ($statusSlug) {
                        'disetujui' => 'bg-[#ECFDF5] text-[#065F46]',
                        'perlu-revisi' => 'bg-[#FEE2E2] text-[#991B1B]',
                        default => 'bg-[#FEF3C7] text-[#92400E]',
                    },
                    'status_dot' => match ($statusSlug) {
                        'disetujui' => '#059669',
                        'perlu-revisi' => '#DC2626',
                        default => '#D97706',
                    },
                    'icon_bg' => '#ECFDF5',
                    'icon_color' => '#059669',
                    'is_database' => true,
                ]);
            }
        } catch (\Throwable $e) {
            // Milestone submission query failsafe
        }

        // 4. Data murni dari database (tanpa dummy mockup)
        $allAktivitas = $aktivitasDb;

        // 4. Filter Kelas
        if ($request->filled('kelas') && $request->kelas !== 'semua') {
            $allAktivitas = $allAktivitas->filter(fn ($item) => $item['kelas'] === $request->kelas);
        }

        // 5. Filter Jenis Laporan
        if ($request->filled('jenis') && $request->jenis !== 'semua') {
            $allAktivitas = $allAktivitas->filter(fn ($item) => $item['jenis_laporan'] === $request->jenis);
        }

        // 6. Filter Status Evaluasi
        if ($request->filled('status') && $request->status !== 'semua') {
            $allAktivitas = $allAktivitas->filter(fn ($item) => $item['status'] === $request->status);
        }

        // 7. Filter Pencarian (Keyword: judul, proyek, kelompok, pengunggah, kelas)
        if ($request->filled('search')) {
            $keyword = mb_strtolower(trim($request->search));
            if ($keyword !== '') {
                $allAktivitas = $allAktivitas->filter(function ($item) use ($keyword) {
                    return str_contains(mb_strtolower($item['judul'] ?? ''), $keyword)
                        || str_contains(mb_strtolower($item['proyek_nama'] ?? ''), $keyword)
                        || str_contains(mb_strtolower($item['kelompok_nama'] ?? ''), $keyword)
                        || str_contains(mb_strtolower($item['pengunggah'] ?? ''), $keyword)
                        || str_contains(mb_strtolower($item['kelas'] ?? ''), $keyword);
                });
            }
        }

        // 8. Paginasi: default 5 per halaman, parameter per_page didukung untuk pengujian
        $perPage = max(1, (int) $request->query('per_page', 5));
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $allAktivitas->values()->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $aktivitasList = new LengthAwarePaginator(
            $pagedItems,
            $allAktivitas->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $daftarKelas = Kelas::orderBy('nama_kelas')->pluck('nama_kelas')->unique()->values()->all();
        if (empty($daftarKelas)) {
            $daftarKelas = ['SIB 3A', 'SIB 3B'];
        }

        return view('dosen.aktifitas-laporan', compact('aktivitasList', 'daftarKelas'));
    }

    /**
     * Menampilkan detail entri logbook untuk ditinjau dosen.
     */
    public function showLogbook(int|string $id)
    {
        $logbook = LogbookMilestone::with([
            'kelompok.kelas',
            'kelompok.anggotaKelompok.mahasiswa',
            'mahasiswa',
        ])->findOrFail($id);

        $kelompok = $logbook->kelompok;

        return view('dosen.logbook.show', compact('logbook', 'kelompok'));
    }

    /**
     * Menyimpan hasil review dosen (setujui atau minta revisi).
     */
    public function reviewLogbook(Request $request, int|string $id)
    {
        $request->validate([
            'action' => 'required|in:setujui,revisi',
            'feedback' => 'nullable|string|max:2000',
        ]);

        $logbook = LogbookMilestone::with(['kelompok.mahasiswa'])->findOrFail($id);

        $action = $request->input('action');
        $feedback = $request->input('feedback');

        if ($action === 'revisi' && empty(trim((string) $feedback))) {
            return back()->withInput()->withErrors([
                'feedback' => 'Mohon sertakan catatan atau arahan revisi agar mahasiswa mengetahui apa yang perlu diperbaiki.',
            ]);
        }

        $logbook->status = ($action === 'setujui') ? 'disetujui' : 'revisi';
        $logbook->feedback = $feedback;
        $logbook->save();

        try {
            $recipients = collect();
            if ($logbook->kelompok) {
                $recipients = $logbook->kelompok->mahasiswa;
                if ($recipients->isEmpty()) {
                    $recipients = $logbook->kelompok->anggotaKelompok->map(fn ($a) => $a->mahasiswa)->filter();
                }
            }
            if ($logbook->mahasiswa) {
                $recipients = $recipients->push($logbook->mahasiswa);
            }

            foreach ($recipients->unique('id') as $mhs) {
                Notifikasi::create([
                    'penerima_type' => Mahasiswa::class,
                    'penerima_id' => $mhs->id,
                    'judul' => ($action === 'setujui') ? 'Logbook Disetujui' : 'Logbook Perlu Revisi',
                    'isi' => ($action === 'setujui')
                        ? 'Logbook "'.$logbook->judul.'" telah disetujui oleh dosen pembimbing.'
                        : 'Logbook "'.$logbook->judul.'" memerlukan revisi: '.Str::limit((string) $feedback, 120),
                    'tautan' => route('mahasiswa.logbook'),
                    'dibaca_pada' => null,
                ]);
            }
        } catch (\Throwable $e) {
            // Failsafe notifikasi
        }

        $pesan = ($action === 'setujui')
            ? 'Logbook berhasil disetujui dan catatan evaluasi telah disimpan.'
            : 'Permintaan revisi logbook berhasil dikirim ke mahasiswa.';

        return redirect()->route('dosen.laporan-aktivitas.logbook', $logbook->id)->with('success', $pesan);
    }

    /**
     * Menampilkan detail pengajuan proposal untuk ditinjau dosen.
     */
    public function showProposal(int|string $id)
    {
        $proposal = Proposal::with([
            'kelompok.kelas',
            'kelompok.periode',
            'kelompok.dosen',
            'kelompok.anggotaKelompok.mahasiswa',
        ])->findOrFail($id);

        $kelompok = $proposal->kelompok;

        return view('dosen.proposal.show', compact('proposal', 'kelompok'));
    }

    /**
     * Menyimpan hasil review dosen untuk proposal (setujui atau minta revisi).
     */
    public function reviewProposal(Request $request, int|string $id)
    {
        $request->validate([
            'action' => 'required|in:setujui,revisi',
            'catatan_revisi' => 'nullable|string|max:2000',
        ]);

        $proposal = Proposal::with(['kelompok.mahasiswa', 'kelompok.anggotaKelompok.mahasiswa'])->findOrFail($id);

        $action = $request->input('action');
        $catatan = $request->input('catatan_revisi');

        if ($action === 'revisi' && empty(trim((string) $catatan))) {
            return back()->withInput()->withErrors([
                'catatan_revisi' => 'Mohon sertakan catatan atau saran revisi agar mahasiswa mengetahui apa yang perlu diperbaiki.',
            ]);
        }

        $proposal->status = ($action === 'setujui') ? 'disetujui' : 'revisi';
        $proposal->catatan_revisi = $catatan;
        $proposal->save();

        // Jika disetujui dan kelompok belum memiliki judul proyek, perbarui judul proyek kelompok
        if ($action === 'setujui' && $proposal->kelompok && empty($proposal->kelompok->judul_proyek)) {
            $proposal->kelompok->update([
                'judul_proyek' => $proposal->judul,
            ]);
        }

        try {
            $recipients = collect();
            if ($proposal->kelompok) {
                $recipients = $proposal->kelompok->mahasiswa;
                if ($recipients->isEmpty()) {
                    $recipients = $proposal->kelompok->anggotaKelompok->map(fn ($a) => $a->mahasiswa)->filter();
                }
            }

            foreach ($recipients->unique('id') as $mhs) {
                Notifikasi::create([
                    'penerima_type' => Mahasiswa::class,
                    'penerima_id' => $mhs->id,
                    'judul' => ($action === 'setujui') ? 'Proposal Disetujui' : 'Proposal Perlu Revisi',
                    'isi' => ($action === 'setujui')
                        ? 'Proposal proyek "'.$proposal->judul.'" telah disetujui oleh dosen pembimbing.'
                        : 'Proposal proyek "'.$proposal->judul.'" memerlukan perbaikan: '.Str::limit((string) $catatan, 120),
                    'tautan' => route('mahasiswa.proposal'),
                    'dibaca_pada' => null,
                ]);
            }
        } catch (\Throwable $e) {
            // Failsafe notifikasi
        }

        $pesan = ($action === 'setujui')
            ? 'Proposal proyek berhasil disetujui dan catatan evaluasi telah disimpan.'
            : 'Permintaan revisi proposal berhasil dikirim ke mahasiswa.';

        return redirect()->route('dosen.laporan-aktivitas.proposal', $proposal->id)->with('success', $pesan);
    }

    /**
     * Mengunduh berkas lampiran proposal proyek.
     */
    public function downloadProposal(int|string $id)
    {
        $proposal = Proposal::findOrFail($id);

        if (! $proposal->file_proposal) {
            abort(404, 'Berkas proposal tidak ditemukan.');
        }

        $downloadName = $proposal->nama_berkas;

        if (Storage::disk('local')->exists($proposal->file_proposal)) {
            return Storage::disk('local')->download($proposal->file_proposal, $downloadName);
        }

        if (Storage::disk('public')->exists($proposal->file_proposal)) {
            return Storage::disk('public')->download($proposal->file_proposal, $downloadName);
        }

        abort(404, 'Berkas proposal tidak ditemukan.');
    }

    /**
     * Mengunduh berkas lampiran bukti kegiatan logbook.
     */
    public function downloadLogbook(int|string $id)
    {
        $logbook = LogbookMilestone::findOrFail($id);

        if (! $logbook->file_bukti) {
            abort(404, 'Berkas logbook tidak ditemukan.');
        }

        $extension = pathinfo($logbook->file_bukti, PATHINFO_EXTENSION) ?: 'pdf';
        $downloadName = 'Logbook-'.Str::slug($logbook->judul ?: 'PBL').'.'.$extension;

        if (Storage::disk('local')->exists($logbook->file_bukti)) {
            return Storage::disk('local')->download($logbook->file_bukti, $downloadName);
        }

        if (Storage::disk('public')->exists($logbook->file_bukti)) {
            return Storage::disk('public')->download($logbook->file_bukti, $downloadName);
        }

        abort(404, 'Berkas bukti logbook tidak ditemukan.');
    }

    /**
     * Menampilkan detail review terpadu pengumpulan milestone (3 mata kuliah) untuk kelompok.
     */
    public function showMilestone(Kelompok $kelompok, Milestone $milestone)
    {
        $kelompok->load(['kelas', 'periode', 'dosen', 'anggotaKelompok.mahasiswa']);

        $mataKuliahList = MataKuliah::query()
            ->where('is_aktif', true)
            ->whereIn('nama_matkul', MahasiswaController::MATA_KULIAH_SIM_PBL)
            ->get()
            ->sortBy(fn (MataKuliah $mk) => array_search($mk->nama_matkul, MahasiswaController::MATA_KULIAH_SIM_PBL, true))
            ->values();

        $laporanMatkul = [];
        $allApproved = true;
        $anyRevision = false;
        $anySubmitted = false;
        $latestSubmissionDate = null;

        foreach ($mataKuliahList as $mk) {
            $pengumpulan = PengumpulanMilestone::with('pengunggah')
                ->where('kelompok_id', $kelompok->id)
                ->where('milestone_id', $milestone->id)
                ->where('mata_kuliah', $mk->slug)
                ->first();

            $penyelesaian = PenyelesaianMilestone::query()
                ->where('kelompok_id', $kelompok->id)
                ->where('milestone_id', $milestone->id)
                ->where('mata_kuliah_id', $mk->id)
                ->first();

            $statusRaw = 'belum_dikumpulkan';
            if ($pengumpulan) {
                $anySubmitted = true;
                $statusRaw = strtolower($pengumpulan->status ?: 'diajukan');
                if ($pengumpulan->created_at && (! $latestSubmissionDate || $pengumpulan->created_at->gt($latestSubmissionDate))) {
                    $latestSubmissionDate = $pengumpulan->created_at;
                }
            }
            if ($penyelesaian && $penyelesaian->status === 'selesai') {
                $statusRaw = 'disetujui';
            } elseif ($penyelesaian && $penyelesaian->status === 'revisi') {
                $statusRaw = 'revisi';
            }

            if ($statusRaw === 'revisi') {
                $anyRevision = true;
                $allApproved = false;
            } elseif (! in_array($statusRaw, ['disetujui', 'selesai'], true)) {
                $allApproved = false;
            }

            $fileSize = null;
            $fileExt = 'PDF';
            if ($pengumpulan && $pengumpulan->file_path) {
                $fileExt = strtoupper(pathinfo($pengumpulan->file_name ?: $pengumpulan->file_path, PATHINFO_EXTENSION) ?: 'PDF');
                if (Storage::disk('local')->exists($pengumpulan->file_path)) {
                    $bytes = Storage::disk('local')->size($pengumpulan->file_path);
                    $fileSize = ($bytes >= 1048576) ? number_format($bytes / 1048576, 1).' MB' : number_format($bytes / 1024, 0).' KB';
                }
            }

            $laporanMatkul[] = [
                'mata_kuliah' => $mk,
                'pengumpulan' => $pengumpulan,
                'penyelesaian' => $penyelesaian,
                'status_raw' => $statusRaw,
                'status_label' => match ($statusRaw) {
                    'disetujui', 'selesai' => 'Disetujui',
                    'revisi' => 'Perlu Revisi',
                    'diajukan', 'menunggu review', 'menunggu' => 'Menunggu Review',
                    default => 'Belum Dikumpulkan',
                },
                'file_size' => $fileSize ?: '1.5 MB',
                'file_ext' => $fileExt,
            ];
        }

        $overallStatus = 'menunggu-review';
        if ($anyRevision) {
            $overallStatus = 'perlu-revisi';
        } elseif ($allApproved && $anySubmitted) {
            $overallStatus = 'disetujui';
        } elseif (! $anySubmitted) {
            $overallStatus = 'belum-dikumpulkan';
        }

        $catatanEvaluasi = PenyelesaianMilestone::query()
            ->where('kelompok_id', $kelompok->id)
            ->where('milestone_id', $milestone->id)
            ->whereNotNull('catatan')
            ->value('catatan');

        return view('dosen.milestone.show', compact(
            'kelompok',
            'milestone',
            'laporanMatkul',
            'overallStatus',
            'latestSubmissionDate',
            'catatanEvaluasi'
        ));
    }

    /**
     * Menyimpan hasil review dosen untuk pengumpulan milestone terpadu.
     */
    public function reviewMilestone(Request $request, Kelompok $kelompok, Milestone $milestone)
    {
        $request->validate([
            'action' => 'required|in:setujui,revisi',
            'catatan' => 'nullable|string|max:2000',
        ]);

        $action = $request->input('action');
        $catatan = $request->input('catatan');

        if ($action === 'revisi' && empty(trim((string) $catatan))) {
            return back()->withInput()->withErrors([
                'catatan' => 'Mohon sertakan arahan atau saran revisi agar mahasiswa mengetahui apa yang perlu diperbaiki.',
            ]);
        }

        /** @var Dosen|null $dosen */
        $dosen = Auth::guard('dosen')->user();
        $dosenId = $dosen?->id;

        $mataKuliahList = MataKuliah::query()
            ->where('is_aktif', true)
            ->whereIn('nama_matkul', MahasiswaController::MATA_KULIAH_SIM_PBL)
            ->get();

        $statusPengumpulan = ($action === 'setujui') ? 'disetujui' : 'revisi';
        $statusPenyelesaian = ($action === 'setujui') ? 'selesai' : 'berjalan';

        DB::transaction(function () use ($kelompok, $milestone, $mataKuliahList, $dosenId, $statusPengumpulan, $statusPenyelesaian, $catatan) {
            foreach ($mataKuliahList as $mk) {
                PengumpulanMilestone::query()
                    ->where('kelompok_id', $kelompok->id)
                    ->where('milestone_id', $milestone->id)
                    ->where('mata_kuliah', $mk->slug)
                    ->update([
                        'status' => $statusPengumpulan,
                    ]);

                $penyelesaian = PenyelesaianMilestone::firstOrNew([
                    'kelompok_id' => $kelompok->id,
                    'milestone_id' => $milestone->id,
                    'mata_kuliah_id' => $mk->id,
                ]);

                $penyelesaian->status = $statusPenyelesaian;
                $penyelesaian->catatan = $catatan;
                if ($dosenId) {
                    $penyelesaian->diverifikasi_oleh = $dosenId;
                }
                $penyelesaian->diverifikasi_pada = now();
                $penyelesaian->save();
            }
        });

        try {
            $recipients = $kelompok->mahasiswa;
            if ($recipients->isEmpty()) {
                $recipients = $kelompok->anggotaKelompok->map(fn ($a) => $a->mahasiswa)->filter();
            }

            foreach ($recipients->unique('id') as $mhs) {
                Notifikasi::create([
                    'penerima_type' => Mahasiswa::class,
                    'penerima_id' => $mhs->id,
                    'judul' => ($action === 'setujui') ? 'Milestone Disetujui: '.$milestone->judul : 'Milestone Perlu Revisi: '.$milestone->judul,
                    'isi' => ($action === 'setujui')
                        ? 'Laporan '.$milestone->judul.' untuk seluruh mata kuliah telah disetujui oleh dosen pembimbing.'
                        : 'Laporan '.$milestone->judul.' memerlukan perbaikan: '.Str::limit((string) $catatan, 120),
                    'tautan' => route('mahasiswa.milestone'),
                    'dibaca_pada' => null,
                ]);
            }
        } catch (\Throwable $e) {
            // Failsafe notifikasi
        }

        $pesan = ($action === 'setujui')
            ? 'Laporan milestone berhasil disetujui untuk seluruh mata kuliah.'
            : 'Permintaan revisi milestone berhasil dikirimkan ke kelompok mahasiswa.';

        return redirect()->route('dosen.laporan-aktivitas.milestone', [$kelompok->id, $milestone->id])->with('success', $pesan);
    }
}
