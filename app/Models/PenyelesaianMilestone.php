<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenyelesaianMilestone extends Model
{
    public const STATUS_BELUM_DIMULAI = 'belum_dimulai';

    public const STATUS_BERJALAN = 'berjalan';

    public const STATUS_SELESAI = 'selesai';

    public const STATUSES = [
        self::STATUS_BELUM_DIMULAI,
        self::STATUS_BERJALAN,
        self::STATUS_SELESAI,
    ];

    protected $table = 'penyelesaian_milestone';

    protected $fillable = [
        'kelompok_id',
        'milestone_id',
        'mata_kuliah_id',
        'status',
        'catatan',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public static function normalizeStatus(?string $status): string
    {
        return strtolower(trim((string) $status));
    }

    public function isSelesai(): bool
    {
        return static::normalizeStatus($this->status) === self::STATUS_SELESAI;
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'diverifikasi_oleh');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatPenyelesaianMilestone::class);
    }
}
