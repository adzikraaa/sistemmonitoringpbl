<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPenyelesaianMilestone extends Model
{
    protected $table = 'riwayat_penyelesaian_milestone';

    protected $fillable = [
        'penyelesaian_milestone_id',
        'dosen_id',
        'status_lama',
        'status_baru',
        'catatan',
    ];

    public function penyelesaianMilestone(): BelongsTo
    {
        return $this->belongsTo(PenyelesaianMilestone::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }
}
