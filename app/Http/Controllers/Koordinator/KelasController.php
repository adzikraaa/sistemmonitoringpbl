<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\DosenPengampu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    /**
     * Tampilkan daftar kelas dengan filter pencarian, angkatan, dan status.
     */
    public function index(Request $request)
    {
        $periodeAktif = Periode::where('is_aktif', true)->first();

        $query = Kelas::with([
            'mahasiswa',
            'periode',
            'kelompok.dosen',
            'dosenPengampu.dosen',
            'dosenPengampu.mataKuliah',
        ]);

        // Filter pencarian nama kelas, prodi, atau dosen koordinator
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('prodi', 'like', "%{$search}%")
                  ->orWhereHas('dosenPengampu.dosen', function ($q2) use ($search) {
                      $q2->where('nama', 'like', "%{$search}%")
                         ->orWhere('nidn', 'like', "%{$search}%");
                  });
            });
        }

        // Filter angkatan berdasarkan periode
        if ($request->filled('angkatan')) {
            $query->whereHas('periode', function ($q) use ($request) {
                $q->where('tahun_akademik', 'like', $request->angkatan . '%');
            });
        }

        // Filter status aktif/nonaktif
        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === 'aktif');
        }

        $kelasList = $query->orderBy('nama_kelas')->paginate(10)->withQueryString();

        // Data untuk form tambah/edit & filter
        $periodeList    = Periode::orderByDesc('is_aktif')->orderByDesc('tahun_akademik')->get();
        $dosenList      = Dosen::where('is_aktif', true)->orderBy('nama')->get();
        $mataKuliahList = MataKuliah::where('is_aktif', true)->orderBy('nama_matkul')->get();

        // Hitung total mahasiswa dari kelas yang ditampilkan
        $totalMahasiswa = $kelasList->sum(fn($k) => $k->mahasiswa->count());

        // Pilih kelas untuk form edit dan card info di panel bawah
        $selectedId    = $request->get('selected_id');
        $selectedKelas = null;
        if ($selectedId) {
            $selectedKelas = $kelasList->firstWhere('id', (int) $selectedId);
        }
        if (!$selectedKelas) {
            $selectedKelas = $kelasList->firstWhere('nama_kelas', 'SIB 3C') ?? $kelasList->first();
        }

        return view('koordinator.master-data.kelas.index', compact(
            'kelasList',
            'periodeAktif',
            'periodeList',
            'dosenList',
            'mataKuliahList',
            'totalMahasiswa',
            'selectedKelas',
        ));
    }

    /**
     * Simpan kelas baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'prodi'      => 'required|string|max:100',
            'periode_id' => 'required|exists:periode,id',
            'is_aktif'   => 'nullable|boolean',
            'dosen_id'   => 'nullable|exists:dosen,id',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'prodi'      => $request->prodi,
            'periode_id' => $request->periode_id,
            'is_aktif'   => $request->boolean('is_aktif', true),
        ]);

        if ($request->filled('dosen_id')) {
            $matkulFirst = MataKuliah::where('is_aktif', true)->first();
            if ($matkulFirst) {
                DosenPengampu::updateOrCreate(
                    [
                        'kelas_id'       => $kelas->id,
                        'mata_kuliah_id' => $matkulFirst->id,
                    ],
                    [
                        'dosen_id'       => $request->dosen_id,
                    ]
                );
            }
        }

        return redirect()->route('koordinator.master-data.kelas', ['selected_id' => $kelas->id])
            ->with('success', "Kelas {$request->nama_kelas} berhasil ditambahkan.");
    }

    /**
     * Update data kelas.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'prodi'      => 'required|string|max:100',
            'periode_id' => 'required|exists:periode,id',
            'dosen_id'   => 'nullable|exists:dosen,id',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'prodi'      => $request->prodi,
            'periode_id' => $request->periode_id,
        ]);

        if ($request->filled('dosen_id')) {
            $matkulFirst = MataKuliah::where('is_aktif', true)->first();
            if ($matkulFirst) {
                DosenPengampu::updateOrCreate(
                    [
                        'kelas_id'       => $kelas->id,
                        'mata_kuliah_id' => $matkulFirst->id,
                    ],
                    [
                        'dosen_id'       => $request->dosen_id,
                    ]
                );
            }
        }

        return redirect()->route('koordinator.master-data.kelas', ['selected_id' => $kelas->id])
            ->with('success', "Data kelas {$kelas->nama_kelas} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / nonaktif kelas.
     */
    public function toggleStatus(Kelas $kelas)
    {
        $kelas->update(['is_aktif' => !$kelas->is_aktif]);

        $status = $kelas->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('koordinator.master-data.kelas', ['selected_id' => $kelas->id])
            ->with('success', "Kelas {$kelas->nama_kelas} berhasil {$status}.");
    }

    /**
     * Hapus kelas (hanya jika tidak memiliki mahasiswa).
     */
    public function destroy(Kelas $kelas)
    {
        if ($kelas->mahasiswa()->count() > 0) {
            return redirect()->route('koordinator.master-data.kelas')
                ->with('error', "Kelas {$kelas->nama_kelas} tidak dapat dihapus karena masih memiliki {$kelas->mahasiswa()->count()} mahasiswa terdaftar.");
        }

        $nama = $kelas->nama_kelas;
        $kelas->delete();

        return redirect()->route('koordinator.master-data.kelas')
            ->with('success', "Kelas {$nama} berhasil dihapus.");
    }
}
