<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use Illuminate\Http\Request;

class KelompokController extends Controller
{
    /**
     * Menampilkan daftar kelompok bimbingan dosen pengampu
     */
    public function index(Request $request)
    {
        // 1. Ambil ID dosen yang sedang login
        $dosenId = auth()->user()->id;

        // 2. Query dasar dengan Eager Loading agar tidak terjadi masalah N+1
        $query = Kelompok::with(['anggotaKelompok.mahasiswa', 'logbookMilestone'])
            ->where('dosen_id', $dosenId);

        // 3. Fitur Live Search (Berdasarkan nama kelompok atau judul proyek)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', "%{$search}%")
                  ->orWhere('judul_proyek', 'like', "%{$search}%");
            });
        }

        // 4. Fitur Filter Kelas (misal: SIB 3B)
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // 5. Fitur Filter Status (Berjalan, Review, Terlambat)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 6. Paginasi data kelompok (6 kartu per halaman)
        $kelompokList = $query->paginate(6);

        // Hardcode nilai progress sementara sesuai UI 
        // nanti diganti kalau rumusnya sudah disepakati
        foreach ($kelompokList as $item) {
            $item->progress_dummy = match ($item->status ?? 'Berjalan') {
                'Review' => 64,
                'Terlambat' => 31,
                default => 78,
            };
        }

        return view('dosen.kelompok.index', compact('kelompokList'));
    }

    /**
     * Menampilkan detail satu kelompok bimbingan
     */
    public function show($id)
    {
        $dosenId = auth()->user()->id;

        // Ambil detail kelompok bimbingan dosen beserta relasinya
        $kelompok = Kelompok::with(['anggotaKelompok.mahasiswa', 'logbookMilestone', 'proposal'])
            ->where('dosen_id', $dosenId)
            ->findOrFail($id);

        return view('dosen.kelompok.show', compact('kelompok'));
    }
}