<?php

namespace Database\Seeders;

use App\Models\Deliverable;
use App\Models\Milestone;
use App\Models\Periode;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $periode = Periode::where('is_aktif', true)->firstOrFail();

        // Data milestone dan deliverable disalin langsung dari MahasiswaController::milestone()
        $milestones = [
            [
                'kode'    => 'M1',
                'judul'   => 'Inisiasi Proyek & Proposal',
                'deskripsi' => 'Pengajuan proposal, pemodelan lingkup masalah, dan persetujuan dosen pembimbing.',
                'urutan'  => 1,
                'deadline' => '2026-04-25',
                'deliverables' => [
                    ['nama' => 'Draf Proposal Proyek', 'urutan' => 1],
                    ['nama' => 'Struktur WBS & Timeline Proyek', 'urutan' => 2],
                    ['nama' => 'Pembagian Peran Anggota Tim', 'urutan' => 3],
                ],
            ],
            [
                'kode'    => 'M2',
                'judul'   => 'Desain Arsitektur & Basis Data',
                'deskripsi' => 'Perencanaan UI/UX Figma, skema relasi database (ERD), Use Case, dan inisiasi modul backend.',
                'urutan'  => 2,
                'deadline' => '2026-05-30',
                'deliverables' => [
                    ['nama' => 'Prototype High-Fidelity UI/UX Figma', 'urutan' => 1],
                    ['nama' => 'Diagram Use Case & Flowchart Sistem', 'urutan' => 2],
                    ['nama' => 'ERD & Kamus Data Fisik (PostgreSQL)', 'urutan' => 3],
                    ['nama' => 'Dokumen Spesifikasi Kebutuhan (SRS)', 'urutan' => 4],
                ],
            ],
            [
                'kode'    => 'M3',
                'judul'   => 'Implementasi Modul & Fitur Inti',
                'deskripsi' => 'Pengembangan controller, Blade views, middleware RBAC, dan integrasi fitur monitoring.',
                'urutan'  => 3,
                'deadline' => '2026-06-10',
                'deliverables' => [
                    ['nama' => 'Modul Ruang Mahasiswa (Proposal & Milestone)', 'urutan' => 1],
                    ['nama' => 'Modul Ruang Dosen (Review & Assessment)', 'urutan' => 2],
                    ['nama' => 'Autentikasi & Middleware Multi-Role', 'urutan' => 3],
                ],
            ],
            [
                'kode'    => 'M4',
                'judul'   => 'Pengujian Sistem & Quality Assurance',
                'deskripsi' => 'Eksekusi pengujian fungsional (Blackbox Testing), pencatatan bug report, dan usability testing.',
                'urutan'  => 4,
                'deadline' => '2026-06-18',
                'deliverables' => [
                    ['nama' => 'Dokumen Test Case & Test Plan (PMPL)', 'urutan' => 1],
                    ['nama' => 'Laporan Eksekusi Pengujian & Bug Fixing', 'urutan' => 2],
                ],
            ],
            [
                'kode'    => 'M5',
                'judul'   => 'Laporan Akhir & Gelar Karya (Expo)',
                'deskripsi' => 'Penyusunan naskah laporan Bab 1-5, poster pameran, video demo produk, dan pendaftaran sidang.',
                'urutan'  => 5,
                'deadline' => '2026-06-28',
                'deliverables' => [
                    ['nama' => 'Laporan Akhir Proyek PBL (PDF)', 'urutan' => 1],
                    ['nama' => 'Video Demo & Poster Produk', 'urutan' => 2],
                    ['nama' => 'Peer Assessment & Kelayakan Sidang', 'urutan' => 3],
                ],
            ],
        ];

        foreach ($milestones as $milestoneData) {
            $deliverables = $milestoneData['deliverables'];
            unset($milestoneData['deliverables']);

            $milestone = Milestone::updateOrCreate(
                ['periode_id' => $periode->id, 'urutan' => $milestoneData['urutan']],
                array_merge($milestoneData, ['periode_id' => $periode->id])
            );

            foreach ($deliverables as $deliverableData) {
                Deliverable::updateOrCreate(
                    ['milestone_id' => $milestone->id, 'urutan' => $deliverableData['urutan']],
                    ['milestone_id' => $milestone->id, 'nama' => $deliverableData['nama'], 'urutan' => $deliverableData['urutan']]
                );
            }
        }
    }
}
