<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Periode;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('is_aktif', true)->first() ?? Periode::latest('tanggal_mulai')->first();

        $milestones = Milestone::query()
            ->when($periodeAktif, function ($query, $periodeAktif) {
                $query->where('periode_id', $periodeAktif->id);
            })
            ->orderBy('urutan')
            ->get();

        $selectedMilestone = $milestones->first();

        return view('koordinator.milestone-global', [
            'periodeAktif' => $periodeAktif,
            'periodeOptions' => Periode::orderBy('tanggal_mulai', 'desc')->get(),
            'milestones' => $milestones,
            'selectedMilestone' => $selectedMilestone,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'urutan' => ['required', 'integer', 'min:1'],
            'periode_id' => ['nullable', 'exists:periode,id'],
        ]);

        $periodeId = $validated['periode_id'] ?? $this->activePeriodeId();

        if (empty($periodeId)) {
            abort(422, 'Semester aktif belum tersedia. Tambahkan periode terlebih dahulu.');
        }

        $resolvedUrutan = $this->resolveUrutan($periodeId, $validated['urutan']);

        Milestone::create([
            'periode_id' => $periodeId,
            'kode' => $this->buildKode($resolvedUrutan),
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'urutan' => $resolvedUrutan,
            'deadline' => $validated['deadline'] ?? null,
        ]);

        return redirect()->route('koordinator.milestone-global.index')->with('success', 'Milestone berhasil ditambahkan.');
    }

    public function update(Request $request, Milestone $milestone)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'urutan' => ['required', 'integer', 'min:1'],
            'periode_id' => ['nullable', 'exists:periode,id'],
        ]);

        $periodeId = $validated['periode_id'] ?? $this->activePeriodeId() ?? $milestone->periode_id;

        $resolvedUrutan = $this->resolveUrutan($periodeId, $validated['urutan'], $milestone->id);

        $milestone->update([
            'periode_id' => $periodeId,
            'kode' => $this->buildKode($resolvedUrutan),
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'urutan' => $resolvedUrutan,
            'deadline' => $validated['deadline'] ?? null,
        ]);

        return redirect()->route('koordinator.milestone-global.index')->with('success', 'Perubahan milestone berhasil disimpan.');
    }

    protected function resolveUrutan(int $periodeId, int $urutan, ?int $exceptMilestoneId = null): int
    {
        $usedUrutans = Milestone::query()
            ->where('periode_id', $periodeId)
            ->when($exceptMilestoneId, function ($query) use ($exceptMilestoneId) {
                $query->whereKeyNot($exceptMilestoneId);
            })
            ->pluck('urutan')
            ->all();

        if (!in_array($urutan, $usedUrutans, true)) {
            return $urutan;
        }

        $candidate = $urutan;
        while (in_array($candidate, $usedUrutans, true)) {
            $candidate++;
        }

        return $candidate;
    }

    protected function activePeriodeId(): ?int
    {
        return Periode::where('is_aktif', true)->value('id')
            ?? Periode::latest('tanggal_mulai')->value('id');
    }

    protected function buildKode(int $urutan): string
    {
        return 'M' . str_pad((string) $urutan, 2, '0', STR_PAD_LEFT);
    }
}
