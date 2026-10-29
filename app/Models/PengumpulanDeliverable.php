<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumpulanDeliverable extends Model
{
    protected $table = 'pengumpulan_deliverable';

    protected $fillable = [
        'deliverable_id',
        'kelompok_id',
        'diunggah_oleh',
        'file_name',
        'file_path',
        'status',
        'catatan',
    ];

    public function deliverable(): BelongsTo
    {
        return $this->belongsTo(Deliverable::class);
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
