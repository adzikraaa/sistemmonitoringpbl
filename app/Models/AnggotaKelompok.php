<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaKelompok extends Model
{
    protected $table = 'anggota_kelompok';

    protected $fillable = [
        'kelompok_id',
        'mahasiswa_id',
        'role_label',
        'is_ketua',
    ];

    protected function casts(): array
    {
        return [
            'is_ketua' => 'boolean',
        ];
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
