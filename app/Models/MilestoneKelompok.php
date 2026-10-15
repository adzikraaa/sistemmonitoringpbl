<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneKelompok extends Model
{
    protected $table = 'milestone_kelompok';

    protected $fillable = [
        'milestone_id',
        'kelompok_id',
        'status',
        'catatan',
    ];

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }
}
