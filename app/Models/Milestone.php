<?php

namespace App\Models;

use App\Services\MilestoneMataKuliahSync;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    protected static function booted(): void
    {
        static::created(function (Milestone $milestone) {
            app(MilestoneMataKuliahSync::class)->forMilestone($milestone);
        });
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function deliverable(): HasMany
    {
        return $this->hasMany(Deliverable::class);
    }

    public function mataKuliah(): BelongsToMany
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'milestone_mata_kuliah',
            'milestone_id',
            'mata_kuliah_id'
        )->withTimestamps();
    }

    public function logbookMilestone(): HasMany
    {
        return $this->hasMany(LogbookMilestone::class);
    }

    public function milestoneKelompok(): HasMany
    {
        return $this->hasMany(MilestoneKelompok::class);
    }

    public function penyelesaianMilestone(): HasMany
    {
        return $this->hasMany(PenyelesaianMilestone::class);
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
