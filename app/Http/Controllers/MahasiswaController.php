<?php

namespace App\Http\Controllers;

use App\Models\DosenPengampu;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\PengumpulanMilestone;
use App\Models\Proposal;
use App\Models\RiwayatProposal;
use App\Services\ProgressService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    private const MATA_KULIAH_SIM_PBL = [
        'Pemrograman Web Lanjut',
        'Manajemen Proyek Sistem Informasi',
        'Penjaminan Mutu Perangkat Lunak',
    ];

    private function kelompokAktif(): ?Kelompok
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        if (! $mahasiswa) {
            return null;
        }

        return Kelompok::query()
            ->periodeAktif()
            ->whereHas('mahasiswa', fn ($query) => $query->where('mahasiswa.id', $mahasiswa->id))
            ->orderBy('id')
            ->first();
    }

    /**
     * Data kelompok dan user (hardcoded untuk prototype).
     * Saat backend lengkap, data ini diambil dari database/session.
     */
    private function sharedViewData(): array
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        return [
            'userName' => $mahasiswa?->nama ?? 'Mahasiswa',
            'userNim' => $mahasiswa?->nim ?? '-',
            'userKelas' => $mahasiswa?->kelas_id ?? '-',
            'userProdi' => 'Teknologi Informasi',
        ];
    }

    public function pilihSemester()
    {
        $kelompok = $this->kelompokAktif();
        $progres = $kelompok
            ? app(ProgressService::class)->untukKelompok($kelompok)
            : $this->kosongProgres();

        return view('mahasiswa.pilih-semester', array_merge($this->sharedViewData(), [
            'kelompok' => $kelompok,
            'progres' => $progres,
        ]));
    }

    public function dashboard()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $kelompok = $this->kelompokAktif();
        $progres = $kelompok
            ? app(ProgressService::class)->untukKelompok($kelompok)
            : $this->kosongProgres();
        $proposal = $kelompok
            ? Proposal::where('kelompok_id', $kelompok->id)->latest()->first()
            : null;
        $logbookStats = $kelompok ? $this->logbookStats($kelompok) : $this->kosongLogbookStats();
        $anggota = $this->anggotaView($kelompok);

        $aktivitasTerbaru = ($mahasiswa && $kelompok)
            ? LogbookMilestone::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('kelompok_id', $kelompok->id)
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->limit(5)
                ->get()
            : collect();

        return view('mahasiswa.dashboard', array_merge($this->sharedViewData(), [
            'kelompok' => $kelompok,
            'progres' => $progres,
            'proposal' => $proposal,
            'logbookStats' => $logbookStats,
            'anggota' => $anggota,
            'tahapan' => $this->tahapanDashboard($proposal),
            'aktivitasTerbaru' => $aktivitasTerbaru,
        ]));
    }

    public function kelompok()
    {
        $kelompok = $this->kelompokAktif();
        $progres = $kelompok
            ? app(ProgressService::class)->untukKelompok($kelompok)
            : $this->kosongProgres();
        $logbookStats = $kelompok ? $this->logbookStats($kelompok) : $this->kosongLogbookStats();
        $nextMilestone = $kelompok
            ? Milestone::where('periode_id', $kelompok->periode_id)->orderBy('urutan')->first()
            : null;

        return view('mahasiswa.kelompok', array_merge($this->sharedViewData(), [
            'kelompok' => $kelompok,
            'progres' => $progres,
            'anggota' => $this->anggotaView($kelompok),
            'logbookStats' => $logbookStats,
            'nextMilestone' => $nextMilestone,
        ]));
    }

    private function kosongProgres(): array
    {
        return ['persen' => 0.0, 'selesai' => 0, 'total' => 0, 'punya_data' => false];
    }

    private function kosongLogbookStats(): array
    {
        return [
            'total' => 0,
            'menunggu' => 0,
            'disetujui' => 0,
            'revisi' => 0,
            'terbaru' => null,
        ];
    }

    private function anggotaView(?Kelompok $kelompok): array
    {
        if (! $kelompok) {
            return [];
        }

        return $kelompok->anggotaTerurut()->map(function ($anggota) {
            $nama = $anggota->mahasiswa?->nama ?? 'Mahasiswa';
            $parts = preg_split('/\s+/', trim($nama)) ?: [];
            $awal = $parts[0] ?? 'M';
            $inisial = strtoupper(
                substr($awal, 0, 1).(isset($parts[1]) ? substr($parts[1], 0, 1) : substr($awal, 1, 1))
            );

            return [
                'nama' => $nama,
                'nim' => $anggota->mahasiswa?->nim ?? '-',
                'peran' => $anggota->is_ketua ? 'Ketua' : 'Anggota',
                'role' => $anggota->is_ketua ? 'Ketua' : 'Anggota',
                'inisial' => $inisial,
                'avatar' => $inisial,
            ];
        })->values()->all();
    }

    private function statusProgres(array $progres): string
    {
        $total = (int) ($progres['total'] ?? 0);
        $selesai = (int) ($progres['selesai'] ?? 0);

        if ($total === 0) {
            return 'Belum ada data';
        }

        if ($selesai >= $total) {
            return 'Selesai';
        }

        return $selesai > 0 ? 'Berjalan' : 'Belum dimulai';
    }

    private function logbookStats(Kelompok $kelompok): array
    {
        $records = LogbookMilestone::where('kelompok_id', $kelompok->id)->get();

        $status = fn ($record) => strtolower(trim((string) $record->status));

        return [
            'total' => $records->count(),
            'menunggu' => $records->filter(
                fn ($record) => in_array($status($record), ['diajukan', 'menunggu review', 'menunggu'], true)
            )->count(),
            'disetujui' => $records->filter(fn ($record) => $status($record) === 'disetujui')->count(),
            'revisi' => $records->filter(fn ($record) => $status($record) === 'revisi')->count(),
            'terbaru' => $records->sortByDesc('tanggal')->first(),
        ];
    }

    private function tahapanDashboard(?Proposal $proposal): array
    {
        $proposalState = 'todo';
        $proposalStatus = 'Belum ada proposal';

        if ($proposal) {
            $raw = strtolower((string) $proposal->status);

            [$proposalState, $proposalStatus] = match ($raw) {
                'disetujui' => ['done', 'Disetujui'],
                'revisi' => ['active', 'Revisi'],
                'ditolak' => ['todo', 'Ditolak'],
                default => ['active', 'Dalam Proses'],
            };
        }

        return [
            ['label' => 'Proposal', 'state' => $proposalState, 'status' => $proposalStatus],
            ['label' => 'Analisis', 'state' => 'todo', 'status' => 'Belum tersedia'],
            ['label' => 'Perancangan', 'state' => 'todo', 'status' => 'Belum tersedia'],
            ['label' => 'Implementasi', 'state' => 'todo', 'status' => 'Belum tersedia'],
        ];
    }

    public function proposal()
    {
        $kelompok = $this->kelompokAktif();

        $dbProposal = $kelompok
            ? Proposal::with('riwayatProposal')->where('kelompok_id', $kelompok->id)->latest()->first()
            : null;

        if ($dbProposal) {
            $latestVersion = $dbProposal->riwayatProposal()->max('versi') ?? 1;
            $versiLabel = 'VERSI '.str_pad((string) $latestVersion, 2, '0', STR_PAD_LEFT);

            $statusRaw = strtolower($dbProposal->status);
            $statusFormatted = match ($statusRaw) {
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
                'revisi' => 'Revisi',
                default => 'Diajukan',
            };

            $timeline = [
                [
                    'event' => 'Draf & Proposal diajukan',
                    'date' => $dbProposal->tanggal_pengajuan ? $dbProposal->tanggal_pengajuan->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                    'state' => 'done',
                ],
                [
                    'event' => 'Pemeriksaan oleh pembimbing',
                    'date' => in_array($statusRaw, ['disetujui', 'ditolak', 'revisi']) ? 'Selesai' : 'Sedang ditinjau',
                    'state' => in_array($statusRaw, ['disetujui', 'ditolak', 'revisi']) ? 'done' : 'current',
                ],
                [
                    'event' => match ($statusRaw) {
                        'disetujui' => 'Proposal disetujui',
                        'ditolak' => 'Proposal ditolak',
                        'revisi' => 'Perlu revisi',
                        default => 'Menunggu keputusan',
                    },
                    'date' => in_array($statusRaw, ['disetujui', 'ditolak', 'revisi']) ? 'Tercatat' : 'Menunggu',
                    'state' => in_array($statusRaw, ['disetujui', 'ditolak', 'revisi']) ? 'done' : '',
                ],
            ];

            $proposalData = [
                'id' => $dbProposal->id,
                'versi' => $versiLabel,
                'judul' => $dbProposal->judul,
                'deskripsi' => $dbProposal->deskripsi,
                'latar_belakang' => $dbProposal->latar_belakang,
                'tujuan' => $dbProposal->tujuan,
                'metode' => $dbProposal->metode,
                'file_proposal' => $dbProposal->file_proposal,
                'file_name' => $dbProposal->file_proposal ? 'proposal-'.$dbProposal->id.'.pdf' : 'proposal-pbl.pdf',
                'file_url' => $dbProposal->file_proposal ? route('mahasiswa.proposal.download', $dbProposal->id) : null,
                'status' => $statusFormatted,
                'status_raw' => $statusRaw,
                'tanggal' => $dbProposal->tanggal_pengajuan ? $dbProposal->tanggal_pengajuan->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                'feedback' => $dbProposal->catatan_revisi ?: 'Belum ada catatan atau revisi dari pembimbing.',
                'timeline' => $timeline,
            ];
        } else {
            $proposalData = [
                'id' => null,
                'versi' => 'VERSI -',
                'judul' => 'Belum ada proposal',
                'deskripsi' => 'Ajukan proposal pertama untuk kelompok ini.',
                'latar_belakang' => 'Belum ada data.',
                'tujuan' => 'Belum ada data.',
                'metode' => 'Belum ada data.',
                'file_proposal' => null,
                'file_name' => '',
                'file_url' => null,
                'status' => 'Belum diajukan',
                'status_raw' => 'belum diajukan',
                'tanggal' => '',
                'feedback' => 'Belum ada catatan dari pembimbing.',
                'timeline' => [],
            ];
        }

        return view('mahasiswa.proposal', array_merge(
            $this->sharedViewData(),
            ['proposal' => $proposalData]
        ));
    }

    public function logbook()
    {
        /** @var Mahasiswa|null $mahasiswa */
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $kelompok = $this->kelompokAktif();

        $records = ($mahasiswa && $kelompok)
            ? LogbookMilestone::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('kelompok_id', $kelompok->id)
                ->orderBy('tanggal')
                ->orderBy('id')
                ->get()
            : collect();

        $normalize = fn ($value) => strtolower(trim((string) $value));

        $logbookStats = [
            'total' => $records->count(),
            'menunggu' => $records->filter(
                fn ($record) => in_array($normalize($record->status), ['diajukan', 'menunggu review', 'menunggu'], true)
            )->count(),
            'disetujui' => $records->filter(fn ($record) => $normalize($record->status) === 'disetujui')->count(),
            'revisi' => $records->filter(fn ($record) => $normalize($record->status) === 'revisi')->count(),
            'terbaru' => $records->sortByDesc('tanggal')->first(),
        ];

        $logbooks = $records->map(function (LogbookMilestone $logbook) {
            $status = match (strtolower((string) $logbook->status)) {
                'disetujui' => 'Disetujui',
                'revisi' => 'Revisi',
                'ditolak' => 'Ditolak',
                default => 'Menunggu Review',
            };

            return [
                'id' => (string) $logbook->id,
                'tanggal' => $logbook->tanggal->toDateString(),
                'judul' => $logbook->judul ?: $logbook->kegiatan,
                'deskripsi' => $logbook->deskripsi,
                'progress' => (int) $logbook->progress,
                'hasil' => $logbook->hasil,
                'rencana' => $logbook->rencana,
                'kendala' => $logbook->kendala,
                'status' => $status,
                'feedback' => $logbook->feedback,
            ];
        })->all();

        $earliest = null;

        foreach ($logbooks as $logbook) {
            $timestamp = strtotime($logbook['tanggal']);

            if ($earliest === null || $timestamp < $earliest) {
                $earliest = $timestamp;
            }
        }

        foreach ($logbooks as $index => $logbook) {
            $diffDays = ($earliest !== null)
                ? (strtotime($logbook['tanggal']) - $earliest) / 86400
                : 0;

            $logbooks[$index]['minggu'] = (int) floor($diffDays / 7) + 1;
        }

        return view('mahasiswa.logbook', array_merge($this->sharedViewData(), [
            'logbooks' => $logbooks,
            'earliestTimestamp' => $earliest ?? time(),
            'kelompok' => $kelompok,
            'logbookStats' => $logbookStats,
        ]));
    }

    public function notifikasi()
    {
        return view('mahasiswa.notifikasi', $this->sharedViewData());
    }

    /** Halaman daftar Milestone per Mata Kuliah */
    public function milestone()
    {
        $kelompok = $this->kelompokAktif();

        $mataKuliahModels = MataKuliah::query()
            ->where('is_aktif', true)
            ->whereIn('nama_matkul', self::MATA_KULIAH_SIM_PBL)
            ->get()
            ->sortBy(fn (MataKuliah $mataKuliah) => array_search(
                $mataKuliah->nama_matkul,
                self::MATA_KULIAH_SIM_PBL,
                true
            ))
            ->values();

        $mataKuliahIds = $mataKuliahModels->pluck('id')->all();
        $progresMap = $kelompok
            ? app(ProgressService::class)->untukDaftarMataKuliah($kelompok, $mataKuliahModels)
            : [];

        $dosenMap = $kelompok
            ? DosenPengampu::query()
                ->with('dosen')
                ->where('kelas_id', $kelompok->kelas_id)
                ->whereIn('mata_kuliah_id', $mataKuliahIds)
                ->get()
                ->keyBy('mata_kuliah_id')
            : collect();

        $mataKuliahList = $mataKuliahModels->map(function (MataKuliah $mataKuliah) use ($progresMap, $dosenMap) {
            $progres = $progresMap[$mataKuliah->id] ?? $this->kosongProgres();
            $status = $this->statusProgres($progres);

            return [
                'id' => $mataKuliah->id,
                'kode' => $mataKuliah->kode ?? 'MK-'.$mataKuliah->id,
                'slug' => $mataKuliah->slug ?: Str::slug((string) $mataKuliah->nama_matkul),
                'nama' => $mataKuliah->nama_matkul,
                'judul' => $mataKuliah->nama_matkul,
                'dosen' => $dosenMap->get($mataKuliah->id)?->dosen?->nama ?? 'Belum ditugaskan',
                'progress' => $progres['persen'],
                'selesai' => $progres['selesai'],
                'total' => $progres['total'],
                'punya_data' => $progres['punya_data'],
                'status' => $status,
            ];
        })->values()->all();

        return view('mahasiswa.milestone', array_merge($this->sharedViewData(), [
            'milestones' => $mataKuliahList,
            'mataKuliahList' => $mataKuliahList,
        ]));
    }

    /** Halaman Level 2: Detail Milestone per Mata Kuliah */
    public function milestoneDetail($id)
    {
        $slug = (string) $id;

        $mataKuliah = MataKuliah::query()
            ->where('slug', $slug)
            ->where('is_aktif', true)
            ->first();

        if (! $mataKuliah) {
            abort(404);
        }

        $namaMatkul = $mataKuliah->nama_matkul;
        $kelompok = $this->kelompokAktif();

        $submissions = $kelompok
            ? PengumpulanMilestone::with(['milestone', 'pengunggah'])
                ->where('kelompok_id', $kelompok->id)
                ->where('mata_kuliah', $mataKuliah->slug)
                ->latest()
                ->get()
            : collect();

        $anggotaKelompok = $this->anggotaView($kelompok);
        $progresMatkul = $kelompok
            ? app(ProgressService::class)->untukMataKuliah($kelompok, $mataKuliah)
            : $this->kosongProgres();
        $statusMilestone = $this->statusProgres($progresMatkul);
        $milestoneOptions = $kelompok
            ? Milestone::query()
                ->where('periode_id', $kelompok->periode_id)
                ->whereHas('mataKuliah', fn ($query) => $query->where('mata_kuliah.id', $mataKuliah->id))
                ->orderBy('urutan')
                ->get()
            : collect();

        $milestones = $submissions->map(function (PengumpulanMilestone $submission) {
            $milestone = $submission->milestone;
            $extension = strtolower(pathinfo($submission->file_name, PATHINFO_EXTENSION));

            $dokumen = [
                [
                    'tipe' => $extension === 'pdf' ? 'pdf' : ($extension === 'zip' ? 'zip' : 'file'),
                    'nama' => $submission->file_name,
                    'sub' => 'Dokumen '.strtoupper($extension ?: 'FILE'),
                    'action' => 'download',
                    'url' => route('mahasiswa.milestone.berkas.download', $submission->id),
                ],
            ];

            if ($submission->link_bukti) {
                $dokumen[] = [
                    'tipe' => 'github',
                    'nama' => 'Tautan Repositori / Demo',
                    'sub' => preg_replace('#^https?://#', '', $submission->link_bukti),
                    'action' => 'open',
                    'url' => $submission->link_bukti,
                ];
            }

            return [
                'id' => $submission->milestone_id,
                'judul' => $milestone
                    ? 'Milestone '.$milestone->urutan.' – '.$milestone->judul
                    : 'Milestone',
                'status' => strtolower((string) $submission->status) === 'diajukan'
                    ? 'Menunggu Review'
                    : ucfirst((string) $submission->status),
                'ringkasan' => $submission->catatan ?? '',
                'waktu' => $submission->created_at
                    ? $submission->created_at->translatedFormat('d F Y, H:i').' WIB'
                    : '',
                'pengunggah' => $submission->pengunggah?->nama ?? 'Mahasiswa',
                'dokumen' => $dokumen,
            ];
        })->all();

        return view('mahasiswa.milestone-detail', array_merge($this->sharedViewData(), [
            'slug' => $slug,
            'namaMatkul' => $namaMatkul,
            'kelompok' => $kelompok,
            'anggotaKelompok' => $anggotaKelompok,
            'berkasTerkumpul' => ! empty($milestones),
            'milestones' => $milestones,
            'milestoneOptions' => $milestoneOptions,
            'progresMatkul' => $progresMatkul,
            'statusMilestone' => $statusMilestone,
        ]));
    }

    /** Menyimpan pengumpulan milestone & berkas pendukung (Pertemuan 6: Validasi & File Storage) */
    public function storeMilestoneUpload(Request $request, $id)
    {
        $slug = (string) $id;

        $mataKuliah = MataKuliah::query()
            ->where('slug', $slug)
            ->where('is_aktif', true)
            ->first();

        if (! $mataKuliah) {
            abort(404);
        }

        /** @var Mahasiswa|null $mahasiswa */
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $kelompok = $this->kelompokAktif();

        if (! $mahasiswa || ! $kelompok) {
            return redirect()
                ->route('mahasiswa.milestone.detail', $mataKuliah->slug)
                ->withErrors(['berkas' => 'Anda belum tergabung dalam kelompok pada periode aktif.']);
        }

        $validated = $request->validate([
            'milestone_id' => [
                'required',
                'integer',
                Rule::exists('milestone', 'id')->where(
                    fn ($query) => $query->where('periode_id', $kelompok->periode_id)
                ),
                Rule::exists('milestone_mata_kuliah', 'milestone_id')
                    ->where('mata_kuliah_id', $mataKuliah->id),
            ],
            'catatan' => ['required', 'string', 'max:2000'],
            'berkas' => ['required', 'file', 'mimes:pdf,zip', 'max:20480'],
            'link_bukti' => ['nullable', 'url', 'max:255'],
        ], [
            'milestone_id.required' => 'Tahapan milestone wajib dipilih.',
            'milestone_id.exists' => 'Milestone tidak terdaftar untuk mata kuliah ini pada periode kelompok Anda.',
            'catatan.required' => 'Ringkasan capaian milestone wajib diisi.',
            'berkas.required' => 'Berkas laporan (PDF/ZIP) wajib diunggah.',
            'berkas.file'           => 'Berkas laporan yang diunggah tidak valid atau melebihi batas upload server.',
            'berkas.mimes' => 'Format berkas harus berupa PDF atau ZIP.',
            'berkas.max' => 'Ukuran file melebihi batas maksimal yang diperbolehkan (maksimal 20 MB). Silakan gunakan file dengan ukuran lebih kecil.',
            'link_bukti.url' => 'Format tautan repositori/demo tidak valid (harus diawali http:// atau https://).',
        ]);

        $file = $request->file('berkas');
        $originalName = $file->getClientOriginalName();
        $storedPath = $file->store(
            'milestone-submissions/'.$kelompok->id.'/'.$validated['milestone_id'].'/'.$mataKuliah->slug,
            'local'
        );

        $oldPath = null;

        try {
            $submission = DB::transaction(function () use (
                $validated,
                $mataKuliah,
                $kelompok,
                $mahasiswa,
                $originalName,
                $storedPath,
                &$oldPath
            ) {
                $existing = PengumpulanMilestone::query()
                    ->where('kelompok_id', $kelompok->id)
                    ->where('milestone_id', $validated['milestone_id'])
                    ->where('mata_kuliah', $mataKuliah->slug)
                    ->lockForUpdate()
                    ->first();

                $oldPath = $existing?->file_path;

                $data = [
                    'kelompok_id' => $kelompok->id,
                    'milestone_id' => $validated['milestone_id'],
                    'diunggah_oleh' => $mahasiswa->id,
                    'mata_kuliah' => $mataKuliah->slug,
                    'file_name' => $originalName,
                    'file_path' => $storedPath,
                    'link_bukti' => $validated['link_bukti'] ?? null,
                    'catatan' => $validated['catatan'],
                    'status' => 'diajukan',
                ];

                if ($existing) {
                    $existing->update($data);

                    return $existing;
                }

                return PengumpulanMilestone::create($data);
            });
        } catch (UniqueConstraintViolationException $exception) {
            Storage::disk('local')->delete($storedPath);

            $tripleExists = PengumpulanMilestone::query()
                ->where('kelompok_id', $kelompok->id)
                ->where('milestone_id', $validated['milestone_id'])
                ->where('mata_kuliah', $mataKuliah->slug)
                ->exists();

            if (! $tripleExists) {
                throw $exception;
            }

            return redirect()
                ->route('mahasiswa.milestone.detail', $mataKuliah->slug)
                ->withErrors([
                    'berkas' => 'Submission untuk milestone dan mata kuliah ini sudah ada. '
                        .'Muat ulang halaman lalu coba lagi.',
                ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($storedPath);

            throw $exception;
        }

        if ($oldPath && $oldPath !== $storedPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()
            ->route('mahasiswa.milestone.detail', $mataKuliah->slug)
            ->with('success', 'Laporan milestone berhasil dikumpulkan dan menunggu review dosen!');
    }

    public function downloadMilestoneSubmission(PengumpulanMilestone $pengumpulan)
    {
        $kelompok = $this->kelompokAktif();

        abort_unless($kelompok && $pengumpulan->kelompok_id === $kelompok->id, 404);

        abort_unless(
            $pengumpulan->file_path && Storage::disk('local')->exists($pengumpulan->file_path),
            404
        );

        return Storage::disk('local')->download($pengumpulan->file_path, $pengumpulan->file_name);
    }

    /**
     * Menangani pengajuan proposal proyek PBL beserta unggah berkas PDF ke database & storage.
     */
    public function storeProposal(Request $request)
    {
        $kelompok = $this->kelompokAktif();

        if (! $kelompok) {
            return redirect()
                ->route('mahasiswa.proposal')
                ->withErrors(['file_proposal' => 'Anda belum tergabung dalam kelompok pada periode aktif.']);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tujuan' => 'required|string',
            'latar_belakang' => 'nullable|string',
            'metode' => 'nullable|string',
            'file_proposal' => 'required|file|mimes:pdf|max:20480',
        ], [
            'judul.required'          => 'Judul proyek wajib diisi.',
            'deskripsi.required'      => 'Deskripsi singkat wajib diisi.',
            'tujuan.required'         => 'Tujuan wajib diisi.',
            'file_proposal.required'  => 'Berkas proposal (PDF) wajib diunggah.',
            'file_proposal.mimes'     => 'Format berkas harus berupa PDF.',
            'file_proposal.max'       => 'Ukuran berkas proposal maksimal 20 MB.',
        ]);

        $latarBelakang = $validated['latar_belakang'] ?? '-';
        $metode = $validated['metode'] ?? '-';

        $file = $request->file('file_proposal');
        $filePath = $file->storeAs('proposals', Str::random(40).'.pdf', 'local');

        try {
            DB::transaction(function () use (
                $validated,
                $kelompok,
                $latarBelakang,
                $metode,
                $filePath
            ) {
                $existingProposal = Proposal::query()
                    ->where('kelompok_id', $kelompok->id)
                    ->lockForUpdate()
                    ->latest()
                    ->first();

                if ($existingProposal) {
                    $nextVersion = ($existingProposal->riwayatProposal()->max('versi') ?? 0) + 1;

                    $existingProposal->update([
                        'judul' => $validated['judul'],
                        'deskripsi' => $validated['deskripsi'],
                        'latar_belakang' => $latarBelakang,
                        'tujuan' => $validated['tujuan'],
                        'metode' => $metode,
                        'file_proposal' => $filePath,
                        'status' => 'diajukan',
                        'catatan_revisi' => null,
                        'tanggal_pengajuan' => now()->toDateString(),
                    ]);

                    RiwayatProposal::create([
                        'proposal_id' => $existingProposal->id,
                        'versi' => $nextVersion,
                        'judul' => $validated['judul'],
                        'file_proposal' => $filePath,
                        'status' => 'diajukan',
                        'catatan' => 'Pengajuan revisi proposal (Versi '.$nextVersion.')',
                    ]);

                    return;
                }

                $newProposal = Proposal::create([
                    'kelompok_id' => $kelompok->id,
                    'judul' => $validated['judul'],
                    'deskripsi' => $validated['deskripsi'],
                    'latar_belakang' => $latarBelakang,
                    'tujuan' => $validated['tujuan'],
                    'metode' => $metode,
                    'file_proposal' => $filePath,
                    'status' => 'diajukan',
                    'catatan_revisi' => null,
                    'tanggal_pengajuan' => now()->toDateString(),
                ]);

                RiwayatProposal::create([
                    'proposal_id' => $newProposal->id,
                    'versi' => 1,
                    'judul' => $validated['judul'],
                    'file_proposal' => $filePath,
                    'status' => 'diajukan',
                    'catatan' => 'Pengajuan proposal awal (Versi 01)',
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($filePath);

            throw $exception;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proposal berhasil diajukan.',
                'status' => 'diajukan',
            ]);
        }

        return redirect()->route('mahasiswa.proposal')->with('success', 'Proposal berhasil diajukan dan sedang menunggu review dosen pembimbing!');
    }

    /**
     * Mengunduh berkas PDF proposal yang tersimpan di storage.
     */
    public function downloadProposal($id = null)
    {
        $kelompok = $this->kelompokAktif();

        abort_unless($kelompok, 404);

        $proposal = $id
            ? Proposal::query()->where('kelompok_id', $kelompok->id)->findOrFail($id)
            : Proposal::where('kelompok_id', $kelompok->id)->latest()->first();

        if (! $proposal || ! $proposal->file_proposal) {
            abort(404, 'Berkas proposal tidak ditemukan.');
        }

        $downloadName = 'proposal-'.$proposal->id.'.pdf';

        if (Storage::disk('local')->exists($proposal->file_proposal)) {
            return Storage::disk('local')->download($proposal->file_proposal, $downloadName);
        }

        if (Storage::disk('public')->exists($proposal->file_proposal)) {
            return Storage::disk('public')->download($proposal->file_proposal, $downloadName);
        }

        abort(404, 'Berkas proposal tidak ditemukan.');
    }

    /**
     * Menangani penambahan catatan logbook (backend stub).
     * Menerima data dari form modal tambah logbook dan mengembalikan JSON.
     */
    public function storeLogbook(Request $request)
    {
        /** @var Mahasiswa|null $mahasiswa */
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $kelompok = $this->kelompokAktif();

        if (! $mahasiswa || ! $kelompok) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum tergabung dalam kelompok pada periode aktif.',
            ], 422);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'progress' => 'required|integer|min:0|max:100',
            'hasil' => 'nullable|string',
            'kendala' => 'nullable|string',
            'rencana' => 'nullable|string',
        ]);

        $logbook = LogbookMilestone::create([
            'milestone_id' => null,
            'kelompok_id' => $kelompok->id,
            'mahasiswa_id' => $mahasiswa->id,
            'tanggal' => $validated['tanggal'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'kegiatan' => filled($validated['deskripsi'] ?? null)
                ? $validated['deskripsi']
                : $validated['judul'],
            'progress' => $validated['progress'],
            'hasil' => $validated['hasil'] ?? null,
            'kendala' => $validated['kendala'] ?? null,
            'rencana' => $validated['rencana'] ?? null,
            'status' => 'diajukan',
            'feedback' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan logbook berhasil ditambahkan.',
            'data' => [
                'id' => (string) $logbook->id,
                'tanggal' => $logbook->tanggal->toDateString(),
                'judul' => $logbook->judul,
                'deskripsi' => $logbook->deskripsi,
                'progress' => (int) $logbook->progress,
                'hasil' => $logbook->hasil,
                'rencana' => $logbook->rencana,
                'kendala' => $logbook->kendala,
                'status' => 'Menunggu Review',
                'feedback' => null,
            ],
        ]);
    }

    /**
     * Update Logbook Entry (backend stub).
     *
     * Menerima data edit dari modal logbook.
     * Mengembalikan JSON sukses untuk diproses oleh JS.
     * Implementasi update ke DB dapat ditambahkan
     * setelah tabel logbook_milestone tersedia.
     */
    public function updateLogbook(Request $request, string $id)
    {
        $validated = $request->validate([
            'tanggal'  => 'required|date',
            'judul'    => 'required|string|max:255',
            'deskripsi'=> 'nullable|string',
            'progress' => 'required|integer|min:0|max:100',
            'hasil'    => 'nullable|string',
            'kendala'  => 'nullable|string',
            'rencana'  => 'nullable|string',
        ]);

        // TODO: update ke database setelah tabel logbook_milestone tersedia
        // $logbook = LogbookMilestone::where('id', $id)
        //     ->where('mahasiswa_id', auth('mahasiswa')->id())
        //     ->firstOrFail();
        // $logbook->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catatan logbook berhasil diperbarui.',
            'data'    => array_merge($validated, ['id' => $id]),
        ]);
    }
}
