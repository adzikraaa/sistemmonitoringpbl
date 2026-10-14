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
        return view('mahasiswa.proposal', $this->sharedViewData());
    }

    public function logbook()
    {
        return view('mahasiswa.logbook', $this->sharedViewData());
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
                    ['nama' => 'Draf Proposal Proyek', 'status' => 'Disetujui'],
                    ['nama' => 'Struktur WBS & Timeline Proyek', 'status' => 'Disetujui'],
                    ['nama' => 'Pembagian Peran Anggota Tim', 'status' => 'Disetujui'],
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
                'progress'    => 75,
                'catatan'     => 'Modul dashboard dan skema ERD sudah terarah baik. Perbaiki kardinalitas relasi pada tabel evaluasi.',
                'deliverables' => [
                    ['nama' => 'Prototype High-Fidelity UI/UX Figma', 'status' => 'Disetujui'],
                    ['nama' => 'Diagram Use Case & Flowchart Sistem', 'status' => 'Disetujui'],
                    ['nama' => 'ERD & Kamus Data Fisik (PostgreSQL)', 'status' => 'Perlu Revisi'],
                    ['nama' => 'Dokumen Spesifikasi Kebutuhan (SRS)', 'status' => 'Progres 60%'],
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
                    ['nama' => 'Modul Ruang Mahasiswa (Proposal & Milestone)', 'status' => 'Belum dimulai'],
                    ['nama' => 'Modul Ruang Dosen (Review & Assessment)', 'status' => 'Belum dimulai'],
                    ['nama' => 'Autentikasi & Middleware Multi-Role', 'status' => 'Belum dimulai'],
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
                    ['nama' => 'Dokumen Test Case & Test Plan (PMPL)', 'status' => 'Belum dimulai'],
                    ['nama' => 'Laporan Eksekusi Pengujian & Bug Fixing', 'status' => 'Belum dimulai']
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
                    ['nama' => 'Laporan Akhir Proyek PBL (PDF)', 'status' => 'Belum dimulai'],
                    ['nama' => 'Video Demo & Poster Produk', 'status' => 'Belum dimulai'],
                    ['nama' => 'Peer Assessment & Kelayakan Sidang', 'status' => 'Belum dimulai'],
                ]
            ],
        ];
        return view('mahasiswa.milestone', array_merge($this->sharedViewData(), [
            'milestones' => $milestones
        ]));
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
        ]);

        // TODO: simpan ke database setelah tabel proposals tersedia
        // Proposal::create([...$validated, 'kelompok_id' => ..., 'status' => 'diajukan']);

        return response()->json([
            'success' => true,
            'message' => 'Proposal berhasil diajukan.',
            'status'  => 'diajukan',
        ]);
    }
}
