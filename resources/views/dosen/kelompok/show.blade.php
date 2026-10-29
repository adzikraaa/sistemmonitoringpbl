@extends('layouts.dosen')

@section('breadcrumb')
    Detail Kelompok
@endsection

@section('title', 'Detail Kelompok')

@section('content')
<div class="p-6">
    <!-- Breadcrumb & Back -->
    <div class="mb-4">
        <a href="{{ route('dosen.kelompok.index') }}" class="text-xs text-gray-500 hover:text-gray-800 flex items-center gap-1">
            ← Kembali ke kelompok
        </a>
    </div>

    <!-- Card Header Kelompok -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">DETAIL KELOMPOK • {{ $kelompok->kelas->nama_kelas ?? $kelompok->kelas ?? 'SIB 3B' }}</span>
        <h1 class="text-2xl font-serif font-bold text-gray-900 mt-1 mb-2">
            {{ $kelompok->nama_kelompok ?? 'Kelompok 2' }} - {{ $kelompok->judul_proyek ?? 'Pengembangan Sistem Monitoring PBL' }}
        </h1>
        <p class="text-xs text-gray-400 mb-4">Semester Ganjil 2026/2027</p>

        <span class="inline-block bg-emerald-50 text-emerald-600 border border-emerald-200 text-xs font-semibold px-3 py-1 rounded-full mb-6">
            {{ $kelompok->status ?? 'Berjalan' }}
        </span>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-100 text-xs">
            <div>
                <span class="text-gray-400 block mb-1">Dosen Pengampu</span>
                <span class="font-bold text-gray-800">{{ auth()->user()->name ?? 'Dian Subhi, S.Kom., M.Kom.' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block mb-1">Anggota kelompok</span>
                <span class="font-bold text-gray-800">{{ $kelompok->anggotaKelompok->count() }} mahasiswa</span>
            </div>
            <div>
                <span class="text-gray-400 block mb-1">Logbook tercatat</span>
                <span class="font-bold text-gray-800">{{ $kelompok->logbookMilestone->count() }} entri</span>
            </div>
            <div>
                <span class="text-gray-400 block mb-1">Aktivitas terakhir</span>
                <span class="font-bold text-gray-800">{{ $kelompok->updated_at ? $kelompok->updated_at->format('d M Y') : '24 Okt 2026' }}</span>
            </div>
        </div>
    </div>

    <!-- Grid Konten Detail -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card Anggota Kelompok -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h2 class="font-bold text-gray-900 text-base mb-1">Anggota kelompok</h2>
            <p class="text-xs text-gray-400 mb-4">Daftar anggota yang terdaftar di sistem.</p>

            <div class="space-y-4">
                @forelse($kelompok->anggotaKelompok as $anggota)
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-100 last:border-none last:pb-0">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr($anggota->mahasiswa->nama ?? 'M', 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">{{ $anggota->mahasiswa->nama ?? 'Nama Mahasiswa' }}</h4>
                            <span class="text-xs text-gray-400">Mahasiswa • {{ $anggota->mahasiswa->nim ?? 'SIB 3B' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400">Belum ada anggota terdaftar.</p>
                @endforelse
            </div>
        </div>

        <!-- Card Ringkasan Administrasi & Aktivitas -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h2 class="font-bold text-gray-900 text-base mb-1">Ringkasan administrasi & aktivitas</h2>
            <p class="text-xs text-gray-400 mb-6">Status berdasarkan data yang tercatat.</p>

            <div class="space-y-5 text-xs mb-6">
                <div class="flex items-start justify-between">
                    <div class="flex gap-3">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <div>
                            <h4 class="font-bold text-gray-800">Proposal proyek</h4>
                            <p class="text-gray-400">Proposal telah dikirim dan tercatat pada sistem.</p>
                        </div>
                    </div>
                    <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded font-medium">Terkirim</span>
                </div>

                <div class="flex items-start justify-between">
                    <div class="flex gap-3">
                        <span class="text-emerald-600 font-bold">G</span>
                        <div>
                            <h4 class="font-bold text-gray-800">Logbook aktivitas</h4>
                            <p class="text-gray-400">{{ $kelompok->logbookMilestone->count() }} catatan aktivitas tersedia.</p>
                        </div>
                    </div>
                    <span class="bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded font-medium">Tersedia</span>
                </div>

                <div class="flex items-start justify-between">
                    <div class="flex gap-3">
                        <span class="text-gray-400 font-bold">↗</span>
                        <div>
                            <h4 class="font-bold text-gray-800">Bukti pekerjaan</h4>
                            <p class="text-gray-400">Tautan dan berkas pendukung dapat diperiksa pada detail aktivitas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <button class="w-full py-2.5 bg-gray-50 border border-gray-200 hover:bg-gray-100 text-gray-800 text-xs font-semibold rounded-lg transition-colors">
                Buka Laporan Aktivitas
            </button>
        </div>
    </div>
</div>
@endsection