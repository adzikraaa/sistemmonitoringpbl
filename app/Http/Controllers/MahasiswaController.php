<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('mahasiswa.proposal', array_merge(
            $this->sharedViewData(),
            ['proposal' => $proposal]
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

    /** Halaman Level 2: Detail Milestone per Mata Kuliah */
public function milestone()
    {
        $mataKuliahList = [
            [
                'id' => 1,
                'kode' => 'MK1',
                'slug' => 'pemrograman-web-lanjut',
                'nama' => 'Pemrograman Web Lanjut',
                'dosen' => 'Dian Hanifudin Subhi, S.Kom., M.Kom.',
                'progress' => 45,
                'status' => 'Berjalan',
                'badge_color' => 'bg-emerald-50 text-emerald-600 border-emerald-200'
            ],
            [
                'id' => 2,
                'kode' => 'MK2',
                'slug' => 'manajemen-proyek-sistem-informasi',
                'nama' => 'Manajemen Proyek Sistem Informasi',
                'dosen' => 'Ir. Rudy Ariyanto, S.T., M.Cs.',
                'progress' => 30,
                'status' => 'Berjalan',
                'badge_color' => 'bg-emerald-50 text-emerald-600 border-emerald-200'
            ],
            [
                'id' => 3,
                'kode' => 'MK3',
                'slug' => 'penjaminan-mutu-perangkat-lunak',
                'nama' => 'Penjaminan Mutu Perangkat Lunak',
                'dosen' => 'Usman Nurhasan, S.Kom., M.T.',
                'progress' => 0,
                'status' => 'Belum dimulai',
                'badge_color' => 'bg-gray-100 text-gray-500 border-gray-200'
            ],
        ];

        return view('mahasiswa.milestone', array_merge($this->sharedViewData(), [
            'mataKuliahList' => $mataKuliahList
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

        if ($slug === 'penjaminan-mutu-perangkat-lunak') {
            return view('mahasiswa.milestone-detail', array_merge($this->sharedViewData(), [
                'slug' => $slug,
                'namaMatkul' => $namaMatkul,
                'anggotaKelompok' => $anggotaKelompok,
                'berkasTerkumpul' => false,
                'milestones' => []
            ]));
        }

        $prefixMatkul = ($slug === 'pemrograman-web-lanjut') ? 'PWL' : 'MPSI';

        $milestones = [
            [
                'id' => 2,
                'judul' => 'Milestone 2 – Laporan ' . $namaMatkul,
                'status' => 'Menunggu Review',
                'ringkasan' => 'Fokus pada arsitektur REST full API backend, implementasi otentikasi JWT, middleware Role-Based Access Control (RBAC), dan skema relasi database inventaris terintegrasi dengan ORM Prisma.',
                'waktu' => '18 Oktober 2026, 14:20 WIB',
                'pengunggah' => 'Muhammad Adzikra',
                'dokumen' => [
                    ['tipe' => 'pdf', 'nama' => 'Laporan-M2-' . $prefixMatkul . '-Kel02.pdf', 'sub' => 'Dokumen PDF • 3.8 MB', 'action' => 'download'],
                    ['tipe' => 'image', 'nama' => 'Skema-relasi-db-erd.png', 'sub' => 'Diagram ERD & Arsitektur • 1.6 MB', 'action' => 'download'],
                    ['tipe' => 'github', 'nama' => 'GitHub Repository (branch)', 'sub' => 'github.com/sim-pbl/inventaris', 'action' => 'open'],
                    ['tipe' => 'postman', 'nama' => 'Postman Collection Docs', 'sub' => 'api.sim-pbl.ac.id/docs', 'action' => 'open'],
                ]
            ],
            [
                'id' => 1,
                'judul' => 'Milestone 1 – Laporan ' . $namaMatkul,
                'status' => 'Disetujui',
                'ringkasan' => 'Penyusunan Work Breakdown Structure (WBS) Sprint 1–4, estimasi beban kerja tim (man-hour), Gantt Chart timeline rilis fitur, dan pemetaan alokasi resource anggota kelompok sesuai pembagian peran.',
                'waktu' => '18 September 2026, 15:05 WIB',
                'pengunggah' => 'Niky Tania',
                'dokumen' => [
                    ['tipe' => 'pdf', 'nama' => 'Laporan-M1-' . $prefixMatkul . '-Kel02.pdf', 'sub' => 'Dokumen PDF • 2.4 MB', 'action' => 'download'],
                    ['tipe' => 'docs', 'nama' => 'Pembagian Tugas PBL Kel 02', 'sub' => 'Lembar Kerja Analisis • 940 KB', 'action' => 'open'],
                    ['tipe' => 'notion', 'nama' => 'Kanban Sprint Backlog & Roadmapping', 'sub' => 'notion.so/pbl-kelompok01/sprint-m1', 'action' => 'open', 'full_width' => true],
                ]
            ]
        ];

        return view('mahasiswa.milestone-detail', array_merge($this->sharedViewData(), [
            'slug' => $slug,
            'namaMatkul' => $namaMatkul,
            'anggotaKelompok' => $anggotaKelompok,
            'berkasTerkumpul' => true,
            'milestones' => $milestones
        ]));
    }

    /** Menyimpan upload berkas deliverable. */
    public function storeMilestoneUpload(Request $request, $id)
    {
        $request->validate([
            'berkas' => 'required|file|mimes:pdf,zip|max:20480',
            'item_id' => 'required|string',
        ]);

        return redirect()
            ->route('mahasiswa.milestone')
            ->with('success', 'Berkas deliverable berhasil dikirim!');
    }

    /**
     * Menangani pengajuan proposal (backend stub).
     * Endpoint ini menerima data dari form modal dan mengembalikan JSON.
     */
    public function storeProposal(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'latar_belakang' => 'required|string',
            'tujuan' => 'required|string',
            'metode' => 'required|string',
        ]);

        // TODO: Simpan ke database setelah tabel proposals tersedia.
        // Proposal::create([...$validated, 'kelompok_id' => ..., 'status' => 'diajukan']);

        return response()->json([
            'success' => true,
            'message' => 'Proposal berhasil diajukan.',
            'status' => 'diajukan',
        ]);
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