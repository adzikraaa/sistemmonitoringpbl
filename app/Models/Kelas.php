<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['nama_kelas'];

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }
}
