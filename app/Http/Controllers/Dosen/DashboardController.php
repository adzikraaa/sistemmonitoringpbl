<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    protected function kelasList(): array
    {
        return [
            [
                'id' => 1,
                'nama' => 'SIB 3A',
                'prodi' => 'Sistem Informasi Bisnis',
                'semester' => 'Semester 5',
                'jumlah_kelompok' => 6,
                'progress' => 72,
                'menunggu_review' => 3,
                'periode' => 'Periode Ganjil Berjalan',
            ],
            [
                'id' => 2,
                'nama' => 'SIB 3B',
                'prodi' => 'Sistem Informasi Bisnis',
                'semester' => 'Semester 5',
                'jumlah_kelompok' => 7,
                'progress' => 68,
                'menunggu_review' => 4,
                'periode' => 'Periode Ganjil Berjalan',
            ],
            [
                'id' => 3,
                'nama' => 'SIB 3C',
                'prodi' => 'Sistem Informasi Bisnis',
                'semester' => 'Semester 5',
                'jumlah_kelompok' => 5,
                'progress' => 80,
                'menunggu_review' => 2,
                'periode' => 'Periode Ganjil Berjalan',
            ],
            [
                'id' => 4,
                'nama' => 'SIB 3D',
                'prodi' => 'Sistem Informasi Bisnis',
                'semester' => 'Semester 5',
                'jumlah_kelompok' => 6,
                'progress' => 61,
                'menunggu_review' => 5,
                'periode' => 'Periode Ganjil Berjalan',
            ],
        ];
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

        return back()->with('success', 'Kelas '. $kelas['nama'] .' berhasil dipilih.');
    }

    public function kelompok(Request $request)
    {
        $classId = $request->query('class_id', session('dosen_active_class_id'));

        if (! $classId) {
            return redirect()->route('dosen.pilih-kelas')->with('warning', 'Pilih kelas terlebih dahulu untuk masuk ke ruang kelompok.');
        }

        $kelas = collect($this->kelasList())->firstWhere('id', $classId);

        if (! $kelas) {
            return redirect()->route('dosen.pilih-kelas')->with('warning', 'Kelas yang dipilih tidak tersedia.');
        }

        session(['dosen_active_class_id' => $kelas['id']]);

        $kelompok = [
            ['id' => 1, 'kelas_id' => 1, 'nama' => 'Kelompok 01', 'ketua' => 'Rafi Nurfadilah', 'jumlah_anggota' => 5, 'status' => 'Aktif'],
            ['id' => 2, 'kelas_id' => 1, 'nama' => 'Kelompok 02', 'ketua' => 'Hana Aulia', 'jumlah_anggota' => 5, 'status' => 'Review'],
            ['id' => 3, 'kelas_id' => 1, 'nama' => 'Kelompok 03', 'ketua' => 'Iqbal Rahman', 'jumlah_anggota' => 5, 'status' => 'Aktif'],
            ['id' => 4, 'kelas_id' => 2, 'nama' => 'Kelompok 01', 'ketua' => 'Dika Pratama', 'jumlah_anggota' => 5, 'status' => 'Aktif'],
            ['id' => 5, 'kelas_id' => 2, 'nama' => 'Kelompok 02', 'ketua' => 'Nadya Putri', 'jumlah_anggota' => 5, 'status' => 'Review'],
            ['id' => 6, 'kelas_id' => 3, 'nama' => 'Kelompok 01', 'ketua' => 'Ayu Lestari', 'jumlah_anggota' => 5, 'status' => 'Aktif'],
            ['id' => 7, 'kelas_id' => 4, 'nama' => 'Kelompok 01', 'ketua' => 'Fajri Ramadhan', 'jumlah_anggota' => 5, 'status' => 'Aktif'],
        ];

        return view('dosen.kelompok', [
            'kelas' => $kelas,
            'kelompok' => collect($kelompok)->where('kelas_id', $classId)->values()->all(),
        ]);
    }

    protected function currentKelas(): ?array
    {
        $id = session('dosen_active_class_id');

        if (! $id) {
            return null;
        }

        return collect($this->kelasList())->firstWhere('id', $id);
    }

    protected function sampleAktivitas(): array
    {
        return [
            [
                'id' => 'act-1',
                'kelompok_id' => 1,
                'kelompok_nama' => 'Kelompok 01',
                'kelas' => 'SIB 3A',
                'proyek_nama' => 'Pengembangan Sistem Magang',
                'jenis_laporan' => 'logbook',
                'judul' => 'Logbook: Implementasi autentikasi & perancangan token JWT',
                'pengunggah' => 'Andi Pratama',
                'waktu' => '28 September 2026, 14:15',
                'status' => 'menunggu-review',
                'status_label' => 'Menunggu review',
                'status_badge' => 'bg-[#FEF3C7] text-[#92400E]',
                'status_dot' => '#D97706',
                'icon_bg' => '#FDF6EE',
                'icon_color' => '#D97706',
                'is_database' => false,
            ],
            [
                'id' => 'act-2',
                'kelompok_id' => 1,
                'kelompok_nama' => 'Kelompok 01',
                'kelas' => 'SIB 3B',
                'proyek_nama' => 'Platform Kolaborasi Laboratorium',
                'jenis_laporan' => 'proposal',
                'judul' => 'Proposal: Revisi latar belakang & batasan implementasi sensor IoT',
                'pengunggah' => 'Dimas Arya',
                'waktu' => '28 September 2026, 14:15',
                'status' => 'perlu-revisi',
                'status_label' => 'Perlu revisi',
                'status_badge' => 'bg-[#FEE2E2] text-[#991B1B]',
                'status_dot' => '#DC2626',
                'icon_bg' => '#FEE2E2',
                'icon_color' => '#DC2626',
                'is_database' => false,
            ],
            [
                'id' => 'act-3',
                'kelompok_id' => 1,
                'kelompok_nama' => 'Kelompok 01',
                'kelas' => 'SIB 3C',
                'proyek_nama' => 'Sistem Informasi Inventaris Kampus',
                'jenis_laporan' => 'logbook',
                'judul' => 'Logbook: Pengujian unit testing modul inventaris barang',
                'pengunggah' => 'Siti Rahma',
                'waktu' => 'Disetujui kemarin pada 20:10',
                'status' => 'disetujui',
                'status_label' => 'Disetujui',
                'status_badge' => 'bg-[#ECFDF5] text-[#065F46]',
                'status_dot' => '#059669',
                'icon_bg' => '#ECFDF5',
                'icon_color' => '#059669',
                'is_database' => false,
            ],
            [
                'id' => 'act-4',
                'kelompok_id' => 1,
                'kelompok_nama' => 'Kelompok 01',
                'kelas' => 'SIB 3D',
                'proyek_nama' => 'Pengembangan Situs Web Perpustakaan Kampus',
                'jenis_laporan' => 'logbook',
                'judul' => 'Logbook: Penyusunan skema database ERD & relasi tabel',
                'pengunggah' => 'Budi Santoso',
                'waktu' => 'Disetujui pada 25 Sep 2026',
                'status' => 'disetujui',
                'status_label' => 'Disetujui',
                'status_badge' => 'bg-[#ECFDF5] text-[#065F46]',
                'status_dot' => '#059669',
                'icon_bg' => '#ECFDF5',
                'icon_color' => '#059669',
                'is_database' => false,
            ],
            [
                'id' => 'act-5',
                'kelompok_id' => 1,
                'kelompok_nama' => 'Kelompok 01',
                'kelas' => 'SIB 3E',
                'proyek_nama' => 'Pengembangan Sistem Alarm Pintar',
                'jenis_laporan' => 'milestone',
                'judul' => 'Milestone 1 : Laporan Progress Minggu 1–4',
                'pengunggah' => 'Andika Sulaiman',
                'waktu' => 'Disetujui pada 8 Okt 2026',
                'status' => 'disetujui',
                'status_label' => 'Disetujui',
                'status_badge' => 'bg-[#ECFDF5] text-[#065F46]',
                'status_dot' => '#059669',
                'icon_bg' => '#ECFDF5',
                'icon_color' => '#059669',
                'is_database' => false,
            ],
        ];
    }

    public function laporanAktivitas(Request $request)
    {
        $aktivitasDb = collect();

        // 1. Ambil logbook dari database jika tersedia
        try {
            $logbooks = LogbookMilestone::with(['kelompok.kelas', 'mahasiswa'])->latest()->get();
            foreach ($logbooks as $lb) {
                $statusSlug = match(strtolower($lb->status ?? '')) {
                    'disetujui'              => 'disetujui',
                    'revisi', 'perlu revisi' => 'perlu-revisi',
                    default                  => 'menunggu-review',
                };
                $aktivitasDb->push([
                    'id'            => 'db-lb-' . $lb->id,
                    'kelompok_id'   => $lb->kelompok_id ?? 1,
                    'kelompok_nama' => $lb->kelompok?->nama_kelompok ?? 'Kelompok',
                    'kelas'         => $lb->kelompok?->kelas?->nama_kelas ?? 'SIB 3B',
                    'proyek_nama'   => $lb->kelompok?->judul_proyek ?? 'Proyek PBL',
                    'jenis_laporan' => 'logbook',
                    'judul'         => 'Logbook: ' . ($lb->judul ?? 'Catatan Mingguan'),
                    'pengunggah'    => $lb->mahasiswa?->nama ?? 'Mahasiswa',
                    'waktu'         => $lb->created_at ? $lb->created_at->translatedFormat('d F Y, H:i') : now()->translatedFormat('d F Y, H:i'),
                    'status'        => $statusSlug,
                    'status_label'  => match($statusSlug) {
                        'disetujui'    => 'Disetujui',
                        'perlu-revisi' => 'Perlu revisi',
                        default        => 'Menunggu review',
                    },
                    'status_badge'  => match($statusSlug) {
                        'disetujui'    => 'bg-[#ECFDF5] text-[#065F46]',
                        'perlu-revisi' => 'bg-[#FEE2E2] text-[#991B1B]',
                        default        => 'bg-[#FEF3C7] text-[#92400E]',
                    },
                    'status_dot'    => match($statusSlug) {
                        'disetujui'    => '#059669',
                        'perlu-revisi' => '#DC2626',
                        default        => '#D97706',
                    },
                    'icon_bg'       => '#FDF6EE',
                    'icon_color'    => '#D97706',
                    'is_database'   => true,
                ]);
            }
        } catch (\Throwable $e) {
            // Logbook query failsafe
        }

        // 2. Ambil proposal dari database jika tersedia
        try {
            $proposals = Proposal::with(['kelompok.kelas'])->latest()->get();
            foreach ($proposals as $prop) {
                $statusSlug = match(strtolower($prop->status ?? '')) {
                    'disetujui'         => 'disetujui',
                    'ditolak', 'revisi' => 'perlu-revisi',
                    default             => 'menunggu-review',
                };
                $aktivitasDb->push([
                    'id'            => 'db-prop-' . $prop->id,
                    'kelompok_id'   => $prop->kelompok_id ?? 1,
                    'kelompok_nama' => $prop->kelompok?->nama_kelompok ?? 'Kelompok',
                    'kelas'         => $prop->kelompok?->kelas?->nama_kelas ?? 'SIB 3B',
                    'proyek_nama'   => $prop->judul ?? 'Pengajuan Proposal',
                    'jenis_laporan' => 'proposal',
                    'judul'         => 'Proposal: ' . ($prop->judul ?? 'Pengajuan Draf'),
                    'pengunggah'    => 'Ketua Kelompok',
                    'waktu'         => $prop->tanggal_pengajuan ? $prop->tanggal_pengajuan->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                    'status'        => $statusSlug,
                    'status_label'  => match($statusSlug) {
                        'disetujui'    => 'Disetujui',
                        'perlu-revisi' => 'Perlu revisi',
                        default        => 'Menunggu review',
                    },
                    'status_badge'  => match($statusSlug) {
                        'disetujui'    => 'bg-[#ECFDF5] text-[#065F46]',
                        'perlu-revisi' => 'bg-[#FEE2E2] text-[#991B1B]',
                        default        => 'bg-[#FEF3C7] text-[#92400E]',
                    },
                    'status_dot'    => match($statusSlug) {
                        'disetujui'    => '#059669',
                        'perlu-revisi' => '#DC2626',
                        default        => '#D97706',
                    },
                    'icon_bg'       => '#FEE2E2',
                    'icon_color'    => '#DC2626',
                    'is_database'   => true,
                ]);
            }
        } catch (\Throwable $e) {
            // Proposal query failsafe
        }

        // 3. Gabungkan data: DB di awal, disusul sampel mockup
        $allAktivitas = $aktivitasDb->concat($this->sampleAktivitas());

        // 4. Filter Kelas
        if ($request->filled('kelas') && $request->kelas !== 'semua') {
            $allAktivitas = $allAktivitas->filter(fn($item) => $item['kelas'] === $request->kelas);
        }

        // 5. Filter Jenis Laporan
        if ($request->filled('jenis') && $request->jenis !== 'semua') {
            $allAktivitas = $allAktivitas->filter(fn($item) => $item['jenis_laporan'] === $request->jenis);
        }

        // 6. Filter Status Evaluasi
        if ($request->filled('status') && $request->status !== 'semua') {
            $allAktivitas = $allAktivitas->filter(fn($item) => $item['status'] === $request->status);
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

        $daftarKelas = ['SIB 3A', 'SIB 3B', 'SIB 3C', 'SIB 3D', 'SIB 3E'];

        return view('dosen.aktifitas-laporan', compact('aktivitasList', 'daftarKelas'));
    }
}
