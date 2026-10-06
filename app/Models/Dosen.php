<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Dosen extends Authenticatable
{
    protected $table = 'dosen';

    protected $fillable = ['nama', 'email', 'password', 'nip', 'koordinator_id'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function koordinator(): BelongsTo
    {
        return $this->belongsTo(Koordinator::class);
    }
}
