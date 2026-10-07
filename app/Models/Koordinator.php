<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Koordinator extends Authenticatable
{
    protected $table = 'koordinator';

    protected $fillable = ['nama', 'email', 'password', 'nip', 'nidn'];
    
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
}
