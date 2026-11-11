<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelompok;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Http\Request;

class KelompokController extends Controller
{
    public function index()
    {
        $kelompokList = Kelompok::with(['dosen', 'kelas', 'anggotaKelompok.mahasiswa', 'periode', 'milestoneKelompok'])
            ->orderByDesc('tanggal_dibuat')
            ->get();

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $dosenList = Dosen::where('is_aktif', true)->orderBy('nama')->get();
        $periodeList = Periode::orderByDesc('is_aktif')->orderByDesc('tanggal_mulai')->get();

        return view('koordinator.kelompok', compact('kelompokList', 'kelasList', 'dosenList', 'periodeList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelompok' => 'required|string|max:120',
            'judul_proyek'  => 'nullable|string|max:255',
            'kelas_id'     => 'required|exists:kelas,id',
            'periode_id'   => 'required|exists:periode,id',
            'dosen_id'     => 'nullable|exists:dosen,id',
        ]);

        $dosenId = $request->filled('dosen_id') ? $request->dosen_id : Dosen::where('is_aktif', true)->value('id');

        Kelompok::create([
            'nama_kelompok' => $request->nama_kelompok,
            'judul_proyek'  => $request->judul_proyek ?: $request->nama_kelompok,
            'dosen_id'      => $dosenId,
            'kelas_id'      => $request->kelas_id,
            'periode_id'    => $request->periode_id,
            'jadwal_bimbingan' => $request->jadwal_bimbingan,
            'status'        => 'Aktif',
            'tanggal_dibuat' => now()->toDateString(),
        ]);

        return redirect()->route('koordinator.kelompok.index')->with('success', 'Kelompok berhasil ditambahkan.');
    }

    public function show($id)
    {
        $kelompok = Kelompok::with(['dosen', 'kelas', 'periode', 'anggotaKelompok.mahasiswa', 'milestoneKelompok'])
            ->findOrFail($id);

        $mahasiswaList = Mahasiswa::query()
            ->where('kelas_id', $kelompok->kelas_id)
            ->where('is_aktif', true)
            ->whereNotIn('id', $kelompok->anggotaKelompok()->pluck('mahasiswa_id'))
            ->orderBy('nama')
            ->get();

        return view('koordinator.kelompok-detail', compact('kelompok', 'mahasiswaList'));
    }

    public function storeAnggota(Request $request, $id)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'role_label' => 'nullable|string|max:50',
        ]);

        $kelompok = Kelompok::with('anggotaKelompok')->findOrFail($id);

        if ($kelompok->anggotaKelompok()->where('mahasiswa_id', $request->mahasiswa_id)->exists()) {
            return back()->withErrors(['mahasiswa_id' => 'Mahasiswa sudah ada di kelompok ini.']);
        }

        if ($kelompok->anggotaKelompok()->count() >= 4) {
            return back()->withErrors(['mahasiswa_id' => 'Kapasitas kelompok sudah penuh.']);
        }

        $roleLabel = $request->role_label ?: 'Anggota';

        $kelompok->anggotaKelompok()->create([
            'mahasiswa_id' => $request->mahasiswa_id,
            'role_label' => $roleLabel,
            'is_ketua' => strtolower($roleLabel) === 'ketua',
        ]);

        return redirect()->route('koordinator.kelompok.show', $kelompok->id)->with('success', 'Anggota berhasil ditambahkan.');
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

        return redirect()->route('koordinator.kelompok.index')->with('success', 'Status kelompok berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelompok = Kelompok::with(['anggotaKelompok', 'milestoneKelompok', 'proposal'])->findOrFail($id);

        $kelompok->anggotaKelompok()->delete();
        $kelompok->milestoneKelompok()->delete();
        $kelompok->proposal()->delete();

        $namaKelompok = $kelompok->nama_kelompok;
        $kelompok->delete();

        return redirect()->route('koordinator.kelompok.index')->with('success', "Kelompok {$namaKelompok} berhasil dihapus.");
    }
}
