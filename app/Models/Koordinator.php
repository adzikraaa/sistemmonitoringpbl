<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Koordinator extends Authenticatable
{
    protected $table = 'koordinator';

    protected $fillable = ['nama', 'email', 'password', 'nidn', 'dosen_id'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function dosen(): HasMany
    {
        return $this->hasMany(Dosen::class);
    }

    public function dosenUtama(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function notifikasi(): MorphMany
    {
        return $this->morphMany(Notifikasi::class, 'penerima');
    }
}
