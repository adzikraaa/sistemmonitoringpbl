<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\DosenPengampu;
use App\Models\Kelompok;
use App\Models\Periode;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif = Periode::where('is_aktif', true)->first();

        // Ambil data notifikasi dinamis dari aktivitas sistem
        $proposalsCount = Proposal::where('status', 'diajukan')->count();
        $penugasanCount = DosenPengampu::count();
        $kelompokCount  = Kelompok::count();

        // Daftar notifikasi koordinator terstruktur
        $notifikasiList = [
            [
                'id'         => 'notif-1',
                'kategori'   => 'proposal',
                'kategori_label' => 'Proposal PBL',
                'judul'      => 'Pengajuan Berkas Proposal Baru',
                'pesan'      => 'Kelompok 01 (SIB 3B) telah mengunggah berkas proposal proyek "Pengembangan Sistem Monitoring PBL" dalam format PDF dan menunggu peninjauan.',
                'waktu'      => '15 menit yang lalu',
                'is_read'    => false,
                'action_url' => route('koordinator.kelompok.index'),
                'action_label' => 'Periksa Kelompok & Proposal',
                'icon_type'  => 'proposal',
            ],
            [
                'id'         => 'notif-2',
                'kategori'   => 'penugasan',
                'kategori_label' => 'Penugasan Dosen',
                'judul'      => 'Penugasan Dosen Penilai Kelas SIB 3B',
                'pesan'      => 'Dosen penilai PBL telah berhasil dipetakan untuk mata kuliah Pemrograman Web Lanjut pada kelas SIB 3B.',
                'waktu'      => '2 jam yang lalu',
                'is_read'    => false,
                'action_url' => route('koordinator.penugasan-dosen.index'),
                'action_label' => 'Lihat Penugasan Dosen',
                'icon_type'  => 'dosen',
            ],
            [
                'id'         => 'notif-3',
                'kategori'   => 'kelompok',
                'kategori_label' => 'Milestone Kelompok',
                'judul'      => 'Laporan Milestone 1 Terkumpul',
                'pesan'      => 'Kelompok 02 telah mengunggah laporan capaian Milestone 1 untuk mata kuliah Pemrograman Web Lanjut.',
                'waktu'      => '5 jam yang lalu',
                'is_read'    => true,
                'action_url' => route('koordinator.kelompok.index'),
                'action_label' => 'Lihat Progres Kelompok',
                'icon_type'  => 'milestone',
            ],
            [
                'id'         => 'notif-4',
                'kategori'   => 'sistem',
                'kategori_label' => 'Sistem & Periode',
                'judul'      => 'Periode Akademik Aktif',
                'pesan'      => $periodeAktif 
                    ? "Sistem sedang berjalan pada periode aktif {$periodeAktif->nama_periode} ({$periodeAktif->tahun_akademik})."
                    : 'Belum ada periode akademik yang ditandai aktif. Silakan tinjau menu Periode.',
                'waktu'      => '1 hari yang lalu',
                'is_read'    => true,
                'action_url' => route('koordinator.periode.index'),
                'action_label' => 'Kelola Periode',
                'icon_type'  => 'sistem',
            ],
            [
                'id'         => 'notif-5',
                'kategori'   => 'penugasan',
                'kategori_label' => 'Dosen Penilai',
                'judul'      => 'Dosen Pembimbing Memperbarui Catatan',
                'pesan'      => 'Dosen pembimbing telah memberikan umpan balik dan persetujuan awal pada logbook mingguan mahasiswa.',
                'waktu'      => '2 hari yang lalu',
                'is_read'    => true,
                'action_url' => route('koordinator.penugasan-dosen.index'),
                'action_label' => 'Buka Direktori Dosen',
                'icon_type'  => 'dosen',
            ],
        ];

        // Jika user pernah klik "Tandai semua dibaca" dalam sesi ini
        if (session('all_notif_read', false)) {
            foreach ($notifikasiList as &$n) {
                $n['is_read'] = true;
            }
            unset($n);
        }

        $totalNotif  = count($notifikasiList);
        $unreadCount = count(array_filter($notifikasiList, fn($n) => !$n['is_read']));
        $actionCount = count(array_filter($notifikasiList, fn($n) => in_array($n['kategori'], ['proposal', 'penugasan'])));

        return view('koordinator.notifikasi', [
            'notifikasi'   => $notifikasiList,
            'totalNotif'   => $totalNotif,
            'unreadCount'  => $unreadCount,
            'actionCount'  => $actionCount,
            'periodeAktif' => $periodeAktif,
        ]);
    }

    public function markAllRead(Request $request)
    {
        session(['all_notif_read' => true]);

        return redirect()->route('koordinator.notifikasi')
            ->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }
}

