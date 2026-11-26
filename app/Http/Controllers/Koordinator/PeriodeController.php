<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PeriodeController extends Controller
{
    public function index(): View
    {
        $periodeAktif = Periode::query()
            ->where('is_aktif', true)
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->first();

        // Ambil seluruh daftar riwayat semester beserta hitungan relasinya
        $riwayatPeriode = Periode::withCount(['kelas', 'kelompok', 'milestone'])
            ->orderBy('is_aktif', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('koordinator.periode', compact('periodeAktif', 'riwayatPeriode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_aktif' => 'nullable',
        ], [
            'tanggal_selesai.after' => 'Tanggal selesai harus lebih akhir daripada tanggal mulai.',
        ]);

        // Validasi anti-tumpang tindih (KOR-05)
        $this->pastikanTanggalTidakTumpangTindih(
            $validated['tanggal_mulai'],
            $validated['tanggal_selesai']
        );

        $isAktif = $request->boolean('is_aktif');

        DB::transaction(function () use ($validated, $isAktif) {
            if ($isAktif) {
                // Nonaktifkan semua periode lain jika periode baru dijadikan aktif
                Periode::query()->update(['is_aktif' => false]);
            }

            Periode::create([
                'nama_periode' => $validated['nama_periode'],
                'tahun_akademik' => $validated['tahun_akademik'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'is_aktif' => $isAktif,
            ]);
        });

        $msg = $isAktif
            ? 'Semester baru berhasil ditambahkan dan langsung dijadikan semester aktif!'
            : 'Semester baru berhasil ditambahkan!';

        return redirect()->route('koordinator.periode.index')->with('success', $msg);
    }

    public function update(Request $request, Periode $periode): RedirectResponse
    {
        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tahun_akademik' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ], [
            'tanggal_selesai.after' => 'Tanggal selesai harus lebih akhir daripada tanggal mulai.',
        ]);

        // Validasi anti-tumpang tindih, kecualikan periode yang sedang diedit
        $this->pastikanTanggalTidakTumpangTindih(
            $validated['tanggal_mulai'],
            $validated['tanggal_selesai'],
            $periode->id
        );

        // is_aktif TIDAK diubah via form update — perubahan status aktif hanya
        // dilakukan melalui tombol "Aktifkan" di tabel riwayat (toggleAktif).
        $periode->update([
            'nama_periode' => $validated['nama_periode'],
            'tahun_akademik' => $validated['tahun_akademik'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
        ]);

        return redirect()->route('koordinator.periode.index')->with('success', 'Pengaturan semester berhasil diperbarui!');
    }

    public function activate(Periode $periode): RedirectResponse
    {
        DB::transaction(function () use ($periode): void {
            Periode::query()
                ->whereKeyNot($periode->id)
                ->where('is_aktif', true)
                ->update(['is_aktif' => false]);

            $periode->forceFill(['is_aktif' => true])->saveOrFail();
        });

        return redirect()->route('koordinator.periode.index')
            ->with('success', 'Semester '.$periode->labelPeriode().' berhasil diaktifkan.');
    }

    public function deactivate(Periode $periode): RedirectResponse
    {
        $periode->forceFill(['is_aktif' => false])->saveOrFail();

        return redirect()->route('koordinator.periode.index')
            ->with('success', 'Semester berhasil dinonaktifkan.');
    }

    /**
     * Memastikan rentang tanggal periode baru/perubahan tidak beririsan
     * dengan periode akademik lain yang sudah terdaftar.
     */
    private function pastikanTanggalTidakTumpangTindih(
        string $tanggalMulai,
        string $tanggalSelesai,
        ?int $ignorePeriodeId = null
    ): void {
        $periodeBentrok = Periode::query()
            ->when($ignorePeriodeId !== null, fn ($query) => $query->whereKeyNot($ignorePeriodeId))
            ->whereDate('tanggal_mulai', '<=', $tanggalSelesai)
            ->whereDate('tanggal_selesai', '>=', $tanggalMulai)
            ->first();

        if ($periodeBentrok !== null) {
            $pesan = sprintf(
                'Rentang tanggal tumpang tindih dengan periode %s (%s – %s).',
                $periodeBentrok->labelPeriode(),
                $periodeBentrok->tanggal_mulai->format('d M Y'),
                $periodeBentrok->tanggal_selesai->format('d M Y')
            );

            throw ValidationException::withMessages([
                'tanggal_mulai' => $pesan,
            ]);
        }
    }
}