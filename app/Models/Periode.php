<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    protected $table = 'periode';

    protected $fillable = [
        'nama_periode',
        'tahun_akademik',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai'   => 'date',
            'tanggal_selesai' => 'date',
            'is_aktif'        => 'boolean',
        ];
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    public function kelompok(): HasMany
    {
        return $this->hasMany(Kelompok::class);
    }

    public function milestone(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }
}
