<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodeController extends Controller
{
    public function index()
    {
        // Ambil periode yang sedang aktif (jika tidak ada, ambil yang terbaru)
        $periodeAktif = Periode::where('is_aktif', true)->first() ?? Periode::latest()->first();

        // Ambil seluruh daftar riwayat semester beserta hitungan relasinya
        $riwayatPeriode = Periode::withCount(['kelas', 'kelompok', 'milestone'])
            ->orderBy('is_aktif', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('koordinator.periode', compact('periodeAktif', 'riwayatPeriode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_periode'   => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:20',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_aktif'        => 'nullable|boolean',
        ]);

        $isAktif = $request->has('is_aktif');

        DB::transaction(function () use ($validated, $isAktif) {
            if ($isAktif) {
                // Nonaktifkan semua periode lain jika periode baru dijadikan aktif
                Periode::query()->update(['is_aktif' => false]);
            }

            Periode::create([
                'nama_periode'   => $validated['nama_periode'],
                'tahun_akademik' => $validated['tahun_akademik'],
                'tanggal_mulai'   => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_aktif'        => $isAktif,
            ]);
        });

        return redirect()->route('koordinator.periode.index')->with('success', 'Semester baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $periode = Periode::findOrFail($id);

        $validated = $request->validate([
            'nama_periode'   => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:20',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_aktif'        => 'nullable|boolean',
        ]);

        $isAktif = $request->has('is_aktif');

        DB::transaction(function () use ($periode, $validated, $isAktif) {
            if ($isAktif && !$periode->is_aktif) {
                Periode::where('id', '!=', $periode->id)->update(['is_aktif' => false]);
            }

            $periode->update([
                'nama_periode'   => $validated['nama_periode'],
                'tahun_akademik' => $validated['tahun_akademik'],
                'tanggal_mulai'   => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_aktif'        => $isAktif,
            ]);
        });

        return redirect()->route('koordinator.periode.index')->with('success', 'Pengaturan semester berhasil diperbarui!');
    }

    public function toggleAktif($id)
    {
        DB::transaction(function () use ($id) {
            Periode::query()->update(['is_aktif' => false]);
            Periode::where('id', $id)->update(['is_aktif' => true]);
        });

        return redirect()->route('koordinator.periode.index')->with('success', 'Semester aktif berhasil diubah!');
    }
}