<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Dosen extends Authenticatable
{
    protected $table = 'dosen';

    protected $fillable = ['nama', 'email', 'password', 'nidn', 'koordinator_id', 'is_aktif', 'last_login_at'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_aktif' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function koordinator(): BelongsTo
    {
        return $this->belongsTo(Koordinator::class);
    }

    public function dosenPengampu(): HasMany
    {
        return $this->hasMany(DosenPengampu::class);
    }

    public function kelompok(): HasMany
    {
        return $this->hasMany(Kelompok::class);
    }

    public function notifikasi(): MorphMany
    {
        return $this->morphMany(Notifikasi::class, 'penerima');
    }
}
