<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    protected $table = 'milestone';

    protected $fillable = [
        'periode_id',
        'kode',
        'judul',
        'deskripsi',
        'urutan',
        'deadline',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'urutan' => 'integer',
        ];
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function deliverable(): HasMany
    {
        return $this->hasMany(Deliverable::class);
    }

    public function logbookMilestone(): HasMany
    {
        return $this->hasMany(LogbookMilestone::class);
    }

    public function milestoneKelompok(): HasMany
    {
        return $this->hasMany(MilestoneKelompok::class);
    }

    public function penilaianKelompok(): HasMany
    {
        return $this->hasMany(PenilaianKelompok::class);
    }

    public function penilaianIndividu(): HasMany
    {
        return $this->hasMany(PenilaianIndividu::class);
    }
}
