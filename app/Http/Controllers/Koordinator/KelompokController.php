<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelompok;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Periode;
use App\Models\RiwayatPenugasanDosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KelompokController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelompok::with([
            'dosen',
            'kelas',
            'anggotaKelompok.mahasiswa',
            'periode',
            'milestoneKelompok',
        ]);

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', "%{$search}%")
                  ->orWhere('judul_proyek', 'like', "%{$search}%")
                  ->orWhereHas('anggotaKelompok.mahasiswa', function ($m) use ($search) {
                      $m->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                  })
                  ->orWhereHas('dosen', function ($d) use ($search) {
                      $d->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $kelompokList = $query->orderByDesc('tanggal_dibuat')
            ->orderBy('nama_kelompok')
            ->paginate(10)
            ->withQueryString();

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $dosenList = Dosen::where('is_aktif', true)->orderBy('nama')->get();
        $periodeList = Periode::orderByDesc('is_aktif')
            ->orderByDesc('tanggal_mulai')
            ->get();

        return view('koordinator.kelompok', compact(
            'kelompokList',
            'kelasList',
            'dosenList',
            'periodeList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:120',
            'judul_proyek' => 'nullable|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'periode_id' => 'required|exists:periode,id',
            'dosen_id' => 'nullable|exists:dosen,id',
            'mahasiswa_ids' => 'nullable|array',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
        ]);

        $dosenId = $request->filled('dosen_id')
            ? $request->dosen_id
            : Dosen::where('is_aktif', true)->value('id');

        DB::transaction(function () use ($request, $dosenId) {
            $kelompok = Kelompok::create([
                'nama_kelompok' => $request->nama_kelompok,
                'judul_proyek' => $request->judul_proyek ?: $request->nama_kelompok,
                'dosen_id' => $dosenId,
                'kelas_id' => $request->kelas_id,
                'periode_id' => $request->periode_id,
                'jadwal_bimbingan' => $request->jadwal_bimbingan,
                'status' => 'Aktif',
                'tanggal_dibuat' => now()->toDateString(),
            ]);

            // Catat penugasan dosen awal jika dosen tersedia.
            if ($dosenId !== null) {
                RiwayatPenugasanDosen::create([
                    'kelompok_id' => $kelompok->id,
                    'dosen_id' => $dosenId,
                    'dosen_sebelumnya_id' => null,
                    'periode_id' => $request->periode_id,
                    'koordinator_id' => Auth::guard('koordinator')->id(),
                    'aksi' => 'penugasan_baru',
                    'catatan' => 'Penugasan dosen awal saat kelompok dibuat.',
                ]);
            }

            $mahasiswaIds = collect($request->input('mahasiswa_ids', []))
                ->filter()
                ->unique()
                ->values()
                ->all();

            foreach ($mahasiswaIds as $index => $mahasiswaId) {
                $kelompok->anggotaKelompok()->create([
                    'mahasiswa_id' => $mahasiswaId,
                    'role_label' => $index === 0 ? 'Ketua' : 'Anggota',
                    'is_ketua' => $index === 0,
                ]);
            }
        });

        return redirect()
            ->route('koordinator.kelompok.index')
            ->with('success', 'Kelompok berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:120',
            'judul_proyek' => 'nullable|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'periode_id' => 'required|exists:periode,id',
            'dosen_id' => 'nullable|exists:dosen,id',
            'mahasiswa_ids' => 'nullable|array',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
        ]);

        DB::transaction(function () use ($request, $id) {
            // Mengunci baris kelompok selama proses perubahan.
            $kelompok = Kelompok::lockForUpdate()->findOrFail($id);

            $dosenSebelumnyaId = $kelompok->dosen_id !== null
                ? (int) $kelompok->dosen_id
                : null;

            // Jika dosen tidak dipilih, pertahankan dosen sebelumnya.
            $dosenBaruId = $request->filled('dosen_id')
                ? (int) $request->dosen_id
                : $dosenSebelumnyaId;

            $kelompok->update([
                'nama_kelompok' => $request->nama_kelompok,
                'judul_proyek' => $request->judul_proyek ?: $request->nama_kelompok,
                'dosen_id' => $dosenBaruId,
                'kelas_id' => $request->kelas_id,
                'periode_id' => $request->periode_id,
                'jadwal_bimbingan' => $request->jadwal_bimbingan,
            ]);

            // Catat hanya jika dosen pembimbing benar-benar berubah.
            if ($dosenSebelumnyaId !== $dosenBaruId) {
                RiwayatPenugasanDosen::create([
                    'kelompok_id' => $kelompok->id,
                    'dosen_id' => $dosenBaruId,
                    'dosen_sebelumnya_id' => $dosenSebelumnyaId,
                    'periode_id' => $request->periode_id,
                    'koordinator_id' => Auth::guard('koordinator')->id(),
                    'aksi' => $dosenSebelumnyaId === null
                        ? 'penugasan_baru'
                        : 'perubahan_dosen',
                    'catatan' => $dosenSebelumnyaId === null
                        ? 'Penugasan dosen pembimbing pada kelompok.'
                        : 'Perubahan dosen pembimbing kelompok.',
                ]);
            }

            $selectedMahasiswaIds = collect(
                $request->input('mahasiswa_ids', [])
            )
                ->filter()
                ->unique()
                ->values()
                ->all();

            $existingMahasiswaIds = $kelompok->anggotaKelompok()
                ->pluck('mahasiswa_id')
                ->all();

            $newMahasiswaIds = array_diff(
                $selectedMahasiswaIds,
                $existingMahasiswaIds
            );

            foreach ($newMahasiswaIds as $index => $mahasiswaId) {
                $kelompok->anggotaKelompok()->create([
                    'mahasiswa_id' => $mahasiswaId,
                    'role_label' => $index === 0 ? 'Ketua' : 'Anggota',
                    'is_ketua' => $index === 0,
                ]);
            }
        });

        return redirect()
            ->route('koordinator.kelompok.index')
            ->with('success', 'Kelompok berhasil diperbarui.');
    }

    public function show($id)
    {
        $kelompok = Kelompok::with([
            'dosen',
            'kelas',
            'periode',
            'anggotaKelompok.mahasiswa',
            'milestoneKelompok',
        ])->findOrFail($id);

        $mahasiswaList = Mahasiswa::query()
    ->where('kelas_id', $kelompok->kelas_id)
    ->where('is_aktif', true)
    ->whereNotIn(
        'id',
        \App\Models\AnggotaKelompok::query()
            ->whereHas('kelompok', function ($query) use ($kelompok) {
                $query->where('periode_id', $kelompok->periode_id);
            })
            ->select('mahasiswa_id')
    )
    ->orderBy('nama')
    ->get();

        return view('koordinator.kelompok-detail', compact(
            'kelompok',
            'mahasiswaList'
        ));
    }

    public function storeAnggota(Request $request, $id)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'role_label' => 'nullable|string|max:50',
        ]);

        $kelompok = Kelompok::with('anggotaKelompok')->findOrFail($id);

       $mahasiswaSudahTerdaftar = \App\Models\AnggotaKelompok::query()
    ->where('mahasiswa_id', $request->mahasiswa_id)
    ->whereHas('kelompok', function ($query) use ($kelompok) {
        $query->where('periode_id', $kelompok->periode_id);
    })
    ->exists();

if ($mahasiswaSudahTerdaftar) {
    return back()->withErrors([
        'mahasiswa_id' => 'Mahasiswa sudah tergabung dalam kelompok lain pada periode ini.',
    ]);
}

        if ($kelompok->anggotaKelompok()->count() >= 4) {
            return back()->withErrors([
                'mahasiswa_id' => 'Kapasitas kelompok sudah penuh.',
            ]);
        }

        $roleLabel = $request->role_label ?: 'Anggota';

        $kelompok->anggotaKelompok()->create([
            'mahasiswa_id' => $request->mahasiswa_id,
            'role_label' => $roleLabel,
            'is_ketua' => strtolower($roleLabel) === 'ketua',
        ]);

        return redirect()
            ->route('koordinator.kelompok.show', $kelompok->id)
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Aktif,Draft,Ditangguhkan',
        ]);

        $kelompok = Kelompok::findOrFail($id);

        $kelompok->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->route('koordinator.kelompok.index')
            ->with('success', 'Status kelompok berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelompok = Kelompok::with([
            'anggotaKelompok',
            'milestoneKelompok',
            'proposal',
        ])->findOrFail($id);

        $kelompok->anggotaKelompok()->delete();
        $kelompok->milestoneKelompok()->delete();
        $kelompok->proposal()->delete();

        $namaKelompok = $kelompok->nama_kelompok;
        $kelompok->delete();

        return redirect()
            ->route('koordinator.kelompok.index')
            ->with('success', "Kelompok {$namaKelompok} berhasil dihapus.");
    }
}
