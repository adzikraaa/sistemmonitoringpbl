<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    protected $table = 'proposal';

    protected $fillable = [
        'kelompok_id',
        'judul',
        'deskripsi',
        'latar_belakang',
        'tujuan',
        'metode',
        'file_proposal',
        'status',
        'catatan_revisi',
        'tanggal_pengajuan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
        ];
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function riwayatProposal(): HasMany
    {
        return $this->hasMany(RiwayatProposal::class);
    }
}
