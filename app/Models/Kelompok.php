<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelompok extends Model
{
    protected $table = 'kelompok';

    protected $fillable = [
        'nama_kelompok',
        'dosen_id',
        'kelas_id',
        'periode_id',
        'jadwal_bimbingan',
        'tanggal_dibuat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dibuat' => 'date',
        ];
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function anggotaKelompok(): HasMany
    {
        return $this->hasMany(AnggotaKelompok::class);
    }

    public function mahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'anggota_kelompok')
            ->withPivot('role_label', 'is_ketua')
            ->withTimestamps();
    }

    public function proposal(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function logbookMilestone(): HasMany
    {
        return $this->hasMany(LogbookMilestone::class);
    }

    public function milestoneKelompok(): HasMany
    {
        return $this->hasMany(MilestoneKelompok::class);
    }

    public function pengumpulanDeliverable(): HasMany
    {
        return $this->hasMany(PengumpulanDeliverable::class);
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
