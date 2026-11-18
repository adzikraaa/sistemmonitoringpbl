<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilestoneKelompok extends Model
{
    public const STATUS_SELESAI = ['selesai'];

    protected $table = 'milestone_kelompok';

    protected $fillable = [
        'milestone_id',
        'kelompok_id',
        'status',
        'catatan',
    ];

    public static function normalizeStatus(?string $status): string
    {
        return strtolower(trim((string) $status));
    }

    public function isSelesai(): bool
    {
        return in_array(
            static::normalizeStatus($this->status),
            static::STATUS_SELESAI,
            true
        );
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }
}
