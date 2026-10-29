<?php

namespace App\Http\Controllers;

use App\Models\Deliverable;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Milestone;
use App\Models\MilestoneKelompok;
use App\Models\PengumpulanDeliverable;
use App\Models\Proposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    private function sharedViewData(): array
    {
        $mahasiswa = auth('mahasiswa')->user();
        $mahasiswa->loadMissing('kelas', 'kelompok.kelas', 'kelompok.dosen');
        $kelompok = $mahasiswa->kelompok->first();

        return [
            'mahasiswa' => $mahasiswa,
            'kelompok' => $kelompok,
            'userName' => $mahasiswa->nama,
            'userNim' => $mahasiswa->nim,
            'userKelas' => $kelompok?->kelas?->nama_kelas ?? $mahasiswa->kelas?->nama_kelas ?? '-',
            'userProdi' => $kelompok?->kelas?->prodi ?? $mahasiswa->kelas?->prodi ?? '-',
        ];
    }

    public function pilihSemester(): View
    {
        return view('mahasiswa.pilih-semester', $this->sharedViewData());
    }

    public function dashboard(): View
    {
        return view('mahasiswa.dashboard', $this->sharedViewData());
    }

    public function kelompok(): View
    {
        return view('mahasiswa.kelompok', $this->sharedViewData());
    }

    public function proposal(): View
    {
        $viewData = $this->sharedViewData();
        $proposal = $viewData['kelompok']?->proposal()->latest('tanggal_pengajuan')->first();

        return view('mahasiswa.proposal', array_merge($viewData, [
            'proposal' => $proposal,
            'proposalTimeline' => $proposal ? [
                ['event' => 'Draf disusun', 'date' => $proposal->created_at, 'state' => 'done'],
                ['event' => 'Dikirim untuk ditinjau', 'date' => $proposal->tanggal_pengajuan, 'state' => 'done'],
                ['event' => $proposal->status === 'disetujui' ? 'Disetujui pembimbing' : 'Menunggu peninjauan', 'date' => $proposal->updated_at, 'state' => in_array($proposal->status, ['disetujui', 'ditolak']) ? 'done' : 'current'],
            ] : [],
        ]));
    }

    public function logbook(): View
    {
        $viewData = $this->sharedViewData();
        $kelompok = $viewData['kelompok'];

        $logbookRecords = $kelompok
            ? $kelompok->logbookMilestone()->with('mahasiswa')->orderBy('tanggal')->get()
            : collect();

        $earliest = $logbookRecords->min(fn (LogbookMilestone $item) => $item->tanggal?->timestamp);

        $logbooks = $logbookRecords->map(function (LogbookMilestone $lb) use ($earliest) {
            $timestamp = $lb->tanggal?->timestamp ?? time();
            $diffDays = $earliest !== null ? ($timestamp - $earliest) / 86400 : 0;

            return [
                'id' => $lb->id,
                'tanggal' => $lb->tanggal?->toDateString(),
                'judul' => $lb->judul ?? $lb->kegiatan,
                'deskripsi' => $lb->deskripsi ?? $lb->kegiatan,
                'progress' => $lb->progress ?? 0,
                'hasil' => $lb->hasil,
                'rencana' => $lb->rencana,
                'kendala' => $lb->kendala,
                'status' => $lb->status === 'disetujui' ? 'Disetujui' : ($lb->status === 'revisi' ? 'Revisi' : 'Menunggu Review'),
                'feedback' => $lb->feedback,
                'minggu' => (int) floor($diffDays / 7) + 1,
            ];
        })->toArray();

        return view('mahasiswa.logbook', array_merge($viewData, [
            'logbooks' => $logbooks,
            'earliestTimestamp' => $earliest ?? time(),
        ]));
    }

    public function milestone(): View
    {
        $viewData = $this->sharedViewData();
        $kelompok = $viewData['kelompok'];
        $periodeId = $kelompok?->periode_id;

        $milestones = Milestone::query()
            ->when($periodeId, fn ($q) => $q->where('periode_id', $periodeId))
            ->with(['deliverable', 'milestoneKelompok' => fn ($q) => $q->where('kelompok_id', $kelompok?->id)])
            ->orderBy('urutan')
            ->get()
            ->map(function (Milestone $m) use ($kelompok) {
                $mk = $m->milestoneKelompok->first();
                $deliverableIds = $m->deliverable->pluck('id');
                $pengumpulan = PengumpulanDeliverable::query()
                    ->where('kelompok_id', $kelompok?->id)
                    ->whereIn('deliverable_id', $deliverableIds)
                    ->get()
                    ->keyBy('deliverable_id');

                $approvedCount = $pengumpulan->where('status', 'disetujui')->count();
                $totalDeliverable = $m->deliverable->count();
                $calculatedProgress = $totalDeliverable > 0 ? round(($approvedCount / $totalDeliverable) * 100) : 0;

                $statusLabel = match ($mk?->status) {
                    'selesai', 'tercapai' => 'Selesai',
                    'berjalan' => 'Berjalan',
                    'terlambat' => 'Terlambat',
                    default => 'Belum dimulai',
                };

                return [
                    'id' => $m->id,
                    'kode' => $m->kode,
                    'judul' => $m->judul,
                    'status' => $statusLabel,
                    'badge' => $statusLabel === 'Selesai' ? 'sim-badge--approved' : ($statusLabel === 'Berjalan' ? 'sim-badge--pending' : 'sim-badge--draft'),
                    'tenggat' => $m->deadline?->format('d M Y') ?? '-',
                    'deskripsi' => $m->deskripsi,
                    'progress' => $calculatedProgress,
                    'catatan' => $mk?->catatan ?? '',
                    'deliverables' => $m->deliverable->map(function (Deliverable $d) use ($pengumpulan) {
                        $p = $pengumpulan->get($d->id);
                        $status = match ($p?->status) {
                            'disetujui' => 'Disetujui',
                            'revisi' => 'Perlu Revisi',
                            'diajukan' => 'Diajukan',
                            default => 'Belum Dimulai',
                        };

                        return [
                            'id' => $d->id,
                            'nama' => $d->nama,
                            'status' => $status,
                            'file_name' => $p?->file_name ?? '',
                            'file_url' => $p?->file_path ? Storage::url($p->file_path) : '',
                            'catatan' => $p?->catatan ?? '',
                        ];
                    })->toArray(),
                ];
            })->toArray();

        return view('mahasiswa.milestone', array_merge($viewData, [
            'milestones' => $milestones,
        ]));
    }

    public function milestoneDetail(int|string $id): View
    {
        $viewData = $this->sharedViewData();
        $kelompok = $viewData['kelompok'];
        $milestone = Milestone::with(['deliverable', 'milestoneKelompok' => fn ($q) => $q->where('kelompok_id', $kelompok?->id)])->findOrFail($id);
        $mk = $milestone->milestoneKelompok->first();

        $detail = [
            'id' => $milestone->id,
            'kode' => $milestone->kode,
            'judul' => $milestone->judul,
            'status' => ucfirst($mk?->status ?? 'belum dimulai'),
            'badge' => ($mk?->status === 'selesai' || $mk?->status === 'tercapai') ? 'sim-badge--approved' : 'sim-badge--pending',
            'tenggat' => $milestone->deadline?->format('d M Y') ?? '-',
            'deskripsi' => $milestone->deskripsi,
            'catatan' => $mk?->catatan ?? '',
            'deliverables' => $milestone->deliverable,
        ];

        return view('mahasiswa.milestone-detail', array_merge($viewData, [
            'milestone' => $detail,
        ]));
    }

    public function storeMilestoneUpload(Request $request, int|string $id): RedirectResponse
    {
        $request->validate([
            'berkas' => ['required', 'file', 'mimes:pdf,zip', 'max:20480'],
            'item_id' => ['nullable', 'string'],
            'deliverable_item' => ['nullable', 'string'],
        ]);

        $kelompok = $this->kelompokMahasiswa();
        $deliverableId = $request->input('item_id') ?: $request->input('deliverable_item');

        $deliverable = is_numeric($deliverableId)
            ? Deliverable::where('id', $deliverableId)->where('milestone_id', $id)->first()
            : Deliverable::where('milestone_id', $id)->orderBy('urutan')->first();

        if (! $deliverable) {
            return redirect()->back()->withErrors(['berkas' => 'Item deliverable tidak valid.']);
        }

        $file = $request->file('berkas');
        $storedPath = $file->store('deliverables', 'public');

        PengumpulanDeliverable::updateOrCreate(
            ['deliverable_id' => $deliverable->id, 'kelompok_id' => $kelompok->id],
            [
                'diunggah_oleh' => auth('mahasiswa')->id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $storedPath,
                'status' => 'diajukan',
            ]
        );

        MilestoneKelompok::updateOrCreate(
            ['milestone_id' => $id, 'kelompok_id' => $kelompok->id],
            ['status' => 'berjalan']
        );

        return redirect()->route('mahasiswa.milestone')->with('success', 'Berkas deliverable berhasil dikirim!');
    }

    public function storeProposal(Request $request): RedirectResponse|array
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'latar_belakang' => ['required', 'string'],
            'tujuan' => ['required', 'string'],
            'metode' => ['required', 'string'],
        ]);
        $kelompok = $this->kelompokMahasiswa();

        $proposal = Proposal::create([
            ...$validated,
            'kelompok_id' => $kelompok->id,
            'status' => 'diajukan',
            'tanggal_pengajuan' => today(),
        ]);

        if ($request->expectsJson()) {
            return ['success' => true, 'message' => 'Proposal berhasil diajukan.', 'proposal_id' => $proposal->id];
        }

        return redirect()->route('mahasiswa.proposal')->with('success', 'Proposal berhasil diajukan.');
    }

    public function storeLogbook(Request $request): RedirectResponse|array
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'hasil' => ['nullable', 'string'],
            'kendala' => ['nullable', 'string'],
            'rencana' => ['nullable', 'string'],
        ]);
        $mahasiswa = auth('mahasiswa')->user();
        $kelompok = $this->kelompokMahasiswa();
        $milestone = Milestone::where('periode_id', $kelompok->periode_id)->orderBy('urutan')->first();

        if (! $milestone) {
            throw ValidationException::withMessages(['tanggal' => 'Milestone untuk periode aktif belum tersedia.']);
        }

        $logbook = LogbookMilestone::create([
            ...$validated,
            'kegiatan' => $validated['deskripsi'] ?? $validated['judul'],
            'milestone_id' => $milestone->id,
            'kelompok_id' => $kelompok->id,
            'mahasiswa_id' => $mahasiswa->id,
            'status' => 'diajukan',
        ]);

        if ($request->expectsJson()) {
            return ['success' => true, 'message' => 'Catatan logbook berhasil ditambahkan.', 'logbook_id' => $logbook->id];
        }

        return redirect()->route('mahasiswa.logbook')->with('success', 'Catatan logbook berhasil ditambahkan.');
    }

    private function kelompokMahasiswa(): Kelompok
    {
        $kelompok = auth('mahasiswa')->user()->kelompok()->first();

        if (! $kelompok) {
            throw ValidationException::withMessages(['kelompok' => 'Anda belum terdaftar dalam kelompok.']);
        }

        return $kelompok;
    }
}
