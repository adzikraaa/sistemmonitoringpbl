<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $kelompok_id
 * @property string $judul
 * @property string|null $deskripsi
 * @property string|null $latar_belakang
 * @property string|null $tujuan
 * @property string|null $metode
 * @property string|null $file_proposal
 * @property string|null $status
 * @property string|null $catatan_revisi
 * @property Carbon|null $tanggal_pengajuan
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Kelompok|null $kelompok
 * @property Collection<int, RiwayatProposal> $riwayatProposal
 */
class Proposal extends Model
{
    protected $table = 'proposal';

    protected $fillable = [
        'kelompok_id',
        'judul',
        'deskripsi',
        'latar_belakang',
        'tujuan',
        'metode',
        'file_proposal',
        'status',
        'catatan_revisi',
        'tanggal_pengajuan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
        ];
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function riwayatProposal(): HasMany
    {
        return $this->hasMany(RiwayatProposal::class);
    }

    /**
     * Mendapatkan nama berkas proposal yang manusiawi dan konsisten.
     */
    public function getNamaBerkasAttribute(): string
    {
        if (! $this->file_proposal) {
            return 'proposal-pbl.pdf';
        }

        $baseName = basename($this->file_proposal);
        $filenameWithoutExt = pathinfo($baseName, PATHINFO_FILENAME);

        // Jika berupa hash acak 32+ karakter (Str::random(40)), ubah jadi nama yang rapi
        if (strlen($filenameWithoutExt) >= 32 && ! str_contains($filenameWithoutExt, ' ') && ! str_contains($filenameWithoutExt, '-') && ! str_contains($filenameWithoutExt, '_')) {
            $slug = Str::slug((string) $this->judul);

            return 'proposal-'.($slug ?: 'kelompok').'-'.$this->id.'.pdf';
        }

        // Hilangkan prefiks versi teknis (contoh: v1_, v2_) agar nama yang tampil adalah nama file asli mahasiswa
        $cleanName = preg_replace('/^v\d+_/', '', $baseName);

        return $cleanName ?: $baseName;
    }
}
