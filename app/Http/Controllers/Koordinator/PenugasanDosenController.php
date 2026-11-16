<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Periode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenugasanDosenController extends Controller
{
    /**
     * Kapasitas maksimum penugasan mata kuliah per dosen per semester.
     */
    public const MAX_KAPASITAS_DOSEN = 3;

    /**
     * Tampilkan halaman utama Penugasan Dosen Penilai PBL.
     */
    public function index(Request $request)
    {
        // 1. Ambil semester/periode aktif
        $activePeriode = Periode::where('is_aktif', true)->first()
            ?? Periode::latest()->first();

        if (!$activePeriode) {
            abort(500, 'Tidak ada periode/semester aktif di sistem.');
        }

        // 2. Daftar kelas pada semester aktif
        $kelasList = Kelas::where('periode_id', $activePeriode->id)
            ->where('is_aktif', true)
            ->orderBy('nama_kelas')
            ->get();

        if ($kelasList->isEmpty()) {
            $kelasList = Kelas::where('is_aktif', true)->orderBy('nama_kelas')->get();
        }

        // 3. 3 Mata Kuliah Wajib Penilai PBL
        $mataKuliahList = MataKuliah::where('is_aktif', true)->orderBy('id')->get();
        if ($mataKuliahList->isEmpty()) {
            $mataKuliahList = MataKuliah::orderBy('id')->get();
        }

        // 4. Filter pilihan kelas (default: kelas pertama, misalnya SIB 3A)
        $selectedKelasId = $request->query('kelas_id');
        $selectedKelas = $selectedKelasId
            ? $kelasList->firstWhere('id', (int) $selectedKelasId)
            : $kelasList->first();

        if (!$selectedKelas && $kelasList->isNotEmpty()) {
            $selectedKelas = $kelasList->first();
        }

        // 5. Ambil semua data DosenPengampu pada kelas-kelas semester ini
        $allPengampu = DosenPengampu::with(['dosen', 'mataKuliah', 'kelas'])
            ->whereIn('kelas_id', $kelasList->pluck('id'))
            ->get();

        // 6. Hitung Status Kelengkapan Per Kelas (Chips)
        $kelasChips = [];
        $kelasTerliputCount = 0;
        $totalWajibPerKelas = $mataKuliahList->count() ?: 3;

        foreach ($kelasList as $kelas) {
            $countAssigned = $allPengampu->where('kelas_id', $kelas->id)->count();
            $isLengkap = ($countAssigned >= $totalWajibPerKelas);
            if ($isLengkap) {
                $kelasTerliputCount++;
            }

            $kelasChips[] = [
                'id'         => $kelas->id,
                'nama'       => $kelas->nama_kelas,
                'assigned'   => $countAssigned,
                'total'      => $totalWajibPerKelas,
                'is_lengkap' => $isLengkap,
                'is_active'  => $selectedKelas && ($selectedKelas->id === $kelas->id),
            ];
        }

        // 7. Tabel Baris Mata Kuliah Penilai untuk Kelas Terpilih
        $tableRows = [];
        $selectedKelasPengampu = $selectedKelas
            ? $allPengampu->where('kelas_id', $selectedKelas->id)
            : collect();

        foreach ($mataKuliahList as $mk) {
            $pengampu = $selectedKelasPengampu->firstWhere('mata_kuliah_id', $mk->id);
            $kelasTerliputGlobal = $allPengampu->where('mata_kuliah_id', $mk->id)->unique('kelas_id')->count();

            $tableRows[] = [
                'mata_kuliah_id'   => $mk->id,
                'nama_matkul'      => $mk->nama_matkul,
                'sks'              => $mk->sks ?? 3,
                'semester_label'   => 'Semester 5',
                'kelas_terliput'   => $kelasTerliputGlobal,
                'pengampu_id'      => $pengampu?->id,
                'dosen'            => $pengampu?->dosen,
                'is_assigned'      => $pengampu !== null && $pengampu->dosen !== null,
                'kelas_nama'       => $selectedKelas?->nama_kelas ?? '-',
                'kelas_id'         => $selectedKelas?->id,
            ];
        }

        // 8. Hitung Metrik / Kartu Ringkasan
        $dosenTersediaCount = Dosen::where('is_aktif', true)->count();
        $mkTerliputDiKelas = $tableRows ? collect($tableRows)->where('is_assigned', true)->count() : 0;
        $sisaMataKuliah = max(0, $totalWajibPerKelas - $mkTerliputDiKelas);

        $metrik = [
            'dosen_tersedia'        => $dosenTersediaCount,
            'kelas_terliput_ratio'  => "{$kelasTerliputCount} / " . max(1, $kelasList->count()),
            'mk_terliput_ratio'     => "{$mkTerliputDiKelas} / {$totalWajibPerKelas}",
            'sisa_mk'               => $sisaMataKuliah,
        ];

        // 9. Direktori Dosen Penilai (Kolom Kanan)
        $dosenListRaw = Dosen::where('is_aktif', true)->orderBy('nidn')->get();
        $direktoriDosen = [];

        foreach ($dosenListRaw as $dosen) {
            $dosenAssignments = $allPengampu->where('dosen_id', $dosen->id);
            $assignedCount = $dosenAssignments->count();
            $slotTersedia = max(0, self::MAX_KAPASITAS_DOSEN - $assignedCount);
            $isPenuh = ($assignedCount >= self::MAX_KAPASITAS_DOSEN);

            // Subtitle text misal: "DSN-001 · Pemrograman Web Lanjut (SIB 3A)"
            $assignmentTexts = [];
            foreach ($dosenAssignments as $asgn) {
                $assignmentTexts[] = ($asgn->mataKuliah?->nama_matkul ?? '') . ' (' . ($asgn->kelas?->nama_kelas ?? '') . ')';
            }
            $subtitleDetail = !empty($assignmentTexts)
                ? ($dosen->nidn . ' · ' . implode(', ', $assignmentTexts))
                : ($dosen->nidn . ' · Belum ada penugasan');

            // Inisial avatar (misal: "Dr. Rian Hidayat" -> "RH")
            $initials = $this->extractInitials($dosen->nama);

            $direktoriDosen[] = [
                'id'              => $dosen->id,
                'nama'            => $dosen->nama,
                'nidn'            => $dosen->nidn,
                'initials'        => $initials,
                'subtitle'        => $subtitleDetail,
                'assigned_count'  => $assignedCount,
                'max_capacity'    => self::MAX_KAPASITAS_DOSEN,
                'slot_tersedia'   => $slotTersedia,
                'percent'         => round(($assignedCount / self::MAX_KAPASITAS_DOSEN) * 100),
                'is_penuh'        => $isPenuh,
            ];
        }

        return view('koordinator.penugasan-dosen', compact(
            'activePeriode',
            'kelasList',
            'selectedKelas',
            'mataKuliahList',
            'tableRows',
            'metrik',
            'kelasChips',
            'direktoriDosen'
        ));
    }

    /**
     * Simpan penugasan dosen penilai baru / multiple mata kuliah.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dosen_id'        => 'required|exists:dosen,id',
            'kelas_id'        => 'required|exists:kelas,id',
            'mata_kuliah_ids' => 'required|array|min:1',
            'mata_kuliah_ids.*' => 'exists:mata_kuliah,id',
        ], [
            'dosen_id.required'        => 'Pilih dosen penilai terlebih dahulu.',
            'dosen_id.exists'          => 'Dosen penilai yang dipilih tidak valid.',
            'kelas_id.required'        => 'Pilih kelas terlebih dahulu.',
            'kelas_id.exists'          => 'Kelas yang dipilih tidak valid.',
            'mata_kuliah_ids.required' => 'Pilih minimal satu mata kuliah wajib penilai.',
            'mata_kuliah_ids.min'      => 'Pilih minimal satu mata kuliah wajib penilai.',
        ]);

        $dosen = Dosen::findOrFail($validated['dosen_id']);
        $kelas = Kelas::findOrFail($validated['kelas_id']);

        // Cek kapasitas dosen
        // Hitung penugasan dosen saat ini di luar mata kuliah yang akan diupdate pada kelas ini
        $currentAssignments = DosenPengampu::where('dosen_id', $dosen->id)
            ->where(function ($q) use ($kelas, $validated) {
                $q->where('kelas_id', '!=', $kelas->id)
                  ->orWhereNotIn('mata_kuliah_id', $validated['mata_kuliah_ids']);
            })
            ->count();

        $newTotal = $currentAssignments + count($validated['mata_kuliah_ids']);
        if ($newTotal > self::MAX_KAPASITAS_DOSEN) {
            throw ValidationException::withMessages([
                'dosen_id' => "Dosen {$dosen->nama} hanya memiliki sisa " . max(0, self::MAX_KAPASITAS_DOSEN - $currentAssignments) . " slot penugasan. Maksimal " . self::MAX_KAPASITAS_DOSEN . " penugasan per dosen.",
            ]);
        }

        DB::transaction(function () use ($validated, $dosen, $kelas) {
            foreach ($validated['mata_kuliah_ids'] as $mkId) {
                DosenPengampu::updateOrCreate(
                    [
                        'kelas_id'       => $kelas->id,
                        'mata_kuliah_id' => $mkId,
                    ],
                    [
                        'dosen_id'       => $dosen->id,
                    ]
                );
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Penugasan dosen penilai ({$dosen->nama}) berhasil disimpan untuk {$kelas->nama_kelas}.",
            ]);
        }

        return redirect()
            ->route('koordinator.penugasan-dosen.index', ['kelas_id' => $kelas->id])
            ->with('success', "Penugasan dosen penilai ({$dosen->nama}) berhasil disimpan untuk kelas {$kelas->nama_kelas}.");
    }

    /**
     * Perbarui dosen penilai untuk suatu mata kuliah dan kelas yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $pengampu = DosenPengampu::findOrFail($id);

        $validated = $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
        ], [
            'dosen_id.required' => 'Pilih dosen penilai pengganti.',
            'dosen_id.exists'   => 'Dosen yang dipilih tidak valid.',
        ]);

        $dosenBaru = Dosen::findOrFail($validated['dosen_id']);

        // Cek kapasitas dosen baru jika berbeda
        if ($pengampu->dosen_id !== $dosenBaru->id) {
            $assignedCount = DosenPengampu::where('dosen_id', $dosenBaru->id)->count();
            if ($assignedCount >= self::MAX_KAPASITAS_DOSEN) {
                throw ValidationException::withMessages([
                    'dosen_id' => "Dosen {$dosenBaru->nama} sudah mencapai kapasitas penuh (" . self::MAX_KAPASITAS_DOSEN . " penugasan).",
                ]);
            }
        }

        $pengampu->update(['dosen_id' => $dosenBaru->id]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Dosen penilai berhasil diperbarui menjadi {$dosenBaru->nama}.",
            ]);
        }

        return redirect()
            ->route('koordinator.penugasan-dosen.index', ['kelas_id' => $pengampu->kelas_id])
            ->with('success', "Dosen penilai berhasil diperbarui menjadi {$dosenBaru->nama}.");
    }

    /**
     * Hapus / batalkan penugasan dosen penilai pada suatu mata kuliah kelas.
     */
    public function destroy($id)
    {
        $pengampu = DosenPengampu::findOrFail($id);
        $kelasId = $pengampu->kelas_id;
        $matkulNama = $pengampu->mataKuliah?->nama_matkul ?? 'Mata Kuliah';

        $pengampu->delete();

        return redirect()
            ->route('koordinator.penugasan-dosen.index', ['kelas_id' => $kelasId])
            ->with('success', "Penugasan dosen penilai untuk {$matkulNama} berhasil dibatalkan.");
    }

    /**
     * Endpoint API JSON untuk pengambilan data real-time modal / autocomplete.
     */
    public function apiData(Request $request): JsonResponse
    {
        $dosenList = Dosen::where('is_aktif', true)->orderBy('nidn')->get()->map(function ($d) {
            $count = DosenPengampu::where('dosen_id', $d->id)->count();
            return [
                'id'             => $d->id,
                'nama'           => $d->nama,
                'nidn'           => $d->nidn,
                'assigned_count' => $count,
                'slot_tersedia'  => max(0, self::MAX_KAPASITAS_DOSEN - $count),
                'is_penuh'       => $count >= self::MAX_KAPASITAS_DOSEN,
            ];
        });

        $kelasList = Kelas::where('is_aktif', true)->orderBy('nama_kelas')->get(['id', 'nama_kelas', 'prodi']);
        $mataKuliahList = MataKuliah::where('is_aktif', true)->orderBy('id')->get(['id', 'nama_matkul', 'sks']);

        return response()->json([
            'dosen'       => $dosenList,
            'kelas'       => $kelasList,
            'mata_kuliah' => $mataKuliahList,
        ]);
    }

    /**
     * Helper untuk mengekstrak inisial nama dosen (misal: "Dr. Rian Hidayat" -> "RH").
     */
    private function extractInitials(string $name): string
    {
        // Bersihkan gelar umum di awal/akhir
        $clean = preg_replace('/\b(Dr\.|Prof\.|Ir\.|M\.Kom\.|M\.T\.|M\.Sc\.|S\.Kom\.|S\.T\.)\b/i', '', $name);
        $clean = trim(preg_replace('/[,\.]/', ' ', $clean));
        $words = array_values(array_filter(explode(' ', $clean)));

        if (empty($words)) {
            return strtoupper(substr($name, 0, 2));
        }

        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 2));
        }

        return strtoupper(substr($words[0], 0, 1) . substr($words[count($words) - 1], 0, 1));
    }
}
