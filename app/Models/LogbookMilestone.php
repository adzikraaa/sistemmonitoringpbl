<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookMilestone extends Model
{
    protected $table = 'logbook_milestone';

    protected $fillable = [
        'milestone_id',
        'kelompok_id',
        'mahasiswa_id',
        'tanggal',
        'judul',
        'deskripsi',
        'kegiatan',
        'progress',
        'hasil',
        'kendala',
        'rencana',
        'link_bukti',
        'file_bukti',
        'status',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'progress' => 'integer',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
