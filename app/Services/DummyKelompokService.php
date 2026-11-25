<?php

namespace App\Services;

use App\Models\AnggotaKelompok;
use App\Models\Dosen;
use App\Models\DosenPengampu;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\Mahasiswa;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DummyKelompokService
{
    /**
     * Memastikan mahasiswa memiliki kelompok pada periode aktif.
     * Jika belum memiliki kelompok, kelompok dummy beserta 3 anggota dummy otomatis dibuat secara idempotent.
     */
    public function ensureDummyKelompok(Mahasiswa $mahasiswa): ?Kelompok
    {
        // 1. Dapatkan periode aktif
        $periode = Periode::query()
            ->where('is_aktif', true)
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->first() ?? Periode::query()->orderByDesc('id')->first();

        if (! $periode) {
            Log::warning('DummyKelompokService: Tidak ada data periode yang ditemukan.');

            return null;
        }

        // 2. Cek apakah mahasiswa sudah terdaftar di kelompok pada periode ini (idempotent check)
        $existingKelompok = Kelompok::query()
            ->where('periode_id', $periode->id)
            ->whereHas('mahasiswa', fn ($query) => $query->where('mahasiswa.id', $mahasiswa->id))
            ->orderBy('id')
            ->first();

        if ($existingKelompok) {
            return $existingKelompok;
        }

        // 3. Gunakan DB transaction dengan lock untuk mencegah duplikasi (race condition)
        return DB::transaction(function () use ($mahasiswa, $periode) {
            // Lock record mahasiswa untuk transaksi ini
            $lockedMahasiswa = Mahasiswa::query()
                ->where('id', $mahasiswa->id)
                ->lockForUpdate()
                ->first();

            if (! $lockedMahasiswa) {
                return null;
            }

            // Periksa ulang di dalam transaksi
            $existingKelompok = Kelompok::query()
                ->where('periode_id', $periode->id)
                ->whereHas('mahasiswa', fn ($query) => $query->where('mahasiswa.id', $lockedMahasiswa->id))
                ->orderBy('id')
                ->first();

            if ($existingKelompok) {
                return $existingKelompok;
            }

            // Tentukan kelas_id yang valid
            $kelasId = $lockedMahasiswa->kelas_id;
            if (! $kelasId) {
                $kelas = Kelas::query()
                    ->where('periode_id', $periode->id)
                    ->where('is_aktif', true)
                    ->first()
                    ?? Kelas::query()->where('periode_id', $periode->id)->first()
                    ?? Kelas::query()->where('is_aktif', true)->first()
                    ?? Kelas::query()->first();

                $kelasId = $kelas?->id;

                if ($kelasId) {
                    $lockedMahasiswa->update(['kelas_id' => $kelasId]);
                }
            }

            if (! $kelasId) {
                Log::error('DummyKelompokService: Gagal membuat kelompok dummy karena tidak ada data kelas.');

                return null;
            }

            // Tentukan dosen pembimbing jika tersedia
            $dosenId = DosenPengampu::where('kelas_id', $kelasId)->value('dosen_id')
                ?? Dosen::where('is_aktif', true)->value('id')
                ?? Dosen::query()->value('id');

            // Buat kelompok baru khusus untuk mahasiswa ini
            $namaKelompok = 'Kelompok Uji '.$lockedMahasiswa->nim;
            $kelompok = Kelompok::create([
                'nama_kelompok' => $namaKelompok,
                'judul_proyek' => null,
                'dosen_id' => $dosenId,
                'kelas_id' => $kelasId,
                'periode_id' => $periode->id,
                'status' => 'Aktif',
                'tanggal_dibuat' => now()->toDateString(),
            ]);

            // Anggota 1: Mahasiswa asli yang sedang login (Ketua)
            AnggotaKelompok::firstOrCreate(
                [
                    'kelompok_id' => $kelompok->id,
                    'mahasiswa_id' => $lockedMahasiswa->id,
                ],
                [
                    'role_label' => 'Ketua',
                    'is_ketua' => true,
                ]
            );

            // Anggota 2-4: Tiga anggota dummy
            for ($i = 2; $i <= 4; $i++) {
                $dummyNim = sprintf('DMY%05d%02d', $lockedMahasiswa->id, $i);
                $dummyEmail = sprintf('dummy%d_%d@student.dummy.local', $i, $lockedMahasiswa->id);

                $dummyMhs = Mahasiswa::firstOrCreate(
                    ['nim' => $dummyNim],
                    [
                        'nama' => "Anggota {$i}",
                        'email' => $dummyEmail,
                        'password' => bcrypt(Str::random(32)),
                        'kelas_id' => $kelasId,
                        'is_aktif' => true,
                    ]
                );

                AnggotaKelompok::firstOrCreate(
                    [
                        'kelompok_id' => $kelompok->id,
                        'mahasiswa_id' => $dummyMhs->id,
                    ],
                    [
                        'role_label' => 'Anggota',
                        'is_ketua' => false,
                    ]
                );
            }

            Log::info('DummyKelompokService: Kelompok dummy berhasil dibuat', [
                'kelompok_id' => $kelompok->id,
                'nama_kelompok' => $kelompok->nama_kelompok,
                'ketua_nim' => $lockedMahasiswa->nim,
                'total_anggota' => 4,
            ]);

            return $kelompok;
        });
    }
}
