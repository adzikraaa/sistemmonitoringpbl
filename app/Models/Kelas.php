<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['nama_kelas', 'prodi', 'periode_id', 'is_aktif'];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function kelompok(): HasMany
    {
        return $this->hasMany(Kelompok::class);
    }

    public function dosenPengampu(): HasMany
    {
        return $this->hasMany(DosenPengampu::class);
    }
}
