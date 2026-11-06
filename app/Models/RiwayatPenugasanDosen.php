<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPenugasanDosen extends Model
{
    protected $table = 'riwayat_penugasan_dosen';

    protected $fillable = [
        'kelompok_id',
        'dosen_id',
        'dosen_sebelumnya_id',
        'periode_id',
        'koordinator_id',
        'aksi',
        'catatan',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function dosenSebelumnya(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_sebelumnya_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function koordinator(): BelongsTo
    {
        return $this->belongsTo(Koordinator::class);
    }
}

