<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumpulanMilestone extends Model
{
    protected $table = 'pengumpulan_milestone';

    protected $fillable = [
        'milestone_id',
        'kelompok_id',
        'diunggah_oleh',
        'mata_kuliah',
        'file_name',
        'file_path',
        'link_bukti',
        'catatan',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'diunggah_oleh');
    }
}
