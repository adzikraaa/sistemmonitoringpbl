<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class KelompokController extends Controller
{
    private function sharedViewData(): array
    {
        $dosen = Auth::guard('dosen')->user();

        return [
            'userName'      => $dosen?->nama ?? 'Dosen Pengampu',
            'userNidn'      => $dosen?->nidn ?? '-',
            'userRole'      => 'Dosen Pengampu',
            'tahunAkademik' => '2026/2027',
        ];
    }

    /**
     * Data contoh multi-kelas & multi-status sesuai Figma untuk pengujian filter & detail.
     */
    private function sampleFigmaKelompok(): array
    {
        return [
            (object) [
                'id' => 101,
                'nama_kelompok' => 'Kelompok 01',
                'judul_proyek' => 'Sistem Informasi Inventaris',
                'kelas' => (object) ['nama_kelas' => 'SIB 3B'],
                'status' => 'Berjalan',
                'progress_dummy' => 78,
                'updated_at' => \Carbon\Carbon::parse('2026-10-24'),
                'anggotaKelompok' => collect([
                    (object) ['mahasiswa' => (object) ['nama' => 'Aulia Rahmawati', 'nim' => '244107060011']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Bagas Pratama', 'nim' => '244107060012']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Citra Maharani', 'nim' => '244107060013']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Dimas Saputra', 'nim' => '244107060014']],
                ]),
                'logbookMilestone' => collect(array_fill(0, 8, (object) [])),
            ],
            (object) [
                'id' => 102,
                'nama_kelompok' => 'Kelompok 02',
                'judul_proyek' => 'Pengembangan Sistem Monitoring PBL',
                'kelas' => (object) ['nama_kelas' => 'SIB 3B'],
                'status' => 'Review',
                'progress_dummy' => 64,
                'updated_at' => \Carbon\Carbon::parse('2026-10-25'),
                'anggotaKelompok' => collect([
                    (object) ['mahasiswa' => (object) ['nama' => 'Muhammad Adzikra', 'nim' => '244107060044']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Niky Tania Sari', 'nim' => '244107060053']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Mochammad Tanggaq', 'nim' => '244107060126']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Dina Mei', 'nim' => '244107060105']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Fernanda Ayu', 'nim' => '244107060123']],
                ]),
                'logbookMilestone' => collect(array_fill(0, 12, (object) [])),
            ],
            (object) [
                'id' => 103,
                'nama_kelompok' => 'Kelompok 03',
                'judul_proyek' => 'Platform Pemesanan UMKM',
                'kelas' => (object) ['nama_kelas' => 'SIB 3A'],
                'status' => 'Berjalan',
                'progress_dummy' => 52,
                'updated_at' => \Carbon\Carbon::parse('2026-10-23'),
                'anggotaKelompok' => collect([
                    (object) ['mahasiswa' => (object) ['nama' => 'Rizky Ramadhan', 'nim' => '244107060021']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Nadia Putri', 'nim' => '244107060022']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Fajar Nugroho', 'nim' => '244107060023']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Salsa Bila', 'nim' => '244107060024']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Hendra Wijaya', 'nim' => '244107060025']],
                ]),
                'logbookMilestone' => collect(array_fill(0, 6, (object) [])),
            ],
            (object) [
                'id' => 104,
                'nama_kelompok' => 'Kelompok 04',
                'judul_proyek' => 'Dashboard Analitik Penjualan',
                'kelas' => (object) ['nama_kelas' => 'SIB 3B'],
                'status' => 'Terlambat',
                'progress_dummy' => 31,
                'updated_at' => \Carbon\Carbon::parse('2026-10-18'),
                'anggotaKelompok' => collect([
                    (object) ['mahasiswa' => (object) ['nama' => 'Kevin Pratama', 'nim' => '244107060031']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Intan Permata', 'nim' => '244107060032']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Gilang Saputra', 'nim' => '244107060033']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Maya Sari', 'nim' => '244107060034']],
                ]),
                'logbookMilestone' => collect(array_fill(0, 3, (object) [])),
            ],
            (object) [
                'id' => 105,
                'nama_kelompok' => 'Kelompok 05',
                'judul_proyek' => 'Portal Magang Mahasiswa',
                'kelas' => (object) ['nama_kelas' => 'SIB 3A'],
                'status' => 'Review',
                'progress_dummy' => 46,
                'updated_at' => \Carbon\Carbon::parse('2026-10-25'),
                'anggotaKelompok' => collect([
                    (object) ['mahasiswa' => (object) ['nama' => 'Alvin Maulana', 'nim' => '244107060041']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Devi Anggraini', 'nim' => '244107060042']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Rafi Akbar', 'nim' => '244107060043']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Tari Lestari', 'nim' => '244107060045']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Yoga Pratama', 'nim' => '244107060046']],
                ]),
                'logbookMilestone' => collect(array_fill(0, 6, (object) [])),
            ],
            (object) [
                'id' => 106,
                'nama_kelompok' => 'Kelompok 06',
                'judul_proyek' => 'Pengembangan Laboratorium Virtual',
                'kelas' => (object) ['nama_kelas' => 'SIB 3C'],
                'status' => 'Berjalan',
                'progress_dummy' => 55,
                'updated_at' => \Carbon\Carbon::parse('2026-10-23'),
                'anggotaKelompok' => collect([
                    (object) ['mahasiswa' => (object) ['nama' => 'Bima Sakti', 'nim' => '244107060061']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Larasati Dewi', 'nim' => '244107060062']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Zaki Mubarak', 'nim' => '244107060063']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Anisa Fitri', 'nim' => '244107060064']],
                    (object) ['mahasiswa' => (object) ['nama' => 'Reza Pahlevi', 'nim' => '244107060065']],
                ]),
                'logbookMilestone' => collect(array_fill(0, 6, (object) [])),
            ],
        ];
    }

    public function index(Request $request)
    {
        // 1. Ambil data asli dari DB (dengan Eager Loading)
        $dbKelompok = Kelompok::with(['kelas', 'anggotaKelompok.mahasiswa', 'logbookMilestone'])->get();

        foreach ($dbKelompok as $item) {
            $item->progress_dummy = match ($item->status ?? 'Berjalan') {
                'Review' => 64,
                'Terlambat' => 31,
                default => 78,
            };
        }

        // 2. Gabungkan dengan data sampel Figma jika DB masih sedikit (< 6 data)
        // agar dropdown Kelas (SIB 3A, SIB 3B, SIB 3C) & Status bisa langsung diuji
        $allItems = $dbKelompok->count() >= 6
            ? $dbKelompok
            : $dbKelompok->concat($this->sampleFigmaKelompok());

        $totalTerdaftar = $allItems->count();

        // Daftar kelas unik untuk dropdown filter
        $daftarKelas = $allItems->map(function ($item) {
            return is_object($item->kelas) ? ($item->kelas->nama_kelas ?? null) : $item->kelas;
        })->filter()->unique()->sort()->values();

        // 3. Filter Pencarian (Live Search nama kelompok atau judul proyek)
        if ($request->filled('search')) {
            $keyword = mb_strtolower($request->search);
            $allItems = $allItems->filter(function ($item) use ($keyword) {
                return str_contains(mb_strtolower($item->nama_kelompok ?? ''), $keyword)
                    || str_contains(mb_strtolower($item->judul_proyek ?? ''), $keyword);
            });
        }

        // 4. Filter Dropdown Kelas
        if ($request->filled('kelas')) {
            $kelasFilter = $request->kelas;
            $allItems = $allItems->filter(function ($item) use ($kelasFilter) {
                $namaKelas = is_object($item->kelas) ? ($item->kelas->nama_kelas ?? null) : $item->kelas;
                return $namaKelas === $kelasFilter;
            });
        }

        // 5. Filter Dropdown Status
        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $allItems = $allItems->filter(function ($item) use ($statusFilter) {
                return ($item->status ?? 'Berjalan') === $statusFilter;
            });
        }

        // 6. Paginasi manual 8 item per halaman agar semua tampil rapi dalam 1 layar pengujian
        $perPage = 8;
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
        // Cek apakah ID berasal dari sampel Figma (101-106) atau database
        $sample = collect($this->sampleFigmaKelompok())->firstWhere('id', (int) $id);

        if ($sample) {
            $kelompok = $sample;
        } else {
            $kelompok = Kelompok::with(['kelas', 'anggotaKelompok.mahasiswa', 'logbookMilestone', 'proposal'])
                ->findOrFail($id);
        }

        return view('dosen.kelompok.show', array_merge(
            $this->sharedViewData(),
            compact('kelompok')
        ));
    }

    /**
     * Mengirim pengingat bimbingan ke kelompok mahasiswa (Standar Laravel Form + Flash Session).
     */
    public function kirimPengingat(Request $request)
    {
        $validated = $request->validate([
            'kelompok_nama' => 'required|string|max:100',
            'pesan'         => 'required|string|max:1000',
        ], [
            'kelompok_nama.required' => 'Kelompok tujuan wajib dipilih.',
            'pesan.required'         => 'Pesan pengingat tidak boleh kosong.',
        ]);

        // TODO (Integrasi DB): Simpan ke tabel notifikasi_mahasiswa setelah tabel notifikasi siap
        // Notifikasi::create([...]);

        return redirect()
            ->route('dosen.kelompok')
            ->with('success', 'Pengingat bimbingan berhasil dikirim ke ' . $validated['kelompok_nama'] . '!');
    }
    
}