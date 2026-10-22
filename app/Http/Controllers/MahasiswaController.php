<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Data kelompok & user (hardcoded untuk prototype).
     * Saat backend lengkap, data ini diambil dari DB/session.
     */
    private function sharedViewData(): array
    {
        return [
            'userName'  => 'Mochammad Tanggaq',
            'userNim'   => '244107060105',
            'userKelas' => 'SIB 3B',
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
        $proposal = [
            'judul' => 'Pengembangan Sistem Monitoring PBL',
            'status' => 'Disetujui',
            'tanggal' => '26 September 2026',
            'feedback' => 'Struktur proposal sudah baik. Lanjutkan ke implementasi modul logbook.',
            'timeline' => [
                [
                    'event' => 'Draf disusun',
                    'date' => '02 Mei 2024',
                    'state' => 'done',
                ],
                [
                    'event' => 'Dikirim untuk ditinjau',
                    'date' => '02 Mei 2024',
                    'state' => 'done',
                ],
                [
                    'event' => 'Disetujui pembimbing',
                    'date' => '06 Mei 2024',
                    'state' => 'done',
                ],
                [
                    'event' => 'Mulai pengerjaan',
                    'date' => 'Berjalan',
                    'state' => 'current',
                ],
            ],
        ];

        return view('mahasiswa.proposal', array_merge($this->sharedViewData(), ['proposal' => $proposal]));
    }

    public function logbook()
    {
        $logbooks = [
            [
                'id'       => 'lb1',
                'tanggal'  => '2026-04-28',
                'judul'    => 'Analisis kebutuhan',
                'deskripsi'=> 'Wawancara singkat dengan mahasiswa dan dosen pembimbing.',
                'progress' => 0,
                'hasil'    => 'Daftar kebutuhan prioritas dan alur utama terdokumentasi.',
                'rencana'  => 'Validasi hasil analisis dan susun backlog sprint.',
                'kendala'  => 'Jadwal wawancara dengan koordinator berubah.',
                'status'   => 'Disetujui',
                'feedback' => null,
            ],
            [
                'id'       => 'lb2',
                'tanggal'  => '2026-05-05',
                'judul'    => 'Perancangan database',
                'deskripsi'=> 'Menyusun relasi kelompok, proposal, logbook, milestone, dan penilaian.',
                'progress' => 0,
                'hasil'    => 'ERD dan struktur tabel awal selesai.',
                'rencana'  => 'Finalisasi schema dan mulai implementasi API.',
                'kendala'  => 'Beberapa istilah perlu disepakati dengan koordinator.',
                'status'   => 'Revisi',
                'feedback' => 'Tambahkan relasi histori aktivitas dan status publikasi nilai.',
            ],
            [
                'id'       => 'lb3',
                'tanggal'  => '2026-05-12',
                'judul'    => 'Implementasi autentikasi',
                'deskripsi'=> 'Menyelesaikan alur login dan pembagian akses berdasarkan role pengguna.',
                'progress' => 0,
                'hasil'    => 'Alur login mahasiswa dan dosen sudah dapat diuji.',
                'rencana'  => 'Merapikan validasi form dan melanjutkan modul proposal.',
                'kendala'  => 'Penyesuaian middleware untuk role coordinator.',
                'status'   => 'Disetujui',
                'feedback' => 'Dokumentasi cukup lengkap. Pastikan pesan error mudah dipahami pengguna.',
            ],
        ];

        // Hitung nomor minggu berdasarkan tanggal paling awal
        $earliest = null;
        foreach ($logbooks as $lb) {
            $ts = strtotime($lb['tanggal']);
            if ($earliest === null || $ts < $earliest) {
                $earliest = $ts;
            }
        }
        foreach ($logbooks as $i => $lb) {
            $diffDays = ($earliest !== null)
                ? (strtotime($lb['tanggal']) - $earliest) / 86400
                : 0;
            $logbooks[$i]['minggu'] = (int) floor($diffDays / 7) + 1;
        }

        return view('mahasiswa.logbook', array_merge($this->sharedViewData(), [
            'logbooks'        => $logbooks,
            'earliestTimestamp' => $earliest ?? time(),
        ]));
    }

    public function milestone()
    {
        $milestones = [
            [
                'id'          => 'm1',
                'kode'        => 'M1',
                'judul'       => 'Inisiasi Proyek & Proposal',
                'status'      => 'Selesai',
                'badge'       => 'sim-badge--approved',
                'tenggat'     => '25 Apr 2026',
                'deskripsi'   => 'Pengajuan proposal, pemodelan lingkup masalah, dan persetujuan dosen pembimbing.',
                'progress'    => 100,
                'catatan'     => 'Proposal dan rumusan masalah disetujui dosen. Lanjutkan ke pemodelan UML dan wireframe.',
                'deliverables' => [
                    [
                        'id' => 'd1', 'nama' => 'Draf Proposal Proyek', 'status' => 'Disetujui',
                        'file_name' => 'Draft_Proposal_PBL_v2.pdf', 'file_url' => '#', 'catatan' => 'Format dan isi sudah sesuai.'
                    ],
                    [
                        'id' => 'd2', 'nama' => 'Struktur WBS & Timeline Proyek', 'status' => 'Disetujui',
                        'file_name' => 'WBS_Timeline_Kelompok01.pdf', 'file_url' => '#', 'catatan' => 'Timeline realistis.'
                    ],
                    [
                        'id' => 'd3', 'nama' => 'Pembagian Peran Anggota Tim', 'status' => 'Disetujui',
                        'file_name' => 'Pembagian_Peran_Tim.pdf', 'file_url' => '#', 'catatan' => ''
                    ],
                ]
            ],
            [
                'id'          => 'm2',
                'kode'        => 'M2',
                'judul'       => 'Desain Arsitektur & Basis Data',
                'status'      => 'Berjalan',
                'badge'       => 'sim-badge--pending',
                'tenggat'     => '30 Mei 2026',
                'deskripsi'   => 'Perencanaan UI/UX Figma, skema relasi database (ERD), Use Case, dan inisiasi modul backend.',
                'progress'    => 50,
                'catatan'     => 'Modul dashboard dan skema ERD sudah terarah baik. Perbaiki kardinalitas relasi pada tabel evaluasi.',
                'deliverables' => [
                    [
                        'id' => 'd4', 'nama' => 'Prototype High-Fidelity UI/UX Figma', 'status' => 'Disetujui',
                        'file_name' => 'Figma_Link_Export_v1.pdf', 'file_url' => '#', 'catatan' => 'Desain rapi dan konsisten.'
                    ],
                    [
                        'id' => 'd5', 'nama' => 'Diagram Use Case & Flowchart Sistem', 'status' => 'Disetujui',
                        'file_name' => 'Diagram_UseCase_Flowchart.pdf', 'file_url' => '#', 'catatan' => 'Aktor dan alur sesuai.'
                    ],
                    [
                        'id' => 'd6', 'nama' => 'ERD & Kamus Data Fisik (PostgreSQL)', 'status' => 'Perlu Revisi',
                        'file_name' => 'ERD_Physical_Model_v1.pdf', 'file_url' => '#', 'catatan' => 'Kardinalitas relasi antara tabel mahasiswa dan evaluasi perlu diperbaiki.'
                    ],
                    [
                        'id' => 'd7', 'nama' => 'Dokumen Spesifikasi Kebutuhan (SRS)', 'status' => 'Belum Dimulai',
                        'file_name' => '', 'file_url' => '', 'catatan' => 'Silakan unggah draf dokumen SRS awal.'
                    ],
                ]
            ],
            [
                'id'          => 'm3',
                'kode'        => 'M3',
                'judul'       => 'Implementasi Modul & Fitur Inti',
                'status'      => 'Belum dimulai',
                'badge'       => 'sim-badge--draft',
                'tenggat'     => '10 Jun 2026',
                'deskripsi'   => 'Pengembangan controller, Blade views, middleware RBAC, dan integrasi fitur monitoring.',
                'progress'    => 0,
                'catatan'     => '',
                'deliverables' => [
                    ['id' => 'd8', 'nama' => 'Modul Ruang Mahasiswa (Proposal & Milestone)', 'status' => 'Belum Dimulai', 'file_name' => '', 'file_url' => '', 'catatan' => ''],
                    ['id' => 'd9', 'nama' => 'Modul Ruang Dosen (Review & Assessment)', 'status' => 'Belum Dimulai', 'file_name' => '', 'file_url' => '', 'catatan' => ''],
                    ['id' => 'd10', 'nama' => 'Autentikasi & Middleware Multi-Role', 'status' => 'Belum Dimulai', 'file_name' => '', 'file_url' => '', 'catatan' => ''],
                ]
            ],
            [
                'id'          => 'm4',
                'kode'        => 'M4',
                'judul'       => 'Pengujian Sistem & Quality Assurance',
                'status'      => 'Belum dimulai',
                'badge'       => 'sim-badge--draft',
                'tenggat'     => '18 Jun 2026',
                'deskripsi'   => 'Eksekusi pengujian fungsional (Blackbox Testing), pencatatan bug report, dan usability testing.',
                'progress'    => 0,
                'catatan'     => '',
                'deliverables' => [
                    ['id' => 'd11', 'nama' => 'Dokumen Test Case & Test Plan (PMPL)', 'status' => 'Belum Dimulai', 'file_name' => '', 'file_url' => '', 'catatan' => ''],
                    ['id' => 'd12', 'nama' => 'Laporan Eksekusi Pengujian & Bug Fixing', 'status' => 'Belum Dimulai', 'file_name' => '', 'file_url' => '', 'catatan' => '']
                ]
            ],
            [
                'id'          => 'm5',
                'kode'        => 'M5',
                'judul'       => 'Laporan Akhir & Gelar Karya (Expo)',
                'status'      => 'Belum dimulai',
                'badge'       => 'sim-badge--draft',
                'tenggat'     => '28 Jun 2026',
                'deskripsi'   => 'Penyusunan naskah laporan Bab 1-5, poster pameran, video demo produk, dan pendaftaran sidang.',
                'progress'    => 0,
                'catatan'     => '',
                'deliverables' => [
                    ['id' => 'd13', 'nama' => 'Laporan Akhir Proyek PBL (PDF)', 'status' => 'Belum Dimulai', 'file_name' => '', 'file_url' => '', 'catatan' => ''],
                    ['id' => 'd14', 'nama' => 'Video Demo & Poster Produk', 'status' => 'Belum Dimulai', 'file_name' => '', 'file_url' => '', 'catatan' => ''],
                    ['id' => 'd15', 'nama' => 'Peer Assessment & Kelayakan Sidang', 'status' => 'Belum Dimulai', 'file_name' => '', 'file_url' => '', 'catatan' => ''],
                ]
            ],
        ];
        return view('mahasiswa.milestone', array_merge($this->sharedViewData(), [
            'milestones' => $milestones
        ]));
    }

    //method untuk menampilkan detail milestone pengumpulan bukti kerja
    public function milestoneDetail($id)
    {
        // Ambil data milestone (stub dummy)
        $milestones = [
            'm1' => [
                'id' => 'm1', 'kode' => 'M1', 'judul' => 'Inisiasi Proyek & Proposal',
                'status' => 'Selesai', 'badge' => 'sim-badge--approved', 'tenggat' => '25 Apr 2026',
                'deskripsi' => 'Pengajuan proposal, pemodelan lingkup masalah, dan persetujuan dosen pembimbing.',
                'catatan' => 'Proposal dan rumusan masalah disetujui dosen. Lanjutkan ke pemodelan UML dan wireframe.'
            ],
            'm2' => [
                'id' => 'm2', 'kode' => 'M2', 'judul' => 'Implementasi',
                'status' => 'Berjalan', 'badge' => 'sim-badge--pending', 'tenggat' => '30 Mei 2026',
                'deskripsi' => 'Pembangunan modul inti aplikasi.',
                'catatan' => 'Modul dashboard dan proposal sedang diselesaikan.'
            ],
        ];

        $milestone = $milestones[$id] ?? $milestones['m2'];

        return view('mahasiswa.milestone-detail', array_merge($this->sharedViewData(), [
            'milestone' => $milestone
        ]));
    }

   // Method Simpan Upload Berkas Deliverable
    public function storeMilestoneUpload(Request $request, $id)
    {
        $request->validate([
            'berkas' => 'required|file|mimes:pdf,zip|max:20480', // Maks 20MB
            'item_id' => 'required|string',
        ]);

        // TODO: Simpan ke DB / Storage berdasarkan item_id
        // $path = $request->file('berkas')->store('deliverables');

        return redirect()->route('mahasiswa.milestone')->with('success', 'Berkas deliverable berhasil dikirim!');
    }

    /**
      * Notifikasi.
      */
    public function notifikasi()
    {
        return view('mahasiswa.notifikasi', $this->sharedViewData());
    }

    /**
     * Store Proposal (backend stub).
     *
     * Endpoint ini siap menerima data dari form modal.
     * Saat ini mengembalikan JSON sukses untuk modal JS.
     * Implementasi penyimpanan ke DB dapat ditambahkan
     * setelah migrasi proposal table tersedia.
     */
    public function storeProposal(Request $request)
    {
        $validated = $request->validate([
            'judul'          => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'latar_belakang' => 'required|string',
            'tujuan'         => 'required|string',
            'metode'         => 'required|string',
            'file_proposal'  => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // file_proposal disimpan ke storage jika ada
        $filePath = null;
        if ($request->hasFile('file_proposal')) {
            $filePath = $request->file('file_proposal')->store('proposals', 'public');
        }

        // TODO: simpan ke database setelah kelompok_id tersedia dari session
        // Proposal::create([
        //     ...$validated,
        //     'file_proposal'    => $filePath,
        //     'status'           => 'diajukan',
        //     'tanggal_pengajuan'=> now()->toDateString(),
        //     'kelompok_id'      => auth('mahasiswa')->user()->kelompok?->id,
        // ]);

        return redirect()->route('mahasiswa.proposal')
            ->with('success', 'Proposal berhasil diajukan!');
    }

    /**
     * Store Logbook Entry (backend stub).
     *
     * Menerima data dari form modal tambah logbook.
     * Mengembalikan JSON sukses untuk diproses oleh JS.
     * Implementasi penyimpanan ke DB dapat ditambahkan
     * setelah tabel logbook tersedia.
     */
    public function storeLogbook(Request $request)
    {
        $validated = $request->validate([
            'tanggal'    => 'required|date',
            'kegiatan'   => 'required|string',
            'kendala'    => 'nullable|string',
            'link_bukti' => 'nullable|url|max:255',
        ], [
            'kegiatan.required' => 'Deskripsi kegiatan wajib diisi.',
            'link_bukti.url'    => 'Link bukti harus berupa URL yang valid.',
        ]);

        // TODO: simpan ke database
        // LogbookMilestone::create([
        //     ...$validated,
        //     'mahasiswa_id' => auth('mahasiswa')->id(),
        //     'kelompok_id'  => auth('mahasiswa')->user()->anggotaKelompok?->kelompok_id,
        //     'milestone_id' => ..., // dari kelompok aktif
        //     'status'       => 'diajukan',
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan logbook berhasil ditambahkan.',
            'data'    => $validated,
        ]);
    }
}
