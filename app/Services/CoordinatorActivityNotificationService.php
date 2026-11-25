<?php

namespace App\Services;

use App\Models\Dosen;
use App\Models\Koordinator;
use App\Models\Notifikasi;

class CoordinatorActivityNotificationService
{
    public function kirim(Dosen $dosen, string $judul, string $isi, string $tautan): void
    {
        $now = now();
        $recipientType = (new Koordinator)->getMorphClass();
        $rows = Koordinator::query()->pluck('id')->map(fn (int $koordinatorId): array => [
            'penerima_type' => $recipientType,
            'penerima_id' => $koordinatorId,
            'judul' => $judul,
            'isi' => $dosen->nama.' — '.$isi,
            'tautan' => $tautan,
            'dibaca_pada' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach ($rows->chunk(500) as $chunk) {
            Notifikasi::query()->insert($chunk->all());
        }
    }
}
