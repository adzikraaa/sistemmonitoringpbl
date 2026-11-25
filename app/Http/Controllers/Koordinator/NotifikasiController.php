<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Periode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $koordinator = $request->user('koordinator');
        $records = $koordinator->notifikasi()->latest()->get();

        $notifikasi = $records->map(function (Notifikasi $item): array {
            $judulLower = Str::lower($item->judul);

            [$kategori, $kategoriLabel, $iconType, $actionLabel] = match (true) {
                Str::contains($judulLower, 'proposal') => ['proposal', 'Review Proposal', 'proposal', 'Lihat kelompok'],
                Str::contains($judulLower, ['logbook', 'milestone', 'laporan']) => ['kelompok', 'Aktivitas Dosen', 'milestone', 'Lihat progres kelompok'],
                Str::contains($judulLower, ['penugasan', 'dosen']) => ['penugasan', 'Pemantauan Dosen', 'dosen', 'Lihat penugasan'],
                default => ['sistem', 'Aktivitas Sistem', 'sistem', 'Lihat detail'],
            };

            return [
                'id' => $item->id,
                'kategori' => $kategori,
                'kategori_label' => $kategoriLabel,
                'judul' => $item->judul,
                'pesan' => $item->isi,
                'waktu' => $item->created_at?->diffForHumans() ?? 'Baru saja',
                'is_read' => $item->dibaca_pada !== null,
                'action_url' => $item->tautan,
                'action_label' => $actionLabel,
                'icon_type' => $iconType,
            ];
        })->all();

        return view('koordinator.notifikasi', [
            'notifikasi' => $notifikasi,
            'totalNotif' => $records->count(),
            'unreadCount' => $records->whereNull('dibaca_pada')->count(),
            'actionCount' => $records->whereNull('dibaca_pada')->count(),
            'periodeAktif' => Periode::query()->where('is_aktif', true)->first(),
        ]);
    }

    public function markRead(Request $request, Notifikasi $notifikasi): RedirectResponse
    {
        $koordinator = $request->user('koordinator');

        abort_unless(
            $notifikasi->penerima_type === $koordinator->getMorphClass()
            && (int) $notifikasi->penerima_id === (int) $koordinator->id,
            403
        );

        $notifikasi->update(['dibaca_pada' => now()]);

        $target = $notifikasi->tautan;

        if (! $target || (! Str::startsWith($target, '/') && ! Str::startsWith($target, url('/')))) {
            $target = route('koordinator.notifikasi');
        }

        return redirect()->to($target);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user('koordinator')->notifikasi()
            ->whereNull('dibaca_pada')
            ->update(['dibaca_pada' => now()]);

        return redirect()->route('koordinator.notifikasi')
            ->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }
}
