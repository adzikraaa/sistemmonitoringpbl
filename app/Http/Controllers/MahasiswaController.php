<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\RiwayatProposal;
use App\Models\Kelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    /**
     * Data kelompok dan user (hardcoded untuk prototype).
     * Saat backend lengkap, data ini diambil dari database/session.
     */
    private function sharedViewData(): array
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        return [
            'userName'  => $mahasiswa?->nama ?? 'Mahasiswa',
            'userNim'   => $mahasiswa?->nim ?? '-',
            'userKelas' => $mahasiswa?->kelas_id ?? '-',
            'userProdi' => 'Teknologi Informasi',
        ];
    }

    public function pilihSemester()
    {
        return view('mahasiswa.pilih-semester', $this->sharedViewData());
    }

    public function dashboard()
    {
        return view('mahasiswa.dashboard', $this->sharedViewData());
    }

    public function kelompok()
    {
        return view('mahasiswa.kelompok', $this->sharedViewData());
    }

    public function proposal()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $kelompok = $mahasiswa ? $mahasiswa->kelompok()->first() : null;
        $kelompokId = $kelompok ? $kelompok->id : (Kelompok::first()?->id ?? null);

        $dbProposal = $kelompokId ? Proposal::with('riwayatProposal')->where('kelompok_id', $kelompokId)->latest()->first() : null;

        if ($dbProposal) {
            $latestVersion = $dbProposal->riwayatProposal()->max('versi') ?? 1;
            $versiLabel = 'VERSI ' . str_pad((string) $latestVersion, 2, '0', STR_PAD_LEFT);

            $statusRaw = strtolower($dbProposal->status);
            $statusFormatted = match ($statusRaw) {
                'disetujui' => 'Disetujui',
                'ditolak'   => 'Ditolak',
                'revisi'    => 'Revisi',
                default     => 'Diajukan',
            };

            $timeline = [
                [
                    'event' => 'Draf & Proposal diajukan',
                    'date'  => $dbProposal->tanggal_pengajuan ? $dbProposal->tanggal_pengajuan->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                    'state' => 'done',
                ],
                [
                    'event' => 'Pemeriksaan oleh pembimbing',
                    'date'  => in_array($statusRaw, ['disetujui', 'ditolak', 'revisi']) ? 'Selesai' : 'Sedang ditinjau',
                    'state' => in_array($statusRaw, ['disetujui', 'ditolak', 'revisi']) ? 'done' : 'current',
                ],
                [
                    'event' => match ($statusRaw) {
                        'disetujui' => 'Proposal disetujui',
                        'ditolak'   => 'Proposal ditolak',
                        'revisi'    => 'Perlu revisi',
                        default     => 'Menunggu keputusan',
                    },
                    'date'  => in_array($statusRaw, ['disetujui', 'ditolak', 'revisi']) ? 'Tercatat' : 'Menunggu',
                    'state' => in_array($statusRaw, ['disetujui', 'ditolak', 'revisi']) ? 'done' : '',
                ],
            ];

            $proposalData = [
                'id'             => $dbProposal->id,
                'versi'          => $versiLabel,
                'judul'          => $dbProposal->judul,
                'deskripsi'      => $dbProposal->deskripsi,
                'latar_belakang' => $dbProposal->latar_belakang,
                'tujuan'         => $dbProposal->tujuan,
                'metode'         => $dbProposal->metode,
                'file_proposal'  => $dbProposal->file_proposal,
                'file_name'      => $dbProposal->file_proposal ? basename($dbProposal->file_proposal) : 'proposal-pbl.pdf',
                'file_url'       => $dbProposal->file_proposal ? route('mahasiswa.proposal.download', $dbProposal->id) : null,
                'status'         => $statusFormatted,
                'status_raw'     => $statusRaw,
                'tanggal'        => $dbProposal->tanggal_pengajuan ? $dbProposal->tanggal_pengajuan->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                'feedback'       => $dbProposal->catatan_revisi ?: 'Belum ada catatan atau revisi dari pembimbing.',
                'timeline'       => $timeline,
            ];
        } else {
            $proposalData = [
                'id'             => null,
                'versi'          => 'VERSI 01',
                'judul'          => 'Pengembangan Sistem Monitoring PBL',
                'deskripsi'      => 'Platform web untuk membantu mahasiswa, dosen, dan koordinator memantau progres Project Based Learning secara terstruktur.',
                'latar_belakang' => 'Proses monitoring saat ini masih tersebar di berbagai kanal sehingga histori dan progres sulit dipantau secara utuh.',
                'tujuan'         => 'Membangun satu sistem terpusat untuk proposal, logbook, milestone, dan monitoring progres project.',
                'metode'         => 'Agile dengan iterasi sprint dua mingguan dan validasi bersama dosen pembimbing.',
                'file_proposal'  => null,
                'file_name'      => 'proposal-kelompok-01.pdf',
                'file_url'       => null,
                'status'         => 'Disetujui',
                'status_raw'     => 'disetujui',
                'tanggal'        => '26 September 2026',
                'feedback'       => 'Struktur proposal sudah baik. Lanjutkan ke implementasi modul logbook.',
                'timeline'       => [
                    [
                        'event' => 'Draf disusun',
                        'date'  => '02 Mei 2024',
                        'state' => 'done',
                    ],
                    [
                        'event' => 'Dikirim untuk ditinjau',
                        'date'  => '02 Mei 2024',
                        'state' => 'done',
                    ],
                    [
                        'event' => 'Disetujui pembimbing',
                        'date'  => '06 Mei 2024',
                        'state' => 'done',
                    ],
                    [
                        'event' => 'Mulai pengerjaan',
                        'date'  => 'Berjalan',
                        'state' => 'current',
                    ],
                ],
            ];
        }

        return view('mahasiswa.proposal', array_merge(
            $this->sharedViewData(),
            ['proposal' => $proposalData]
        ));
    }

    public function logbook()
    {
        $logbooks = [
            [
                'id' => 'lb1',
                'tanggal' => '2026-04-28',
                'judul' => 'Analisis kebutuhan',
                'deskripsi' => 'Wawancara singkat dengan mahasiswa dan dosen pembimbing.',
                'progress' => 0,
                'hasil' => 'Daftar kebutuhan prioritas dan alur utama terdokumentasi.',
                'rencana' => 'Validasi hasil analisis dan susun backlog sprint.',
                'kendala' => 'Jadwal wawancara dengan koordinator berubah.',
                'status' => 'Disetujui',
                'feedback' => null,
            ],
            [
                'id' => 'lb2',
                'tanggal' => '2026-05-05',
                'judul' => 'Perancangan database',
                'deskripsi' => 'Menyusun relasi kelompok, proposal, logbook, milestone, dan penilaian.',
                'progress' => 0,
                'hasil' => 'ERD dan struktur tabel awal selesai.',
                'rencana' => 'Finalisasi schema dan mulai implementasi API.',
                'kendala' => 'Beberapa istilah perlu disepakati dengan koordinator.',
                'status' => 'Revisi',
                'feedback' => 'Tambahkan relasi histori aktivitas dan status publikasi nilai.',
            ],
            [
                'id' => 'lb3',
                'tanggal' => '2026-05-12',
                'judul' => 'Implementasi autentikasi',
                'deskripsi' => 'Menyelesaikan alur login dan pembagian akses berdasarkan role pengguna.',
                'progress' => 0,
                'hasil' => 'Alur login mahasiswa dan dosen sudah dapat diuji.',
                'rencana' => 'Merapikan validasi form dan melanjutkan modul proposal.',
                'kendala' => 'Penyesuaian middleware untuk role coordinator.',
                'status' => 'Disetujui',
                'feedback' => 'Dokumentasi cukup lengkap. Pastikan pesan error mudah dipahami pengguna.',
            ],
        ];

        // Hitung nomor minggu berdasarkan tanggal paling awal.
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
        ]));
    }

    public function notifikasi()
    {
        return view('mahasiswa.notifikasi', $this->sharedViewData());
    }

    /** Halaman daftar Milestone per Mata Kuliah */
    public function milestone()
    {
        $milestones = [
            [
                'id'    => 1,
                'kode'  => 'M1',
                'slug'  => 'pemrograman-web-lanjut',
                'judul' => 'Pemrograman Web Lanjut',
                'dosen' => 'Dian Hanifudin Subhi, S.Kom., M.Kom.',
                'progress' => 45,
                'status'   => 'Berjalan',
                'badge'    => 'sim-badge--warning',
                'tenggat'  => '30 Nov 2026',
                'deskripsi' => 'Implementasi modul utama sistem dan integrasi API backend.',
                'catatan'   => null,
                'deliverables' => [
                    ['nama' => 'Laporan Perencanaan Sprint', 'status' => 'Disetujui'],
                    ['nama' => 'ERD & Skema Database', 'status' => 'Disetujui'],
                    ['nama' => 'Implementasi Autentikasi', 'status' => 'Perlu Revisi'],
                    ['nama' => 'Modul Dashboard Mahasiswa', 'status' => 'Belum'],
                ],
            ],
            [
                'id'    => 2,
                'kode'  => 'M2',
                'slug'  => 'manajemen-proyek-sistem-informasi',
                'judul' => 'Manajemen Proyek Sistem Informasi',
                'dosen' => 'Ir. Rudy Ariyanto, S.T., M.Cs.',
                'progress' => 30,
                'status'   => 'Berjalan',
                'badge'    => 'sim-badge--warning',
                'tenggat'  => '15 Des 2026',
                'deskripsi' => 'Penyusunan WBS, Gantt Chart, dan dokumentasi manajemen proyek.',
                'catatan'   => 'Pastikan timeline realistis dan sesuai kapasitas tim.',
                'deliverables' => [
                    ['nama' => 'WBS & Gantt Chart', 'status' => 'Disetujui'],
                    ['nama' => 'Risk Register', 'status' => 'Belum'],
                    ['nama' => 'Sprint Review Report', 'status' => 'Belum'],
                ],
            ],
            [
                'id'    => 3,
                'kode'  => 'M3',
                'slug'  => 'penjaminan-mutu-perangkat-lunak',
                'judul' => 'Penjaminan Mutu Perangkat Lunak',
                'dosen' => 'Usman Nurhasan, S.Kom., M.T.',
                'progress' => 0,
                'status'   => 'Belum dimulai',
                'badge'    => 'sim-badge--default',
                'tenggat'  => '10 Jan 2027',
                'deskripsi' => 'Penyusunan test plan, test case, dan laporan hasil pengujian.',
                'catatan'   => null,
                'deliverables' => [],
            ],
        ];

        $mataKuliahList = array_map(function ($item) {
            return [
                'id'       => $item['id'],
                'kode'     => $item['kode'],
                'slug'     => $item['slug'],
                'nama'     => $item['judul'],
                'dosen'    => $item['dosen'],
                'progress' => $item['progress'],
                'status'   => $item['status'],
            ];
        }, $milestones);

        return view('mahasiswa.milestone', array_merge($this->sharedViewData(), [
            'milestones'     => $milestones,
            'mataKuliahList' => $mataKuliahList,
        ]));
    }

    /** Halaman Level 2: Detail Milestone per Mata Kuliah */
    public function milestoneDetail($id)
    {
        $slug = $id;

        $matkulNames = [
            'pemrograman-web-lanjut' => 'Pemrograman Web Lanjut',
            'manajemen-proyek-sistem-informasi' => 'Manajemen Proyek Sistem Informasi',
            'penjaminan-mutu-perangkat-lunak' => 'Penjaminan Mutu Perangkat Lunak',
        ];

        $namaMatkul = $matkulNames[$slug] ?? 'Pemrograman Web Lanjut';

        $anggotaKelompok = [
            ['nama' => 'Muhammad Adzikra', 'nim' => '244107060044', 'role' => 'Ketua', 'avatar' => 'MA'],
            ['nama' => 'Mochammad Tanggaq', 'nim' => '244107060126', 'role' => 'Anggota', 'avatar' => 'MT'],
            ['nama' => 'Niky Tania', 'nim' => '244107060053', 'role' => 'Anggota', 'avatar' => 'NT'],
            ['nama' => 'Dina Mei', 'nim' => '244107060105', 'role' => 'Anggota', 'avatar' => 'DM'],
            ['nama' => 'Fernanda Ayu', 'nim' => '244107060123', 'role' => 'Anggota', 'avatar' => 'FA'],
        ];

        $prefixMatkul = match ($slug) {
            'pemrograman-web-lanjut' => 'PWL',
            'manajemen-proyek-sistem-informasi' => 'MPSI',
            default => 'PMPL',
        };

        // Ambil riwayat tambahan dari session (simulasi sebelum DB disambungkan)
        $uploadedSession = session("uploaded_milestones_{$slug}", []);

        if ($slug === 'penjaminan-mutu-perangkat-lunak' && empty($uploadedSession)) {
            return view('mahasiswa.milestone-detail', array_merge($this->sharedViewData(), [
                'slug' => $slug,
                'namaMatkul' => $namaMatkul,
                'anggotaKelompok' => $anggotaKelompok,
                'berkasTerkumpul' => false,
                'milestones' => [],
            ]));
        }

        $defaultMilestones = ($slug === 'penjaminan-mutu-perangkat-lunak') ? [] : [
            [
                'id' => 2,
                'judul' => 'Milestone 2 – Laporan ' . $namaMatkul,
                'status' => 'Menunggu Review',
                'ringkasan' => 'Fokus pada arsitektur REST full API backend, implementasi otentikasi JWT, middleware Role-Based Access Control (RBAC), dan skema relasi database inventaris terintegrasi dengan ORM.',
                'waktu' => '18 Oktober 2026, 14:20 WIB',
                'pengunggah' => 'Muhammad Adzikra',
                'dokumen' => [
                    ['tipe' => 'pdf', 'nama' => 'Laporan-M2-' . $prefixMatkul . '-Kel02.pdf', 'sub' => 'Dokumen PDF • 3.8 MB', 'action' => 'download', 'url' => '#'],
                    ['tipe' => 'image', 'nama' => 'Skema-relasi-db-erd.png', 'sub' => 'Diagram ERD & Arsitektur • 1.6 MB', 'action' => 'download', 'url' => '#'],
                    ['tipe' => 'github', 'nama' => 'GitHub Repository (branch)', 'sub' => 'github.com/sim-pbl/inventaris', 'action' => 'open', 'url' => 'https://github.com/adzikraaa/sistemmonitoringpbl'],
                    ['tipe' => 'postman', 'nama' => 'Postman Collection Docs', 'sub' => 'api.sim-pbl.ac.id/docs', 'action' => 'open', 'url' => '#'],
                ],
            ],
            [
                'id' => 1,
                'judul' => 'Milestone 1 – Laporan ' . $namaMatkul,
                'status' => 'Disetujui',
                'ringkasan' => 'Penyusunan Work Breakdown Structure (WBS) Sprint 1–4, estimasi beban kerja tim (man-hour), Gantt Chart timeline rilis fitur, dan pemetaan alokasi resource anggota kelompok sesuai pembagian peran.',
                'waktu' => '18 September 2026, 15:05 WIB',
                'pengunggah' => 'Niky Tania',
                'dokumen' => [
                    ['tipe' => 'pdf', 'nama' => 'Laporan-M1-' . $prefixMatkul . '-Kel02.pdf', 'sub' => 'Dokumen PDF • 2.4 MB', 'action' => 'download', 'url' => '#'],
                    ['tipe' => 'docs', 'nama' => 'Pembagian Tugas PBL Kel 02', 'sub' => 'Lembar Kerja Analisis • 940 KB', 'action' => 'open', 'url' => '#'],
                    ['tipe' => 'notion', 'nama' => 'Kanban Sprint Backlog & Roadmapping', 'sub' => 'notion.so/pbl-kelompok01/sprint-m1', 'action' => 'open', 'url' => '#', 'full_width' => true],
                ],
            ],
        ];

        $milestones = array_merge($uploadedSession, $defaultMilestones);

        return view('mahasiswa.milestone-detail', array_merge($this->sharedViewData(), [
            'slug' => $slug,
            'namaMatkul' => $namaMatkul,
            'anggotaKelompok' => $anggotaKelompok,
            'berkasTerkumpul' => !empty($milestones),
            'milestones' => $milestones,
        ]));
    }

    /** Menyimpan pengumpulan milestone & berkas pendukung (Pertemuan 6: Validasi & File Storage) */
    public function storeMilestoneUpload(Request $request, $id)
    {
        $validated = $request->validate([
            'mata_kuliah'  => ['required', 'string'],
            'milestone_id' => ['required', 'in:1,2,3'],
            'catatan'      => ['required', 'string', 'max:2000'],
            'berkas'       => ['required', 'file', 'mimes:pdf,zip', 'max:20480'], // Maks 20 MB
            'link_bukti'   => ['nullable', 'url', 'max:255'],
        ], [
            'milestone_id.required' => 'Tahapan milestone wajib dipilih.',
            'catatan.required'      => 'Ringkasan capaian milestone wajib diisi.',
            'berkas.required'       => 'Berkas laporan (PDF/ZIP) wajib diunggah.',
            'berkas.mimes'          => 'Format berkas harus berupa PDF atau ZIP.',
            'berkas.max'            => 'Ukuran berkas maksimal 20 MB.',
            'link_bukti.url'        => 'Format tautan repositori/demo tidak valid (harus diawali http:// atau https://).',
        ]);

        $slug = $validated['mata_kuliah'] ?: $id;

        $matkulNames = [
            'pemrograman-web-lanjut' => 'Pemrograman Web Lanjut',
            'manajemen-proyek-sistem-informasi' => 'Manajemen Proyek Sistem Informasi',
            'penjaminan-mutu-perangkat-lunak' => 'Penjaminan Mutu Perangkat Lunak',
        ];
        $namaMatkul = $matkulNames[$slug] ?? 'Mata Kuliah PBL';

        $tahapanLabels = [
            '1' => 'Milestone 1',
            '2' => 'Milestone 2',
            '3' => 'Milestone 3',
        ];

        // Simpan file fisik ke storage/app/public/milestones
        $file = $request->file('berkas');
        $originalName = $file->getClientOriginalName();
        $sizeInMb = number_format($file->getSize() / (1024 * 1024), 2) . ' MB';
        $storedPath = $file->store('milestones/' . $slug, 'public');
        $fileUrl = asset('storage/' . $storedPath);

        $dokumenList = [
            [
                'tipe'   => strtolower($file->getClientOriginalExtension()) === 'pdf' ? 'pdf' : 'zip',
                'nama'   => $originalName,
                'sub'    => 'Dokumen ' . strtoupper($file->getClientOriginalExtension()) . ' • ' . $sizeInMb,
                'action' => 'download',
                'url'    => $fileUrl,
            ],
        ];

        if (!empty($validated['link_bukti'])) {
            $dokumenList[] = [
                'tipe'   => 'github',
                'nama'   => 'Tautan Repositori / Demo',
                'sub'    => preg_replace('#^https?://#', '', $validated['link_bukti']),
                'action' => 'open',
                'url'    => $validated['link_bukti'],
            ];
        }

        $mahasiswa = Auth::guard('mahasiswa')->user();
        $pengunggah = $mahasiswa?->nama ?? 'Niky Tania';

        // Simpan entri baru ke session agar langsung muncul di halaman detail
        $existing = session("uploaded_milestones_{$slug}", []);
        array_unshift($existing, [
            'id'         => (int) $validated['milestone_id'],
            'judul'      => ($tahapanLabels[$validated['milestone_id']] ?? 'Milestone') . ' – Laporan ' . $namaMatkul,
            'status'     => 'Menunggu Review',
            'ringkasan'  => $validated['catatan'],
            'waktu'      => now()->translatedFormat('d F Y, H:i') . ' WIB',
            'pengunggah' => $pengunggah,
            'dokumen'    => $dokumenList,
        ]);
        session(["uploaded_milestones_{$slug}" => $existing]);

        return redirect()
            ->route('mahasiswa.milestone.detail', $slug)
            ->with('success', 'Laporan milestone berhasil dikumpulkan dan menunggu review dosen!');
    }

    /**
     * Menangani pengajuan proposal proyek PBL beserta unggah berkas PDF ke database & storage.
     */
    public function storeProposal(Request $request)
    {
        $validated = $request->validate([
            'judul'          => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'latar_belakang' => 'required|string',
            'tujuan'         => 'required|string',
            'metode'         => 'required|string',
            'file_proposal'  => 'required|file|mimes:pdf|max:20480', // Maks 20 MB PDF
        ], [
            'judul.required'          => 'Judul proyek wajib diisi.',
            'deskripsi.required'      => 'Deskripsi singkat wajib diisi.',
            'latar_belakang.required' => 'Latar belakang wajib diisi.',
            'tujuan.required'         => 'Tujuan wajib diisi.',
            'metode.required'         => 'Metode kerja wajib diisi.',
            'file_proposal.required'  => 'Berkas proposal (PDF) wajib diunggah.',
            'file_proposal.mimes'     => 'Format berkas harus berupa PDF.',
            'file_proposal.max'       => 'Ukuran berkas proposal maksimal 20 MB.',
        ]);

        $file = $request->file('file_proposal');
        $originalName = $file->getClientOriginalName();
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $filePath = $file->storeAs('proposals', $safeName, 'public');

        $mahasiswa = Auth::guard('mahasiswa')->user();
        $kelompok = $mahasiswa ? $mahasiswa->kelompok()->first() : null;
        $kelompokId = $kelompok ? $kelompok->id : (Kelompok::first()?->id ?? 1);

        $existingProposal = Proposal::where('kelompok_id', $kelompokId)->latest()->first();

        if ($existingProposal) {
            $nextVersion = ($existingProposal->riwayatProposal()->max('versi') ?? 1) + 1;

            $existingProposal->update([
                'judul'             => $validated['judul'],
                'deskripsi'         => $validated['deskripsi'],
                'latar_belakang'    => $validated['latar_belakang'],
                'tujuan'            => $validated['tujuan'],
                'metode'            => $validated['metode'],
                'file_proposal'     => $filePath,
                'status'            => 'diajukan',
                'catatan_revisi'    => null,
                'tanggal_pengajuan' => now()->toDateString(),
            ]);

            RiwayatProposal::create([
                'proposal_id'   => $existingProposal->id,
                'versi'         => $nextVersion,
                'judul'         => $validated['judul'],
                'file_proposal' => $filePath,
                'status'        => 'diajukan',
                'catatan'       => 'Pengajuan revisi proposal (Versi ' . $nextVersion . ')',
            ]);
        } else {
            $newProposal = Proposal::create([
                'kelompok_id'       => $kelompokId,
                'judul'             => $validated['judul'],
                'deskripsi'         => $validated['deskripsi'],
                'latar_belakang'    => $validated['latar_belakang'],
                'tujuan'            => $validated['tujuan'],
                'metode'            => $validated['metode'],
                'file_proposal'     => $filePath,
                'status'            => 'diajukan',
                'catatan_revisi'    => null,
                'tanggal_pengajuan' => now()->toDateString(),
            ]);

            RiwayatProposal::create([
                'proposal_id'   => $newProposal->id,
                'versi'         => 1,
                'judul'         => $validated['judul'],
                'file_proposal' => $filePath,
                'status'        => 'diajukan',
                'catatan'       => 'Pengajuan proposal awal (Versi 01)',
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proposal berhasil diajukan.',
                'status'  => 'diajukan',
            ]);
        }

        return redirect()->route('mahasiswa.proposal')->with('success', 'Proposal berhasil diajukan dan sedang menunggu review dosen pembimbing!');
    }

    /**
     * Mengunduh berkas PDF proposal yang tersimpan di storage.
     */
    public function downloadProposal($id = null)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $kelompok = $mahasiswa ? $mahasiswa->kelompok()->first() : null;

        $proposal = $id
            ? Proposal::findOrFail($id)
            : ($kelompok ? Proposal::where('kelompok_id', $kelompok->id)->latest()->first() : Proposal::latest()->first());

        if (!$proposal || !$proposal->file_proposal || !Storage::disk('public')->exists($proposal->file_proposal)) {
            abort(404, 'Berkas proposal tidak ditemukan.');
        }

        return Storage::disk('public')->download($proposal->file_proposal, basename($proposal->file_proposal));
    }

    /**
     * Menangani penambahan catatan logbook (backend stub).
     * Menerima data dari form modal tambah logbook dan mengembalikan JSON.
     */
    public function storeLogbook(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'progress' => 'required|integer|min:0|max:100',
            'hasil' => 'nullable|string',
            'kendala' => 'nullable|string',
            'rencana' => 'nullable|string',
        ]);

        // TODO: Simpan ke database setelah tabel logbook_milestone tersedia.
        // LogbookMilestone::create([...$validated, 'mahasiswa_id' => auth('mahasiswa')->id()]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan logbook berhasil ditambahkan.',
            'data' => $validated,
        ]);
    }
}
