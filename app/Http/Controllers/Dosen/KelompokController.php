<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\PengumpulanMilestone;
use App\Models\Periode;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class KelompokController extends Controller
{
    private function sharedViewData(): array
    {
        $dosen = Auth::guard('dosen')->user();

        return [
            'userName' => $dosen?->nama ?? 'Dosen Pengampu',
            'userNidn' => $dosen?->nidn ?? '-',
            'userRole' => 'Dosen Pengampu',
            'tahunAkademik' => Periode::query()->where('is_aktif', true)->value('tahun_akademik') ?? '2026/2027',
        ];
    }

    public function index(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();
        $kelasIdsPengampu = $dosen?->dosenPengampu()->pluck('kelas_id')->filter()->unique() ?? collect();

        // 1. Ambil data asli dari DB (dengan Eager Loading)
        $query = Kelompok::with(['kelas', 'anggotaKelompok.mahasiswa', 'logbookMilestone', 'proposal']);

        if ($dosen) {
            $query->where(function ($q) use ($dosen, $kelasIdsPengampu) {
                $q->where('dosen_id', $dosen->id);
                if ($kelasIdsPengampu->isNotEmpty()) {
                    $q->orWhereIn('kelas_id', $kelasIdsPengampu);
                }
            });
        }

        $allItems = $query->get();

        // Fallback jika belum ada kelompok yang terhubung spesifik ke dosen ini, ambil semua kelompok
        if ($allItems->isEmpty()) {
            $allItems = Kelompok::with(['kelas', 'anggotaKelompok.mahasiswa', 'logbookMilestone', 'proposal'])->get();
        }

        // Hitung progres riil menggunakan ProgressService
        $progressMap = app(ProgressService::class)->untukDaftar($allItems);

        foreach ($allItems as $item) {
            $item->progress = (int) ($progressMap[$item->id]['persen'] ?? 0);
            if (strtolower($item->status ?? '') === 'aktif') {
                $item->status = 'Berjalan';
            }
        }

        $totalTerdaftar = $allItems->count();

        // Daftar kelas unik dari database
        $daftarKelas = Kelas::orderBy('nama_kelas')->pluck('nama_kelas')->unique()->values();
        if ($daftarKelas->isEmpty()) {
            $daftarKelas = $allItems->map(function ($item) {
                return is_object($item->kelas) ? ($item->kelas->nama_kelas ?? null) : $item->kelas;
            })->filter()->unique()->sort()->values();
        }

        // 2. Filter Pencarian (nama kelompok, judul proyek, atau nama/NIM anggota)
        if ($request->filled('search')) {
            $keyword = mb_strtolower(trim($request->search));
            if ($keyword !== '') {
                $allItems = $allItems->filter(function ($item) use ($keyword) {
                    $matchNamaKelompok = str_contains(mb_strtolower($item->nama_kelompok ?? ''), $keyword);
                    $matchJudulProyek = str_contains(mb_strtolower($item->judul_proyek ?? ''), $keyword);
                    $matchAnggota = false;

                    if (isset($item->anggotaKelompok) && is_iterable($item->anggotaKelompok)) {
                        foreach ($item->anggotaKelompok as $ak) {
                            $namaMhs = mb_strtolower($ak->mahasiswa->nama ?? '');
                            $nimMhs = mb_strtolower($ak->mahasiswa->nim ?? '');
                            if (str_contains($namaMhs, $keyword) || str_contains($nimMhs, $keyword)) {
                                $matchAnggota = true;
                                break;
                            }
                        }
                    }

                    return $matchNamaKelompok || $matchJudulProyek || $matchAnggota;
                });
            }
        }

        // 3. Filter Dropdown Kelas
        if ($request->filled('kelas')) {
            $kelasFilter = $request->kelas;
            $allItems = $allItems->filter(function ($item) use ($kelasFilter) {
                $namaKelas = is_object($item->kelas) ? ($item->kelas->nama_kelas ?? null) : $item->kelas;

                return $namaKelas === $kelasFilter;
            });
        }

        // 4. Filter Dropdown Status
        if ($request->filled('status')) {
            $statusFilter = strtolower($request->status);
            $allItems = $allItems->filter(function ($item) use ($statusFilter) {
                return strtolower($item->status ?? 'berjalan') === $statusFilter;
            });
        }

        // 5. Paginasi manual (default 8 item per halaman)
        $perPage = max(1, (int) $request->query('per_page', 8));
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $allItems->values()->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $kelompokList = new LengthAwarePaginator(
            $pagedItems,
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('dosen.kelompok.index', array_merge(
            $this->sharedViewData(),
            compact('kelompokList', 'totalTerdaftar', 'daftarKelas')
        ));
    }

    public function show($id)
    {
        $kelompok = Kelompok::with([
            'kelas',
            'periode',
            'dosen',
            'anggotaKelompok.mahasiswa',
            'logbookMilestone',
            'proposal' => fn ($q) => $q->latest(),
        ])->findOrFail($id);

        $proposal = $kelompok->proposal->first();
        $logbookList = $kelompok->logbookMilestone;
        $pengumpulanMilestone = PengumpulanMilestone::with('milestone')
            ->where('kelompok_id', $kelompok->id)
            ->latest()
            ->get();

        return view('dosen.kelompok.show', array_merge(
            $this->sharedViewData(),
            compact('kelompok', 'proposal', 'logbookList', 'pengumpulanMilestone')
        ));
    }

    /**
     * Mengirim pengingat bimbingan ke kelompok mahasiswa (Standar Laravel Form + Flash Session).
     */
    public function kirimPengingat(Request $request)
    {
        $validated = $request->validate([
            'kelompok_nama' => 'required|string|max:100',
            'pesan' => 'required|string|max:1000',
        ], [
            'kelompok_nama.required' => 'Kelompok tujuan wajib dipilih.',
            'pesan.required' => 'Pesan pengingat tidak boleh kosong.',
        ]);

        return redirect()
            ->route('dosen.kelompok')
            ->with('success', 'Pengingat bimbingan berhasil dikirim ke '.$validated['kelompok_nama'].'!');
    }
}
