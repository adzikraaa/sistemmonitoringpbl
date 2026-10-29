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

        // Jika belum memilih semester, redirect balik ke pilih semester
        if (!$semesterId) {
            return redirect()->route('dosen.pilih-semester')
                ->with('info', 'Silakan pilih semester terlebih dahulu.');
        }

        // Cari data semester yang dipilih
        $semester = collect($this->semesterData())
            ->firstWhere('id', $semesterId);

        // Jika semester tidak ditemukan (ID tidak valid), reset dan redirect
        if (!$semester) {
            session()->forget('dosen.semester_id');
            return redirect()->route('dosen.pilih-semester')
                ->with('error', 'Semester tidak ditemukan. Silakan pilih ulang.');
        }

        // Baca kelas aktif menggunakan session key yang sama dengan DashboardController::selectKelas()
        // Session key: 'dosen_active_class_id' (integer), disimpan saat dosen memilih kelas di /dosen/pilih-kelas
        $activeClassId = session('dosen_active_class_id');

        // Lookup data kelas dari daftar yang sama dengan DashboardController::kelasList()
        // [MOCKUP] — akan diganti query DB saat tabel kelas + relasi dosen tersedia
        $kelasList = [
            ['id' => 1, 'nama' => 'SIB 3A', 'prodi' => 'Sistem Informasi Bisnis', 'semester' => 'Semester 5', 'jumlah_kelompok' => 6, 'progress' => 72, 'menunggu_review' => 3, 'periode' => 'Periode Ganjil Berjalan'],
            ['id' => 2, 'nama' => 'SIB 3B', 'prodi' => 'Sistem Informasi Bisnis', 'semester' => 'Semester 5', 'jumlah_kelompok' => 7, 'progress' => 68, 'menunggu_review' => 4, 'periode' => 'Periode Ganjil Berjalan'],
            ['id' => 3, 'nama' => 'SIB 3C', 'prodi' => 'Sistem Informasi Bisnis', 'semester' => 'Semester 5', 'jumlah_kelompok' => 5, 'progress' => 80, 'menunggu_review' => 2, 'periode' => 'Periode Ganjil Berjalan'],
            ['id' => 4, 'nama' => 'SIB 3D', 'prodi' => 'Sistem Informasi Bisnis', 'semester' => 'Semester 5', 'jumlah_kelompok' => 6, 'progress' => 61, 'menunggu_review' => 5, 'periode' => 'Periode Ganjil Berjalan'],
        ];

        $kelas = $activeClassId
            ? collect($kelasList)->firstWhere('id', $activeClassId)
            : null;

        return view('dosen.dashboard', array_merge(
            $this->sharedViewData(),
            [
                'semester' => $semester,
                'kelas'    => $kelas,   // null jika belum pilih kelas → blade tampilkan @else
            ]
        ));
    }
}
