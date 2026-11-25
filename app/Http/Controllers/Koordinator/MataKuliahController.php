<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\DosenPengampu;
use App\Models\MataKuliah;
use App\Models\PengumpulanMilestone;
use App\Models\PenyelesaianMilestone;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MataKuliahController extends Controller
{
    /**
     * Tampilkan daftar mata kuliah.
     */
    public function index(Request $request)
    {
        $query = MataKuliah::with(['dosenPengampu.kelas']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama_matkul', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === 'aktif');
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $mataKuliahList = $query->orderBy('kode')->paginate(10)->withQueryString();

        $semesterList = MataKuliah::whereNotNull('semester')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester');

        $totalMk = $mataKuliahList->total();
        $totalSks = $mataKuliahList->sum(fn ($mk) => (int) $mk->sks);

        return view('koordinator.master-data.mata kuliah.index', compact(
            'mataKuliahList',
            'semesterList',
            'totalMk',
            'totalSks',
        ));
    }

    /**
     * Simpan mata kuliah baru.
     * Penugasan dosen dikelola melalui menu Penugasan Dosen.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:mata_kuliah,kode',
            'nama_matkul' => 'required|string|max:100',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'is_aktif' => 'nullable|boolean',
        ]);

        $mataKuliah = DB::transaction(fn () => MataKuliah::create([
            'kode' => strtoupper($validated['kode']),
            'nama_matkul' => $validated['nama_matkul'],
            'sks' => $validated['sks'],
            'semester' => $validated['semester'],
            'is_aktif' => $request->boolean('is_aktif', true),
        ]));

        return redirect()->route('koordinator.master-data.mata-kuliah')
            ->with('success', "Mata kuliah {$mataKuliah->kode} berhasil ditambahkan.");
    }

    /**
     * Perbarui data mata kuliah tanpa mengubah penugasan dosen.
     */
    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mata_kuliah', 'kode')->ignore($mataKuliah->id),
            ],
            'nama_matkul' => 'required|string|max:100',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:8',
            'is_aktif' => 'nullable|boolean',
        ]);

        DB::transaction(fn () => $mataKuliah->update([
            'kode' => strtoupper($validated['kode']),
            'nama_matkul' => $validated['nama_matkul'],
            'sks' => $validated['sks'],
            'semester' => $validated['semester'],
            'is_aktif' => $request->boolean('is_aktif', true),
        ]));

        return redirect()->route('koordinator.master-data.mata-kuliah')
            ->with('success', "Data mata kuliah {$mataKuliah->kode} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif / nonaktif mata kuliah.
     */
    public function toggleStatus(MataKuliah $mataKuliah)
    {
        DB::transaction(fn () => $mataKuliah->update([
            'is_aktif' => ! $mataKuliah->is_aktif,
        ]));

        $status = $mataKuliah->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('koordinator.master-data.mata-kuliah')
            ->with('success', "Mata kuliah {$mataKuliah->kode} berhasil {$status}.");
    }

    /**
     * Hapus mata kuliah hanya jika tidak memiliki penugasan dosen.
     */
    public function destroy(MataKuliah $mataKuliah)
    {
        $penugasanCount = DosenPengampu::where(
            'mata_kuliah_id',
            $mataKuliah->id
        )->count();

        if ($penugasanCount > 0) {
            return redirect()->route('koordinator.master-data.mata-kuliah')
                ->with(
                    'error',
                    "Mata kuliah {$mataKuliah->kode} tidak dapat dihapus karena masih memiliki {$penugasanCount} data penugasan dosen."
                );
        }

        $submissionCount = PengumpulanMilestone::where('mata_kuliah', $mataKuliah->slug)->count();

        if ($submissionCount > 0) {
            return redirect()->route('koordinator.master-data.mata-kuliah')
                ->with(
                    'error',
                    "Mata kuliah {$mataKuliah->kode} tidak dapat dihapus karena sudah memiliki {$submissionCount} submission mahasiswa."
                );
        }

        $penyelesaianCount = PenyelesaianMilestone::where('mata_kuliah_id', $mataKuliah->id)->count();

        if ($penyelesaianCount > 0) {
            return redirect()->route('koordinator.master-data.mata-kuliah')
                ->with(
                    'error',
                    "Mata kuliah {$mataKuliah->kode} tidak dapat dihapus karena sudah memiliki {$penyelesaianCount} data penyelesaian milestone."
                );
        }

        $kode = $mataKuliah->kode;

        try {
            $mataKuliah->delete();
        } catch (QueryException $exception) {
            if (! $this->merupakanPelanggaranForeignKey($exception)) {
                throw $exception;
            }

            return redirect()->route('koordinator.master-data.mata-kuliah')
                ->with(
                    'error',
                    "Mata kuliah {$kode} tidak dapat dihapus karena masih memiliki submission atau relasi data lain."
                );
        }

        return redirect()->route('koordinator.master-data.mata-kuliah')
            ->with('success', "Mata kuliah {$kode} berhasil dihapus.");
    }

    private function merupakanPelanggaranForeignKey(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return $driverCode === 1451
            || str_contains($message, 'foreign key constraint fails')
            || str_contains($message, 'foreign key constraint failed');
    }
}
