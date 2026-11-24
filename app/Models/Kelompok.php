<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $nama_kelompok
 * @property string|null $judul_proyek
 * @property int|null $dosen_id
 * @property int|null $kelas_id
 * @property int|null $periode_id
 * @property string|null $jadwal_bimbingan
 * @property string|null $status
 * @property Carbon|null $tanggal_dibuat
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $progress
 * @property Kelas|null $kelas
 * @property Dosen|null $dosen
 * @property Periode|null $periode
 * @property \Illuminate\Database\Eloquent\Collection<int, AnggotaKelompok> $anggotaKelompok
 * @property \Illuminate\Database\Eloquent\Collection<int, LogbookMilestone> $logbookMilestone
 * @property \Illuminate\Database\Eloquent\Collection<int, Proposal> $proposal
 */
class Kelompok extends Model
{
    protected $table = 'kelompok';

    protected $fillable = [
        'nama_kelompok',
        'judul_proyek',
        'dosen_id',
        'kelas_id',
        'periode_id',
        'jadwal_bimbingan',
        'status',
        'tanggal_dibuat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dibuat' => 'date',
        ];
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function anggotaKelompok(): HasMany
    {
        return $this->hasMany(AnggotaKelompok::class);
    }

    public function scopePeriodeAktif(Builder $query): Builder
    {
        $periodeId = Periode::query()
            ->where('is_aktif', true)
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->value('id');

        if ($periodeId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('periode_id', $periodeId);
    }

    public function scopeWithIdentitas(Builder $query): Builder
    {
        return $query->with([
            'anggotaKelompok' => fn ($anggota) => $anggota
                ->with('mahasiswa')
                ->orderByDesc('is_ketua')
                ->orderBy('id'),
        ]);
    }

    public function ketua(): ?AnggotaKelompok
    {
        if ($this->relationLoaded('anggotaKelompok')) {
            return $this->anggotaKelompok
                ->where('is_ketua', true)
                ->sortBy('id')
                ->first();
        }

        return $this->anggotaKelompok()
            ->where('is_ketua', true)
            ->orderBy('id')
            ->first();
    }

    public function nimKetua(): ?string
    {
        return $this->ketua()?->mahasiswa?->nim;
    }

    public function anggotaTerurut(): Collection
    {
        if ($this->relationLoaded('anggotaKelompok')) {
            return $this->anggotaKelompok
                ->sortBy([
                    ['is_ketua', 'desc'],
                    ['id', 'asc'],
                ])
                ->values();
        }

        return $this->anggotaKelompok()
            ->with('mahasiswa')
            ->orderByDesc('is_ketua')
            ->orderBy('id')
            ->get();
    }

    public function mahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'anggota_kelompok')
            ->withPivot('role_label', 'is_ketua')
            ->withTimestamps();
    }

    public function proposal(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function logbookMilestone(): HasMany
    {
        return $this->hasMany(LogbookMilestone::class);
    }

    public function milestoneKelompok(): HasMany
    {
        return $this->hasMany(MilestoneKelompok::class);
    }

    public function penyelesaianMilestone(): HasMany
    {
        return $this->hasMany(PenyelesaianMilestone::class);
    }

    public function pengumpulanDeliverable(): HasMany
    {
        return $this->hasMany(PengumpulanDeliverable::class);
    }

    public function penilaianKelompok(): HasMany
    {
        return $this->hasMany(PenilaianKelompok::class);
    }

    public function penilaianIndividu(): HasMany
    {
        return $this->hasMany(PenilaianIndividu::class);
    }

    public function riwayatPenugasan(): HasMany
    {
        return $this->hasMany(RiwayatPenugasanDosen::class);
    }
}
