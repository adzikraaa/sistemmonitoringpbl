<?php

namespace App\Models;

use App\Services\MilestoneMataKuliahSync;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'kode',
        'nama_matkul',
        'slug',
        'prodi',
        'sks',
        'semester',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'sks' => 'integer',
            'semester' => 'integer',
            'is_aktif' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MataKuliah $mataKuliah) {
            if (blank($mataKuliah->slug)) {
                $mataKuliah->slug = static::uniqueSlug((string) $mataKuliah->nama_matkul);
            }
        });

        static::updating(function (MataKuliah $mataKuliah) {
            if (blank($mataKuliah->slug)) {
                $mataKuliah->slug = static::uniqueSlug(
                    (string) $mataKuliah->nama_matkul,
                    $mataKuliah->id
                );
            }
        });

        static::created(function (MataKuliah $mataKuliah) {
            app(MilestoneMataKuliahSync::class)->forMataKuliah($mataKuliah);
        });

        static::updated(function (MataKuliah $mataKuliah) {
            if ($mataKuliah->wasChanged('is_aktif')) {
                app(MilestoneMataKuliahSync::class)->forMataKuliah($mataKuliah);
            }
        });
    }

    public function dosenPengampu(): HasMany
    {
        return $this->hasMany(DosenPengampu::class);
    }

    public function milestone(): BelongsToMany
    {
        return $this->belongsToMany(
            Milestone::class,
            'milestone_mata_kuliah',
            'mata_kuliah_id',
            'milestone_id'
        )->withTimestamps();
    }

    public function penyelesaianMilestone(): HasMany
    {
        return $this->hasMany(PenyelesaianMilestone::class);
    }

    protected static function uniqueSlug(string $nama, ?int $ignoreId = null): string
    {
        $base = Str::slug($nama) ?: 'mata-kuliah';
        $slug = $base;
        $counter = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
