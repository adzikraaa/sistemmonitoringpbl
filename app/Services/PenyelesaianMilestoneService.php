<?php

namespace App\Services;

use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelompok;
use App\Models\MataKuliah;
use App\Models\Milestone;
use App\Models\MilestoneMataKuliah;
use App\Models\PengumpulanMilestone;
use App\Models\PenyelesaianMilestone;
use App\Models\RiwayatPenyelesaianMilestone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class PenyelesaianMilestoneService
{
    public function ubahStatus(
        Kelompok $kelompok,
        Milestone $milestone,
        MataKuliah $mataKuliah,
        Dosen $dosen,
        string $status,
        ?string $catatan = null
    ): PenyelesaianMilestone {
        $statusBaru = PenyelesaianMilestone::normalizeStatus($status);

        if (! in_array($statusBaru, PenyelesaianMilestone::STATUSES, true)) {
            throw new \InvalidArgumentException('Status penyelesaian milestone tidak valid.');
        }

        $pengampu = DosenPengampu::query()
            ->where('dosen_id', $dosen->id)
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->where('kelas_id', $kelompok->kelas_id)
            ->exists();

        if (! $pengampu) {
            throw new AuthorizationException('Anda tidak mengampu mata kuliah ini pada kelas kelompok tersebut.');
        }

        if ((int) $milestone->periode_id !== (int) $kelompok->periode_id) {
            throw new \InvalidArgumentException('Milestone tidak berada pada periode kelompok.');
        }

        $mappingAda = MilestoneMataKuliah::query()
            ->where('milestone_id', $milestone->id)
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->exists();

        if (! $mappingAda) {
            throw new \InvalidArgumentException('Milestone tidak terdaftar untuk mata kuliah tersebut.');
        }

        if (! $mataKuliah->is_aktif) {
            throw new \InvalidArgumentException('Mata kuliah tidak aktif.');
        }

        $pengumpulanAda = PengumpulanMilestone::query()
            ->where('kelompok_id', $kelompok->id)
            ->where('milestone_id', $milestone->id)
            ->where('mata_kuliah', $mataKuliah->slug)
            ->exists();

        if (! $pengumpulanAda) {
            throw new \InvalidArgumentException('Laporan mata kuliah belum dikumpulkan.');
        }

        return DB::transaction(function () use (
            $kelompok,
            $milestone,
            $mataKuliah,
            $dosen,
            $statusBaru,
            $catatan
        ) {
            $identity = [
                'kelompok_id' => $kelompok->id,
                'milestone_id' => $milestone->id,
                'mata_kuliah_id' => $mataKuliah->id,
            ];

            $penyelesaian = PenyelesaianMilestone::query()
                ->where($identity)
                ->lockForUpdate()
                ->first();

            $statusLama = $penyelesaian
                ? PenyelesaianMilestone::normalizeStatus($penyelesaian->status)
                : null;

            $data = [
                'status' => $statusBaru,
                'catatan' => $catatan,
                'diverifikasi_oleh' => $dosen->id,
                'diverifikasi_pada' => now(),
            ];

            if (! $penyelesaian) {
                try {
                    $penyelesaian = PenyelesaianMilestone::create(array_merge($data, $identity));

                    RiwayatPenyelesaianMilestone::create([
                        'penyelesaian_milestone_id' => $penyelesaian->id,
                        'dosen_id' => $dosen->id,
                        'status_lama' => null,
                        'status_baru' => $statusBaru,
                        'catatan' => $catatan,
                    ]);

                    return $penyelesaian;
                } catch (UniqueConstraintViolationException $exception) {
                    $penyelesaian = PenyelesaianMilestone::query()
                        ->where($identity)
                        ->lockForUpdate()
                        ->first();

                    if (! $penyelesaian) {
                        throw $exception;
                    }

                    $statusLama = PenyelesaianMilestone::normalizeStatus($penyelesaian->status);
                }
            }

            if ($statusLama === $statusBaru) {
                if ($catatan !== null && $catatan !== $penyelesaian->catatan) {
                    $penyelesaian->update(['catatan' => $catatan]);
                }

                return $penyelesaian;
            }

            $penyelesaian->update($data);

            RiwayatPenyelesaianMilestone::create([
                'penyelesaian_milestone_id' => $penyelesaian->id,
                'dosen_id' => $dosen->id,
                'status_lama' => $statusLama,
                'status_baru' => $statusBaru,
                'catatan' => $catatan,
            ]);

            return $penyelesaian;
        });
    }
}
