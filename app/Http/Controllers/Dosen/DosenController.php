<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    /**
     * Data bersama yang dipass ke semua view Dosen.
     * Ambil dari guard 'dosen' jika sudah login,
     * fallback ke data stub untuk pengembangan.
     */
    private function sharedViewData(): array
    {
        $dosen = Auth::guard('dosen')->user();

        return [
            'userName'      => $dosen?->nama  ?? 'Dosen Pengampu',
            'userNidn'      => $dosen?->nidn  ?? '-',
            'userRole'      => 'Dosen Pengampu',
            // [MOCKUP] Tahun akademik aktif — ganti dengan query ke tabel
            // semester/tahun-akademik jika tabel tersebut sudah tersedia di DB.
            'tahunAkademik' => '2026/2027',
        ];
    }

    /**
     * [MOCKUP] Data semester statis untuk prototype.
     * Pisahkan dari data produksi dengan label komentar [MOCKUP].
     * Ganti dengan query DB setelah tabel semester + relasi dosen tersedia.
     *
     * @return array
     */
    private function semesterData(): array
    {
        return [
            [
                'id'              => 'smt5',
                'tingkat'         => 'PBL TINGKAT III',
                'nama'            => 'Semester 5',
                'tahun'           => 'Tahun Akademik 2026/2027',
                'status'          => 'berjalan',       // 'berjalan' | 'selesai'
                'deskripsi'       => 'Periode PBL yang sedang berlangsung. Kelola 6 tim aktif, logbook mingguan, dan evaluasi milestone.',
                'progress'        => 56,
                'minggu_ke'       => 9,
                'total_minggu'    => 16,
                'status_penilaian'=> null,
            ],
            [
                'id'              => 'smt4',
                'tingkat'         => 'PBL TINGKAT II',
                'nama'            => 'Semester 4',
                'tahun'           => 'Tahun Akademik 2025/2026',
                'status'          => 'selesai',
                'deskripsi'       => 'Arsip data bimbingan periode genap lalu. Akses portofolio produk dan repositori tugas akhir mahasiswa.',
                'progress'        => 100,
                'minggu_ke'       => null,
                'total_minggu'    => null,
                'status_penilaian'=> 'Terarsip',
            ],
            [
                'id'              => 'smt3',
                'tingkat'         => 'PBL TINGKAT II',
                'nama'            => 'Semester 3',
                'tahun'           => 'Tahun Akademik 2025/2026',
                'status'          => 'selesai',
                'deskripsi'       => 'Riwayat rekapitulasi nilai sidang pra-proyek, berkas bimbingan dosen, serta lembar evaluasi mitra industri.',
                'progress'        => 100,
                'minggu_ke'       => null,
                'total_minggu'    => null,
                'status_penilaian'=> 'Terarsip',
            ],
            [
                'id'              => 'smt2',
                'tingkat'         => 'PBL TINGKAT I',
                'nama'            => 'Semester 2',
                'tahun'           => 'Tahun Akademik 2024/2025',
                'status'          => 'selesai',
                'deskripsi'       => 'Riwayat rekapitulasi nilai sidang pra-proyek, berkas bimbingan dosen, serta lembar evaluasi mitra industri.',
                'progress'        => 100,
                'minggu_ke'       => null,
                'total_minggu'    => null,
                'status_penilaian'=> 'Terarsip',
            ],
        ];
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
        $validIds = collect($this->semesterData())->pluck('id')->implode(',');

        $request->validate([
            'semester_id' => 'required|string|in:' . $validIds,
        ]);

        // Simpan konteks semester aktif ke session guard dosen
        $request->session()->put('dosen.semester_id', $request->semester_id);

        // Langsung ke Dashboard Dosen — bukan Pilih Kelas
        return redirect()->route('dosen.dashboard');
    }

    /**
     * Dashboard Dosen Pengampu.
     * Membaca konteks semester dari session (dosen.semester_id)
     * dan kelas aktif dari session (dosen_active_class_id) milik DashboardController.
     */
    public function dashboard()
    {
        $semesterId = session('dosen.semester_id');

        // Cari data semester yang dipilih (opsional — tidak redirect jika kosong)
        $semester = $semesterId
            ? collect($this->semesterData())->firstWhere('id', $semesterId)
            : null;

        // ── [MOCKUP] Data dashboard ───────────────────────────────────────
        // Ganti dengan query Eloquent ke model Kelompok, Logbook, Proposal
        // saat tabel DB sudah tersedia.
        // ─────────────────────────────────────────────────────────────────

        // Summary stats
        $summaryData = [
            'total_kelompok' => 3,
            'laporan_masuk'  => 12,
            'belum_review'   => 4,
            'perlu_revisi'   => 3,
            'terlambat'      => 1,
        ];

        // Peta bimbingan — daftar kelompok yang dibimbing dosen ini
        $kelompokData = [
            [
                'nama'        => 'Kelompok 01',
                'kelas'       => 'SIB 3A',
                'proyek'      => 'Pengembangan Sistem Monitoring PBL',
                'progress'    => 65,
                'status'      => 'berjalan',
                'last_update' => \Carbon\Carbon::now()->subHours(2),
            ],
            [
                'nama'        => 'Kelompok 02',
                'kelas'       => 'SIB 3B',
                'proyek'      => 'Platform Kolaborasi Laboratorium',
                'progress'    => 48,
                'status'      => 'terlambat',
                'last_update' => \Carbon\Carbon::now()->subHours(4),
            ],
            [
                'nama'        => 'Kelompok 01',
                'kelas'       => 'SIB 3B',
                'proyek'      => 'Sistem Informasi Inventaris Kampus',
                'progress'    => 82,
                'status'      => 'selesai',
                'last_update' => \Carbon\Carbon::now()->subHours(5),
            ],
        ];

        // Sinyal minggu ini — kelompok yang belum aktif (null = semua aktif)
        $sinyalKelompok = [
            'nama'     => 'Kelompok Reka',
            'hari_lalu' => 3,
        ];

        // Dokumen yang menunggu review dosen
        $pendingReviews = [
            ['type' => 'Logbook',  'label' => 'Kelompok 1 SIB 3A', 'name' => 'Dzikra'],
            ['type' => 'Proposal', 'label' => 'Kelompok 2 SIB 3B', 'name' => 'Angga'],
            ['type' => 'Logbook',  'label' => 'Kelompok 1 SIB 3B', 'name' => 'Niky'],
        ];

        return view('dosen.dashboard', array_merge(
            $this->sharedViewData(),
            [
                'semester'       => $semester,
                'summaryData'    => $summaryData,
                'kelompokData'   => $kelompokData,
                'sinyalKelompok' => $sinyalKelompok,
                'pendingReviews' => $pendingReviews,
            ]
        ));
    }

    /**
     * Halaman Notifikasi Dosen.
     */
    public function notifikasi()
    {
        // [MOCKUP] — ganti dengan query notifikasi dari DB
        $notifikasi = [
            [
                'id'      => 1,
                'jenis'   => 'logbook',
                'pesan'   => 'Kelompok 01 SIB 3A mengumpulkan logbook minggu ke-9.',
                'waktu'   => \Carbon\Carbon::now()->subMinutes(30),
                'dibaca'  => false,
            ],
            [
                'id'      => 2,
                'jenis'   => 'proposal',
                'pesan'   => 'Proposal baru dari Kelompok 02 SIB 3B menunggu review.',
                'waktu'   => \Carbon\Carbon::now()->subHours(2),
                'dibaca'  => false,
            ],
            [
                'id'      => 3,
                'jenis'   => 'pengingat',
                'pesan'   => 'Kelompok Reka belum mengirim pembaruan sejak 3 hari lalu.',
                'waktu'   => \Carbon\Carbon::now()->subDays(1),
                'dibaca'  => true,
            ],
        ];

        return view('dosen.notifikasi', array_merge(
            $this->sharedViewData(),
            ['notifikasi' => $notifikasi]
        ));
    }
}
