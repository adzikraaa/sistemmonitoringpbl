<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Mahasiswa extends Authenticatable
{
    protected $table = 'mahasiswa';

    protected $fillable = ['nama', 'email', 'password', 'nim', 'kelas_id', 'is_aktif'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_aktif' => 'boolean',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function anggotaKelompok(): HasMany
    {
        return $this->hasMany(AnggotaKelompok::class);
    }

    public function kelompok(): BelongsToMany
    {
        return $this->belongsToMany(Kelompok::class, 'anggota_kelompok')
            ->withPivot('role_label', 'is_ketua')
            ->withTimestamps();
    }

    public function logbookMilestone(): HasMany
    {
        return $this->hasMany(LogbookMilestone::class);
    }

    public function penilaianIndividu(): HasMany
    {
        return $this->hasMany(PenilaianIndividu::class);
    }

    public function notifikasi(): MorphMany
    {
        return $this->morphMany(Notifikasi::class, 'penerima');
    }
}
