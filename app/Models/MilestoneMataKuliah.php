<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneMataKuliah extends Model
{
    protected $table = 'milestone_mata_kuliah';

    protected $fillable = [
        'milestone_id',
        'mata_kuliah_id',
    ];

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }
}
