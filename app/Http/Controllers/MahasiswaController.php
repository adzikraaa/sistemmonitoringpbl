<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelompok;
use App\Models\LogbookMilestone;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\Notifikasi;
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
    public const MATA_KULIAH_SIM_PBL = [
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
            ->with(['proposal', 'logbookMilestone'])
            ->whereHas('mahasiswa', fn($query) => $query->where('mahasiswa.id', $mahasiswa->id))
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
        $unreadCount = $mahasiswa ? $mahasiswa->notifikasi()->whereNull('dibaca_pada')->count() : 0;

        return [
            'userName' => $mahasiswa?->nama ?? 'Mahasiswa',
            'userNim' => $mahasiswa?->nim ?? '-',
            'userKelas' => $mahasiswa?->kelas_id ?? '-',
            'userProdi' => 'Teknologi Informasi',
            'unreadMhsNotifCount' => $unreadCount,
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
                substr($awal, 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : substr($awal, 1, 1))
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

        $status = fn($record) => strtolower(trim((string) $record->status));

        return [
            'total' => $records->count(),
            'menunggu' => $records->filter(
                fn($record) => in_array($status($record), ['diajukan', 'menunggu review', 'menunggu'], true)
            )->count(),
            'disetujui' => $records->filter(fn($record) => $status($record) === 'disetujui')->count(),
            'revisi' => $records->filter(fn($record) => $status($record) === 'revisi')->count(),
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
            $versiLabel = 'VERSI ' . str_pad((string) $latestVersion, 2, '0', STR_PAD_LEFT);

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
                'file_name' => $dbProposal->nama_berkas,
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

        $normalize = fn($value) => strtolower(trim((string) $value));

        $logbookStats = [
            'total' => $records->count(),
            'menunggu' => $records->filter(
                fn($record) => in_array($normalize($record->status), ['diajukan', 'menunggu review', 'menunggu'], true)
            )->count(),
            'disetujui' => $records->filter(fn($record) => $normalize($record->status) === 'disetujui')->count(),
            'revisi' => $records->filter(fn($record) => $normalize($record->status) === 'revisi')->count(),
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
                'file_bukti' => $logbook->file_bukti,
                'file_name' => $logbook->file_bukti ? basename($logbook->file_bukti) : null,
                'file_url' => $logbook->file_bukti ? route('mahasiswa.logbook.download', $logbook->id) : null,
                'link_bukti' => $logbook->link_bukti,
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
        /** @var Mahasiswa|null $mahasiswa */
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $notifikasiList = $mahasiswa ? $mahasiswa->notifikasi()->latest()->get() : collect();

        return view('mahasiswa.notifikasi', array_merge($this->sharedViewData(), [
            'notifikasiList' => $notifikasiList,
        ]));
    }

    public function tandaiNotifikasiDibaca(Request $request, Notifikasi $notifikasi)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        if ($mahasiswa && $notifikasi->penerima_type === $mahasiswa->getMorphClass() && (int) $notifikasi->penerima_id === (int) $mahasiswa->id) {
            $notifikasi->update(['dibaca_pada' => now()]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function tandaiSemuaNotifikasiDibaca(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        if ($mahasiswa) {
            $mahasiswa->notifikasi()->whereNull('dibaca_pada')->update(['dibaca_pada' => now()]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /** Halaman daftar Milestone per Mata Kuliah */
    public function milestone()
    {
        $kelompok = $this->kelompokAktif();

        $mataKuliahModels = MataKuliah::query()
            ->where('is_aktif', true)
            ->whereIn('nama_matkul', self::MATA_KULIAH_SIM_PBL)
            ->get()
            ->sortBy(fn(MataKuliah $mataKuliah) => array_search(
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
                'kode' => $mataKuliah->kode ?? 'MK-' . $mataKuliah->id,
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
            ->whereHas('mataKuliah', fn($query) => $query->where('mata_kuliah.id', $mataKuliah->id))
            ->orderBy('urutan')
            ->get()
            : collect();

        $submissionsByMilestone = $submissions->keyBy('milestone_id');

        $formatSubmission = function (PengumpulanMilestone $submission, ?Milestone $milestone = null): array {
            $milestone ??= $submission->milestone;
            $extension = strtolower(pathinfo((string) $submission->file_name, PATHINFO_EXTENSION));

            $dokumen = [];
            if ($submission->file_name) {
                $dokumen[] = [
                    'tipe' => $extension === 'pdf' ? 'pdf' : ($extension === 'zip' ? 'zip' : 'file'),
                    'nama' => $submission->file_name,
                    'sub' => 'Dokumen ' . strtoupper($extension ?: 'FILE'),
                    'action' => 'download',
                    'url' => route('mahasiswa.milestone.berkas.download', $submission->id),
                ];
            }

            if ($submission->link_bukti) {
                $dokumen[] = [
                    'tipe' => 'github',
                    'nama' => 'Tautan Repositori / Demo',
                    'sub' => preg_replace('#^https?://#', '', $submission->link_bukti),
                    'action' => 'open',
                    'url' => $submission->link_bukti,
                ];
            }

            $waktuUpload = $submission->updated_at ?? $submission->created_at;

            return [
                'id' => $submission->milestone_id,
                'judul' => $milestone
                    ? 'Milestone ' . $milestone->urutan . ' – ' . $milestone->judul
                    : 'Milestone',
                'status' => strtolower((string) $submission->status) === 'diajukan'
                    ? 'Menunggu Review'
                    : ucfirst((string) $submission->status),
                'ringkasan' => $submission->catatan ?? '',
                'waktu' => $waktuUpload
                    ? $waktuUpload->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB'
                    : '',
                'pengunggah' => $submission->pengunggah?->nama ?? 'Mahasiswa',
                'dokumen' => $dokumen,
                'sudah_dikumpulkan' => true,
            ];
        };

        if ($milestoneOptions->isNotEmpty()) {
            $milestones = $milestoneOptions->map(function (Milestone $milestone) use ($submissionsByMilestone, $formatSubmission) {
                /** @var PengumpulanMilestone|null $submission */
                $submission = $submissionsByMilestone->get($milestone->id);

                if ($submission) {
                    return $formatSubmission($submission, $milestone);
                }

                return [
                    'id' => $milestone->id,
                    'judul' => 'Milestone ' . $milestone->urutan . ' – ' . $milestone->judul,
                    'status' => 'Belum Dikumpulkan',
                    'ringkasan' => '',
                    'waktu' => '',
                    'pengunggah' => '',
                    'dokumen' => [],
                    'sudah_dikumpulkan' => false,
                ];
            })->all();
        } else {
            $milestones = $submissions->map(
                fn(PengumpulanMilestone $submission) => $formatSubmission($submission)
            )->all();
        }

        return view('mahasiswa.milestone-detail', array_merge($this->sharedViewData(), [
            'slug' => $mataKuliah->slug,
            'namaMatkul' => $namaMatkul,
            'kelompok' => $kelompok,
            'berkasTerkumpul' => $submissions->isNotEmpty(),
            'milestones' => $milestones,
            'milestoneOptions' => $milestoneOptions,
            'anggotaKelompok' => $anggotaKelompok,
            'progresMatkul' => $progresMatkul,
            'statusMilestone' => $statusMilestone,
        ]));
    }

    /**
     * Mengirim notifikasi pengingat ke seluruh dosen terkait kelompok (Dosen Pembimbing & Dosen Pengampu).
     */
    private function notifyDosenKelompok(Kelompok $kelompok, string $judul, string $isi, ?string $tautan = null, ?int $mataKuliahId = null): void
    {
        try {
            $targetDosenIds = collect();

            if (! empty($kelompok->dosen_id)) {
                $targetDosenIds->push((int) $kelompok->dosen_id);
            }

            if (! empty($kelompok->kelas_id)) {
                $query = DosenPengampu::where('kelas_id', $kelompok->kelas_id);
                if ($mataKuliahId) {
                    $matkulDosenIds = (clone $query)->where('mata_kuliah_id', $mataKuliahId)->pluck('dosen_id');
                    if ($matkulDosenIds->isNotEmpty()) {
                        $targetDosenIds = $targetDosenIds->merge($matkulDosenIds);
                    } else {
                        $targetDosenIds = $targetDosenIds->merge($query->pluck('dosen_id'));
                    }
                } else {
                    $targetDosenIds = $targetDosenIds->merge($query->pluck('dosen_id'));
                }
            }

            $uniqueDosenIds = $targetDosenIds->filter()->unique()->values()->all();

            // Fallback: jika belum ada dosen pembimbing atau pengampu terdaftar, kirim ke seluruh dosen aktif
            if (empty($uniqueDosenIds)) {
                $uniqueDosenIds = Dosen::where('is_aktif', true)->pluck('id')->all();
                if (empty($uniqueDosenIds)) {
                    $uniqueDosenIds = Dosen::pluck('id')->all();
                }
            }

            foreach ($uniqueDosenIds as $dosenId) {
                Notifikasi::create([
                    'penerima_type' => (new Dosen)->getMorphClass(),
                    'penerima_id' => $dosenId,
                    'judul' => $judul,
                    'isi' => $isi,
                    'tautan' => $tautan,
                    'dibaca_pada' => null,
                ]);
            }
        } catch (\Throwable $e) {
            // Failsafe agar kegagalan notifikasi tidak memutus alur penyimpanan berkas utama
        }
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
                    fn($query) => $query->where('periode_id', $kelompok->periode_id)
                ),
                Rule::exists('milestone_mata_kuliah', 'milestone_id')
                    ->where('mata_kuliah_id', $mataKuliah->id),
            ],
            'catatan' => ['required', 'string', 'max:2000'],
            'berkas' => ['required', 'file', 'mimes:pdf,zip', 'max:2048'],
            'link_bukti' => ['nullable', 'url', 'max:255'],
        ], [
            'milestone_id.required' => 'Tahapan milestone wajib dipilih.',
            'milestone_id.exists' => 'Milestone tidak terdaftar untuk mata kuliah ini pada periode kelompok Anda.',
            'catatan.required' => 'Ringkasan capaian milestone wajib diisi.',
            'berkas.required' => 'Berkas laporan (PDF/ZIP) wajib diunggah.',
            'berkas.file' => 'Berkas laporan yang diunggah tidak valid atau melebihi batas upload server.',
            'berkas.mimes' => 'Format berkas harus berupa PDF atau ZIP.',
            'berkas.max' => 'Ukuran file tidak aman — melebihi batas maksimal 2 MB. Harap gunakan file yang lebih kecil.',
            'link_bukti.url' => 'Format tautan repositori/demo tidak valid (harus diawali http:// atau https://).',
        ]);

        $file = $request->file('berkas');
        $originalName = $file->getClientOriginalName();
        $storedPath = $file->store(
            'milestone-submissions/' . $kelompok->id . '/' . $validated['milestone_id'] . '/' . $mataKuliah->slug,
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($existing) {
                    $existing->fill($data);
                    $existing->created_at = now();
                    $existing->updated_at = now();
                    $existing->save();

                    return $existing;
                }

                $submission = new PengumpulanMilestone;
                $submission->fill($data);
                $submission->created_at = now();
                $submission->updated_at = now();
                $submission->save();

                return $submission;
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
                        . 'Muat ulang halaman lalu coba lagi.',
                ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($storedPath);

            throw $exception;
        }

        if ($oldPath && $oldPath !== $storedPath) {
            Storage::disk('local')->delete($oldPath);
        }

        $namaMilestone = Milestone::find($validated['milestone_id'])?->judul ?? 'Milestone';
        $this->notifyDosenKelompok(
            $kelompok,
            'Laporan Milestone Dikumpulkan',
            'Kelompok "' . $kelompok->nama_kelompok . '" telah mengumpulkan laporan "' . $namaMilestone . '" untuk mata kuliah ' . $mataKuliah->nama_matkul . '.',
            route('dosen.laporan-aktivitas.milestone', [$kelompok->id, $validated['milestone_id']]),
            $mataKuliah->id
        );

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
            'file_proposal' => 'required|file|mimes:pdf|max:2048',
        ], [
            'judul.required' => 'Judul proyek wajib diisi.',
            'deskripsi.required' => 'Deskripsi singkat wajib diisi.',
            'tujuan.required' => 'Tujuan wajib diisi.',
            'file_proposal.required' => 'Berkas proposal (PDF) wajib diunggah.',
            'file_proposal.mimes' => 'Format berkas harus berupa PDF.',
            'file_proposal.max' => 'Ukuran file tidak aman — melebihi batas maksimal 2 MB. Harap gunakan file yang lebih kecil.',
        ]);

        $latarBelakang = $validated['latar_belakang'] ?? '-';
        $metode = $validated['metode'] ?? '-';

        $existingProposal = Proposal::query()
            ->where('kelompok_id', $kelompok->id)
            ->latest()
            ->first();
        $nextVersion = $existingProposal ? (($existingProposal->riwayatProposal()->max('versi') ?? 1) + 1) : 1;

        $file = $request->file('file_proposal');
        $originalName = $file->getClientOriginalName();
        $safeOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
        if (! str_ends_with(strtolower($safeOriginalName), '.pdf')) {
            $safeOriginalName .= '.pdf';
        }
        $fileName = 'v' . $nextVersion . '_' . $safeOriginalName;
        $filePath = $file->storeAs('proposals/' . $kelompok->id, $fileName, 'local');

        try {
            DB::transaction(function () use (
                $validated,
                $kelompok,
                $latarBelakang,
                $metode,
                $filePath,
                $nextVersion
            ) {
                $existingProposal = Proposal::query()
                    ->where('kelompok_id', $kelompok->id)
                    ->lockForUpdate()
                    ->latest()
                    ->first();

                if ($existingProposal) {
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
                        'catatan' => 'Pengajuan revisi proposal (Versi ' . $nextVersion . ')',
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

        $prop = Proposal::where('kelompok_id', $kelompok->id)->latest()->first();
        $this->notifyDosenKelompok(
            $kelompok,
            'Pengajuan Proposal Proyek Baru',
            'Kelompok "' . $kelompok->nama_kelompok . '" telah mengajukan proposal: "' . $validated['judul'] . '". Silakan tinjau proposal tersebut.',
            $prop ? route('dosen.laporan-aktivitas.proposal', $prop->id) : route('dosen.laporan-aktivitas')
        );

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
     * Memperbarui proposal proyek yang sudah ada.
     */
    public function updateProposal(Request $request, $id = null)
    {
        $kelompok = $this->kelompokAktif();

        if (! $kelompok) {
            return redirect()->route('mahasiswa.proposal')->withErrors([
                'general' => 'Kelompok tidak ditemukan untuk periode aktif.',
            ]);
        }

        $proposal = $id
            ? Proposal::where('kelompok_id', $kelompok->id)->findOrFail($id)
            : Proposal::where('kelompok_id', $kelompok->id)->latest()->firstOrFail();

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tujuan' => 'required|string',
            'latar_belakang' => 'nullable|string',
            'metode' => 'nullable|string',
            'file_proposal' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'judul.required' => 'Judul proyek wajib diisi.',
            'deskripsi.required' => 'Deskripsi singkat wajib diisi.',
            'tujuan.required' => 'Tujuan wajib diisi.',
            'file_proposal.mimes' => 'Format berkas harus berupa PDF.',
            'file_proposal.max' => 'Ukuran file tidak aman — melebihi batas maksimal 2 MB. Harap gunakan file yang lebih kecil.',
        ]);

        $latarBelakang = $validated['latar_belakang'] ?? $proposal->latar_belakang ?: '-';
        $metode = $validated['metode'] ?? $proposal->metode ?: '-';

        $filePath = $proposal->file_proposal;
        $fileUploaded = false;
        $nextVersion = ($proposal->riwayatProposal()->max('versi') ?? 1) + 1;

        if ($request->hasFile('file_proposal')) {
            $file = $request->file('file_proposal');
            $originalName = $file->getClientOriginalName();
            $safeOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
            if (! str_ends_with(strtolower($safeOriginalName), '.pdf')) {
                $safeOriginalName .= '.pdf';
            }
            $fileName = 'v' . $nextVersion . '_' . $safeOriginalName;
            $filePath = $file->storeAs('proposals/' . $kelompok->id, $fileName, 'local');
            $fileUploaded = true;
        }

        try {
            DB::transaction(function () use (
                $proposal,
                $validated,
                $latarBelakang,
                $metode,
                $filePath,
                $nextVersion
            ) {

                $proposal->update([
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
                    'proposal_id' => $proposal->id,
                    'versi' => $nextVersion,
                    'judul' => $validated['judul'],
                    'file_proposal' => $filePath,
                    'status' => 'diajukan',
                    'catatan' => 'Pembaruan proposal (Versi ' . str_pad((string) $nextVersion, 2, '0', STR_PAD_LEFT) . ')',
                ]);
            });
        } catch (\Throwable $exception) {
            if ($fileUploaded && $filePath) {
                Storage::disk('local')->delete($filePath);
            }

            throw $exception;
        }

        $this->notifyDosenKelompok(
            $kelompok,
            'Pembaruan Proposal Proyek',
            'Kelompok "' . $kelompok->nama_kelompok . '" telah memperbarui proposal: "' . $validated['judul'] . '". Silakan tinjau kembali proposal tersebut.',
            route('dosen.laporan-aktivitas.proposal', $proposal->id)
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proposal berhasil diperbarui.',
                'status' => 'diajukan',
            ]);
        }

        return redirect()->route('mahasiswa.proposal')->with('success', 'Proposal berhasil diperbarui dan diajukan ulang ke dosen pembimbing!');
    }

    /**
     * Menghapus proposal proyek beserta berkas dan riwayatnya.
     */
    public function destroyProposal(Request $request, $id = null)
    {
        $kelompok = $this->kelompokAktif();

        if (! $kelompok) {
            return redirect()->route('mahasiswa.proposal')->withErrors([
                'general' => 'Kelompok tidak ditemukan untuk periode aktif.',
            ]);
        }

        $proposal = $id
            ? Proposal::where('kelompok_id', $kelompok->id)->findOrFail($id)
            : Proposal::where('kelompok_id', $kelompok->id)->latest()->first();

        if (! $proposal) {
            return redirect()->route('mahasiswa.proposal')->withErrors([
                'general' => 'Proposal tidak ditemukan.',
            ]);
        }

        try {
            DB::transaction(function () use ($proposal) {
                if ($proposal->file_proposal && Storage::disk('local')->exists($proposal->file_proposal)) {
                    Storage::disk('local')->delete($proposal->file_proposal);
                }

                foreach ($proposal->riwayatProposal as $riwayat) {
                    if ($riwayat->file_proposal && $riwayat->file_proposal !== $proposal->file_proposal && Storage::disk('local')->exists($riwayat->file_proposal)) {
                        Storage::disk('local')->delete($riwayat->file_proposal);
                    }
                }

                $proposal->riwayatProposal()->delete();
                $proposal->delete();
            });
        } catch (\Throwable $e) {
            return redirect()->route('mahasiswa.proposal')->withErrors([
                'general' => 'Gagal menghapus proposal: ' . $e->getMessage(),
            ]);
        }

        return redirect()->route('mahasiswa.proposal')->with('success', 'Proposal berhasil dihapus. Anda dapat mengajukan proposal baru.');
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

        $downloadName = $proposal->nama_berkas;

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
            'link_bukti' => 'nullable|string|max:500',
            'file_bukti' => 'nullable|file|mimes:pdf,zip,png,jpg,jpeg|max:2048',
        ], [
            'file_bukti.max' => 'Ukuran file tidak aman — melebihi batas maksimal 2 MB. Harap gunakan file yang lebih kecil.',
            'file_bukti.mimes' => 'Format file tidak didukung. Gunakan PDF, ZIP, PNG, atau JPG.',
        ]);

        $filePath = null;
        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $filePath = $file->storeAs('logbook-attachments/' . $kelompok->id, Str::random(40) . '.' . $file->getClientOriginalExtension(), 'local');
        }

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
            'link_bukti' => $validated['link_bukti'] ?? null,
            'file_bukti' => $filePath,
            'status' => 'diajukan',
            'feedback' => null,
        ]);

        $this->notifyDosenKelompok(
            $kelompok,
            'Entri Logbook Baru',
            'Kelompok "' . $kelompok->nama_kelompok . '" menambahkan catatan logbook baru: "' . $logbook->judul . '".',
            route('dosen.laporan-aktivitas.logbook', $logbook->id)
        );

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
                'link_bukti' => $logbook->link_bukti,
                'file_bukti' => $logbook->file_bukti,
                'file_name' => $logbook->file_bukti ? basename($logbook->file_bukti) : null,
                'status' => 'Menunggu Review',
                'feedback' => null,
            ],
        ]);
    }

    /**
     * Update Logbook Entry.
     *
     * Menerima data edit dari modal logbook dan menyimpan perubahan ke database.
     */
    public function updateLogbook(Request $request, string $id)
    {
        /** @var Mahasiswa|null $mahasiswa */
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $kelompok = $this->kelompokAktif();

        if (! $mahasiswa || ! $kelompok) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid. Silakan login kembali.',
            ], 401);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'progress' => 'required|integer|min:0|max:100',
            'hasil' => 'nullable|string',
            'kendala' => 'nullable|string',
            'rencana' => 'nullable|string',
            'link_bukti' => 'nullable|string|max:500',
            'file_bukti' => 'nullable|file|mimes:pdf,zip,png,jpg,jpeg|max:2048',
        ], [
            'file_bukti.max' => 'Ukuran file tidak aman — melebihi batas maksimal 2 MB. Harap gunakan file yang lebih kecil.',
            'file_bukti.mimes' => 'Format file tidak didukung. Gunakan PDF, ZIP, PNG, atau JPG.',
        ]);

        $logbook = LogbookMilestone::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $updateData = [
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
            'link_bukti' => $validated['link_bukti'] ?? $logbook->link_bukti,
        ];

        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $updateData['file_bukti'] = $file->storeAs('logbook-attachments/' . $kelompok->id, Str::random(40) . '.' . $file->getClientOriginalExtension(), 'local');
        }

        $logbook->update($updateData);

        $this->notifyDosenKelompok(
            $kelompok,
            'Pembaruan Catatan Logbook',
            'Kelompok "' . $kelompok->nama_kelompok . '" memperbarui catatan logbook: "' . $logbook->judul . '".',
            route('dosen.laporan-aktivitas.logbook', $logbook->id)
        );

        return response()->json([
            'success' => true,
            'message' => 'Catatan logbook berhasil diperbarui.',
            'data' => array_merge($validated, [
                'id' => $id,
                'file_bukti' => $logbook->file_bukti,
                'file_name' => $logbook->file_bukti ? basename($logbook->file_bukti) : null,
            ]),
        ]);
    }

    /**
     * Unduh berkas lampiran logbook.
     */
    public function downloadLogbook(int|string $id)
    {
        /** @var Mahasiswa|null $mahasiswa */
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $kelompok = $this->kelompokAktif();

        if (! $mahasiswa || ! $kelompok) {
            abort(403);
        }

        $logbook = LogbookMilestone::where('kelompok_id', $kelompok->id)->findOrFail($id);

        if (! $logbook->file_bukti) {
            abort(404, 'Berkas logbook tidak ditemukan.');
        }

        $extension = pathinfo($logbook->file_bukti, PATHINFO_EXTENSION) ?: 'pdf';
        $downloadName = 'Logbook-' . Str::slug($logbook->judul ?: 'PBL') . '.' . $extension;

        if (Storage::disk('local')->exists($logbook->file_bukti)) {
            return Storage::disk('local')->download($logbook->file_bukti, $downloadName);
        }

        if (Storage::disk('public')->exists($logbook->file_bukti)) {
            return Storage::disk('public')->download($logbook->file_bukti, $downloadName);
        }

        abort(404, 'Berkas bukti logbook tidak ditemukan.');
    }
}
