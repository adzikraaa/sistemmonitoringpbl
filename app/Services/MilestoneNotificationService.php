<?php

namespace App\Services;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Milestone;
use App\Models\Notifikasi;

class MilestoneNotificationService
{
    public function kirim(Milestone $milestone, bool $diperbarui = false): void
    {
        $milestone->loadMissing('periode');

        $mulai = $milestone->tanggal_mulai?->translatedFormat('d M Y') ?? 'belum ditetapkan';
        $tenggat = $milestone->deadline?->translatedFormat('d M Y') ?? 'belum ditetapkan';
        $aksi = $diperbarui ? 'diperbarui' : 'ditetapkan';
        $judul = 'Jadwal '.$milestone->judul.' '.$aksi;
        $isi = "Milestone {$milestone->kode} dimulai {$mulai} dan memiliki tenggat {$tenggat}.";
        $now = now();
        $mahasiswaRecipientType = (new Mahasiswa)->getMorphClass();
        $dosenRecipientType = (new Dosen)->getMorphClass();

        $mahasiswaIds = Mahasiswa::query()
            ->where('is_aktif', true)
            ->whereHas('kelas', fn ($query) => $query->where('periode_id', $milestone->periode_id))
            ->pluck('id');

        $dosenIds = Dosen::query()
            ->where('is_aktif', true)
            ->whereHas('dosenPengampu.kelas', fn ($query) => $query->where('periode_id', $milestone->periode_id))
            ->pluck('id');

        $rows = $mahasiswaIds->map(fn (int $id): array => [
            'penerima_type' => $mahasiswaRecipientType,
            'penerima_id' => $id,
            'judul' => $judul,
            'isi' => $isi,
            'tautan' => route('mahasiswa.milestone'),
            'created_at' => $now,
            'updated_at' => $now,
        ])->merge($dosenIds->map(fn (int $id): array => [
            'penerima_type' => $dosenRecipientType,
            'penerima_id' => $id,
            'judul' => $judul,
            'isi' => $isi,
            'tautan' => route('dosen.penyelesaian-milestone.index'),
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        foreach ($rows->chunk(500) as $chunk) {
            Notifikasi::query()->insert($chunk->all());
        }
    }
}
