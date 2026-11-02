<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        if (! session()->has('dosen_active_class_id')) {
            return redirect()->route('dosen.pilih-kelas');
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

    public function laporanAktivitas()
    {
        return view('dosen.aktifitas-laporan');
    }
}
