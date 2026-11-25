<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\Notifikasi;
use App\Models\PengumpulanMilestone;
use App\Models\PenyelesaianMilestone;
use App\Services\CoordinatorActivityNotificationService;
use App\Services\PenyelesaianMilestoneService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PenyelesaianMilestoneController extends Controller
{
    public function index()
    {
        /** @var Dosen|null $dosen */
        $dosen = Auth::guard('dosen')->user();

        if (! $dosen) {
            abort(403);
        }

        $pengampuList = DosenPengampu::query()
            ->with(['mataKuliah', 'kelas'])
            ->where('dosen_id', $dosen->id)
            ->whereHas('kelas', fn ($query) => $query->where('is_aktif', true))
            ->whereHas('mataKuliah', fn ($query) => $query->where('is_aktif', true))
            ->get()
            ->filter(fn ($pengampu) => $pengampu->mataKuliah && $pengampu->kelas)
            ->values();

        $kelasIds = $pengampuList->pluck('kelas_id')->unique()->values()->all();
        $mataKuliahIds = $pengampuList->pluck('mata_kuliah_id')->unique()->values()->all();
        $mataKuliahSlugs = $pengampuList->pluck('mataKuliah.slug')->filter()->unique()->values()->all();

        $kelompokByKelas = Kelompok::query()
            ->with('periode')
            ->whereIn('kelas_id', $kelasIds)
            ->orderBy('nama_kelompok')
            ->get()
            ->groupBy('kelas_id');

        $kelompokIds = $kelompokByKelas->flatMap(fn ($items) => $items)->pluck('id')->all();
        $periodeIds = $kelompokByKelas->flatMap(fn ($items) => $items)
            ->pluck('periode_id')->filter()->unique()->values()->all();

        $milestoneByPeriode = $mataKuliahIds === []
            ? collect()
            : Milestone::query()
                ->with(['mataKuliah' => fn ($query) => $query->whereIn('mata_kuliah.id', $mataKuliahIds)])
                ->whereIn('periode_id', $periodeIds)
                ->orderBy('urutan')
                ->get()
                ->groupBy('periode_id');

        $statusMap = $kelompokIds === []
            ? collect()
            : PenyelesaianMilestone::query()
                ->select('penyelesaian_milestone.*')
                ->join('mata_kuliah', 'mata_kuliah.id', '=', 'penyelesaian_milestone.mata_kuliah_id')
                ->join('pengumpulan_milestone', function ($join) {
                    $join->on('pengumpulan_milestone.kelompok_id', '=', 'penyelesaian_milestone.kelompok_id')
                        ->on('pengumpulan_milestone.milestone_id', '=', 'penyelesaian_milestone.milestone_id')
                        ->on('pengumpulan_milestone.mata_kuliah', '=', 'mata_kuliah.slug');
                })
                ->whereIn('penyelesaian_milestone.kelompok_id', $kelompokIds)
                ->whereIn('penyelesaian_milestone.mata_kuliah_id', $mataKuliahIds)
                ->get()
                ->keyBy(fn ($row) => $row->kelompok_id.':'.$row->milestone_id.':'.$row->mata_kuliah_id);

        $pengumpulanMap = ($kelompokIds === [] || $mataKuliahSlugs === [])
            ? collect()
            : PengumpulanMilestone::query()
                ->whereIn('kelompok_id', $kelompokIds)
                ->whereIn('mata_kuliah', $mataKuliahSlugs)
                ->get()
                ->keyBy(fn ($row) => $row->kelompok_id.':'.$row->milestone_id.':'.$row->mata_kuliah);

        return view('dosen.penyelesaian-milestone.index', compact(
            'pengampuList',
            'kelompokByKelas',
            'milestoneByPeriode',
            'statusMap',
            'pengumpulanMap'
        ));
    }

    public function update(
        Request $request,
        Kelompok $kelompok,
        Milestone $milestone,
        MataKuliah $mataKuliah,
        PenyelesaianMilestoneService $service,
        CoordinatorActivityNotificationService $coordinatorNotifications
    ) {
        /** @var Dosen|null $dosen */
        $dosen = Auth::guard('dosen')->user();

        if (! $dosen) {
            abort(403);
        }

        $pengampu = DosenPengampu::query()
            ->where('dosen_id', $dosen->id)
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->where('kelas_id', $kelompok->kelas_id)
            ->exists();

        if (! $pengampu) {
            abort(403, 'Anda tidak mengampu mata kuliah ini pada kelas kelompok tersebut.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(PenyelesaianMilestone::STATUSES)],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $service->ubahStatus(
                $kelompok,
                $milestone,
                $mataKuliah,
                $dosen,
                $validated['status'],
                $validated['catatan'] ?? null
            );
        } catch (\InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['status' => $exception->getMessage()]);
        }

        try {
            $statusLabel = match ($validated['status']) {
                'selesai' => 'Selesai',
                'berjalan' => 'Sedang Berjalan',
                default => 'Belum Dimulai',
            };
            $recipients = $kelompok->mahasiswa;
            if ($recipients->isEmpty()) {
                $recipients = $kelompok->anggotaKelompok->map(fn ($a) => $a->mahasiswa)->filter();
            }

            foreach ($recipients->unique('id') as $mhs) {
                Notifikasi::create([
                    'penerima_type' => $mhs->getMorphClass(),
                    'penerima_id' => $mhs->id,
                    'judul' => 'Milestone '.$statusLabel.': '.$milestone->judul,
                    'isi' => 'Status milestone "'.$milestone->judul.'" untuk mata kuliah "'.$mataKuliah->nama_matkul.'" telah diubah menjadi "'.$statusLabel.'" oleh dosen.'.(! empty($validated['catatan']) ? ' Catatan: '.Str::limit((string) $validated['catatan'], 100) : ''),
                    'tautan' => route('mahasiswa.milestone'),
                    'dibaca_pada' => null,
                ]);
            }

            $coordinatorNotifications->kirim(
                $dosen,
                'Dosen Memperbarui Status Milestone',
                'Status "'.$milestone->judul.'" untuk '.$mataKuliah->nama_matkul.' pada '.$kelompok->nama_kelompok.' diubah menjadi '.$statusLabel.'.',
                route('koordinator.kelompok.show', $kelompok)
            );
        } catch (\Throwable $e) {
            // Failsafe notifikasi
        }

        return back()->with('success', 'Status penyelesaian milestone berhasil diperbarui.');
    }

    /**
     * Mengunduh berkas laporan pengumpulan milestone mahasiswa oleh dosen pengampu.
     * Pencarian dilakukan dengan identitas unik: kelompok_id + milestone_id + mata_kuliah_slug.
     */
    public function download(Kelompok $kelompok, Milestone $milestone, MataKuliah $mataKuliah)
    {
        /** @var Dosen|null $dosen */
        $dosen = Auth::guard('dosen')->user();

        if (! $dosen) {
            abort(403, 'Akses ditolak. Silakan masuk sebagai dosen terlebih dahulu.');
        }

        // 1. Validasi dosen benar-benar mengampu mata kuliah ini pada kelas kelompok tersebut
        $pengampu = DosenPengampu::query()
            ->where('dosen_id', $dosen->id)
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->where('kelas_id', $kelompok->kelas_id)
            ->exists();

        if (! $pengampu) {
            abort(403, 'Anda tidak mengampu mata kuliah ini pada kelas kelompok tersebut.');
        }

        // 2. Cari laporan berdasarkan identitas unik: kelompok_id + milestone_id + mata_kuliah_slug
        $pengumpulan = PengumpulanMilestone::query()
            ->where('kelompok_id', $kelompok->id)
            ->where('milestone_id', $milestone->id)
            ->where('mata_kuliah', $mataKuliah->slug)
            ->first();

        if (! $pengumpulan) {
            abort(404, 'Laporan pengumpulan milestone tidak ditemukan.');
        }

        // 3. Validasi keberadaan file pada private local disk
        if (! $pengumpulan->file_path || ! Storage::disk('local')->exists($pengumpulan->file_path)) {
            abort(404, 'Berkas fisik laporan tidak ditemukan.');
        }

        // 4. Unduh dengan nama asli file
        $downloadName = $pengumpulan->file_name ?: basename($pengumpulan->file_path);

        return Storage::disk('local')->download($pengumpulan->file_path, $downloadName);
    }

    /**
     * Rute alternatif untuk mengunduh berkas laporan via entitas PengumpulanMilestone.
     */
    public function downloadPengumpulan(PengumpulanMilestone $pengumpulan)
    {
        $kelompok = $pengumpulan->kelompok;
        $milestone = $pengumpulan->milestone;
        $mataKuliah = MataKuliah::query()->where('slug', $pengumpulan->mata_kuliah)->first();

        if (! $kelompok || ! $milestone || ! $mataKuliah) {
            abort(404, 'Entitas laporan milestone tidak lengkap.');
        }

        return $this->download($kelompok, $milestone, $mataKuliah);
    }
}
