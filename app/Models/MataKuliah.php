<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'nama_matkul',
        'prodi',
        'sks',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'sks'      => 'integer',
            'is_aktif' => 'boolean',
        ];
    }

    public function dosenPengampu(): HasMany
    {
        return $this->hasMany(DosenPengampu::class);
    }
}
