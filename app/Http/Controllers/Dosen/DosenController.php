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
     * Membaca konteks semester dari session.
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

        // Jika semester tidak ditemukan (misal ID tidak valid), reset dan redirect
        if (!$semester) {
            $request->session()->forget('dosen.semester_id');
            return redirect()->route('dosen.pilih-semester')
                ->with('error', 'Semester tidak ditemukan. Silakan pilih ulang.');
        }

        // [MOCKUP] Data kelompok/kelas untuk prototype
        // Ganti dengan query DB (kelompok milik dosen ini pada semester terpilih)
        // setelah relasi dosen ↔ kelas ↔ kelompok ↔ semester tersedia.
        $kelompokList = [
            ['id' => 'k1', 'nama' => 'Kelompok 1', 'kelas' => 'SIB 3B', 'proyek' => 'Sistem Monitoring PBL', 'progress' => 60, 'status' => 'Berjalan'],
            ['id' => 'k2', 'nama' => 'Kelompok 2', 'kelas' => 'SIB 3B', 'proyek' => 'Aplikasi Inventaris Lab', 'progress' => 45, 'status' => 'Berjalan'],
            ['id' => 'k3', 'nama' => 'Kelompok 3', 'kelas' => 'SIB 3A', 'proyek' => 'Platform E-Learning', 'progress' => 72, 'status' => 'Berjalan'],
        ];

        return view('dosen.dashboard', array_merge(
            $this->sharedViewData(),
            [
                'semester'     => $semester,
                'kelompokList' => $kelompokList,
            ]
        ));
    }
}
