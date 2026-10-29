<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatProposal extends Model
{
    protected $table = 'riwayat_proposal';

    protected $fillable = [
        'proposal_id',
        'versi',
        'judul',
        'file_proposal',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'versi' => 'integer',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
