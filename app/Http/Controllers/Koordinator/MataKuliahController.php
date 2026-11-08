<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use App\Models\Kelas;
use App\Models\DosenPengampu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MataKuliahController extends Controller
{
    /**
     * Tampilkan daftar mata kuliah dengan pencarian, filter status, dan filter semester.
     */
    public function index(Request $request)
    {
        $query = MataKuliah::with(['dosenPengampu.kelas']);

        // Filter pencarian berdasarkan kode atau nama mata kuliah
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama_matkul', 'like', "%{$search}%");
            });
        }

        // Filter status aktif/nonaktif
        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === 'aktif');
        }

        // Filter semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $mataKuliahList = $query->orderBy('kode')->paginate(10)->withQueryString();

        // Data untuk form tambah/edit dan filter semester
        $kelasList      = Kelas::where('is_aktif', true)->orderBy('nama_kelas')->get();
        $semesterList   = MataKuliah::whereNotNull('semester')->distinct()->orderBy('semester')->pluck('semester');

        // Hitung statistik
        $totalMk  = $mataKuliahList->total();
        $totalSks = $mataKuliahList->sum(fn($mk) => (int) $mk->sks);

        return view('koordinator.master-data.mata kuliah.index', compact(
            'mataKuliahList',
            'kelasList',
            'semesterList',
            'totalMk',
            'totalSks',
        ));
    }

    /**
     * Simpan mata kuliah baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode'        => 'required|string|max:20|unique:mata_kuliah,kode',
            'nama_matkul' => 'required|string|max:100',
            'sks'         => 'required|integer|min:1|max:6',
            'semester'    => 'required|integer|min:1|max:8',
            'is_aktif'    => 'nullable|boolean',
            'kelas_id'    => 'nullable|array',
            'kelas_id.*'  => 'exists:kelas,id',
        ]);

        $mataKuliah = MataKuliah::create([
            'kode'        => strtoupper($request->kode),
            'nama_matkul' => $request->nama_matkul,
            'sks'         => $request->sks,
            'semester'    => $request->semester,
            'is_aktif'    => $request->boolean('is_aktif', true),
        ]);

        // Simpan relasi mata kuliah ke kelas yang dipilih
        if ($request->filled('kelas_id')) {
            foreach ($request->kelas_id as $kelasId) {
                DosenPengampu::updateOrCreate(
                    ['mata_kuliah_id' => $mataKuliah->id, 'kelas_id' => $kelasId],
                    ['dosen_id' => null]
                );
            }
        }

        return redirect()->route('koordinator.master-data.mata-kuliah')
            ->with('success', "Mata kuliah {$mataKuliah->kode} berhasil ditambahkan.");
    }

    /**
     * Update data mata kuliah.
     */
    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'kode'        => 'required|string|max:20|unique:mata_kuliah,kode,' . $mataKuliah->id,
            'nama_matkul' => 'required|string|max:100',
            'sks'         => 'required|integer|min:1|max:6',
            'semester'    => 'required|integer|min:1|max:8',
            'is_aktif'    => 'nullable|boolean',
            'kelas_id'    => 'nullable|array',
            'kelas_id.*'  => 'exists:kelas,id',
        ]);

        $mataKuliah->update([
            'kode'        => strtoupper($request->kode),
            'nama_matkul' => $request->nama_matkul,
            'sks'         => $request->sks,
            'semester'    => $request->semester,
            'is_aktif'    => $request->boolean('is_aktif', true),
        ]);

        // Sinkronkan relasi mata kuliah ke kelas
        if ($request->filled('kelas_id')) {
            DosenPengampu::where('mata_kuliah_id', $mataKuliah->id)->delete();
            foreach ($request->kelas_id as $kelasId) {
                DosenPengampu::updateOrCreate(
                    ['mata_kuliah_id' => $mataKuliah->id, 'kelas_id' => $kelasId],
                    ['dosen_id' => null]
                );
            }
        } else {
            DosenPengampu::where('mata_kuliah_id', $mataKuliah->id)->delete();
        }

        return redirect()->route('koordinator.master-data.mata-kuliah')
            ->with('success', "Data mata kuliah {$mataKuliah->kode} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / nonaktif mata kuliah.
     */
    public function toggleStatus(MataKuliah $mataKuliah)
    {
        $mataKuliah->update(['is_aktif' => !$mataKuliah->is_aktif]);

        $status = $mataKuliah->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('koordinator.master-data.mata-kuliah')
            ->with('success', "Mata kuliah {$mataKuliah->kode} berhasil {$status}.");
    }

    /**
     * Hapus mata kuliah (hanya jika tidak memiliki penugasan dosen).
     */
    public function destroy(MataKuliah $mataKuliah)
    {
        $penugasanCount = DosenPengampu::where('mata_kuliah_id', $mataKuliah->id)->count();

        if ($penugasanCount > 0) {
            return redirect()->route('koordinator.master-data.mata-kuliah')
                ->with('error', "Mata kuliah {$mataKuliah->kode} tidak dapat dihapus karena masih memiliki {$penugasanCount} data penugasan dosen.");
        }

        $kode = $mataKuliah->kode;
        $mataKuliah->delete();

        return redirect()->route('koordinator.master-data.mata-kuliah')
            ->with('success', "Mata kuliah {$kode} berhasil dihapus.");
    }
}
