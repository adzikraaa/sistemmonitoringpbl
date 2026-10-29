@extends('layouts.dosen')

@section('title', 'Kelompok Bimbingan')

@section('content')
<div class="p-6">
    <!-- Header Page -->
    <div class="mb-6">
        <span class="text-xs font-semibold tracking-wider text-gray-400 uppercase">RUANG DOSEN • KELOMPOK</span>
        <div class="flex items-center justify-between mt-1">
            <h1 class="text-3xl font-serif font-bold text-gray-900">Kelompok bimbingan</h1>
            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-full border border-gray-200">
                • {{ $kelompokList->total() }} Kelompok Terdaftar
            </span>
        </div>
        <p class="text-sm text-gray-500 mt-1">Pantau perkembangan proyek dari kelompok yang kamu dampingi.</p>
    </div>

    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('dosen.kelompok.index') }}" x-data="{ submit() { $el.submit() } }">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <!-- Input Search -->
            <div class="relative w-full md:w-1/2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama kelompok atau proyek..." 
                       class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Select Filters -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                <select name="kelas" @change="submit()" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Kelas</option>
                    <option value="SIB 3B" {{ request('kelas') == 'SIB 3B' ? 'selected' : '' }}>SIB 3B</option>
                    <option value="SIB 3A" {{ request('kelas') == 'SIB 3A' ? 'selected' : '' }}>SIB 3A</option>
                </select>

                <select name="status" @change="submit()" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua status</option>
                    <option value="Berjalan" {{ request('status') == 'Berjalan' ? 'selected' : '' }}>Berjalan</option>
                    <option value="Review" {{ request('status') == 'Review' ? 'selected' : '' }}>Review</option>
                    <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Grid List Kelompok -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @forelse($kelompokList as $item)
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            🌐
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base leading-snug">{{ $item->nama_kelompok ?? 'Kelompok' }}</h3>
                            <p class="text-xs text-gray-500">{{ $item->judul_proyek ?? 'Sistem Informasi Monitoring PBL' }}</p>
                        </div>
                    </div>
                    
                    <!-- Status Badge -->
                    @php
                        $statusClass = match($item->status ?? 'Berjalan') {
                            'Review' => 'bg-amber-50 text-amber-600 border-amber-200',
                            'Terlambat' => 'bg-rose-50 text-rose-600 border-rose-200',
                            default => 'bg-emerald-50 text-emerald-600 border-emerald-200'
                        };
                    @endphp
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $statusClass }}">
                        {{ $item->status ?? 'Berjalan' }}
                    </span>
                </div>

                <!-- Info Grid -->
                <div class="bg-gray-50 rounded-lg p-3 grid grid-cols-3 gap-2 text-xs mb-4">
                    <div>
                        <span class="text-gray-400 block mb-0.5">Anggota</span>
                        <span class="font-bold text-gray-800">{{ $item->anggotaKelompok->count() ?? 4 }} mahasiswa</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Catatan logbook</span>
                        <span class="font-bold text-gray-800">{{ $item->logbookMilestone->count() ?? 12 }} catatan</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Aktivitas terakhir</span>
                        <span class="font-bold text-gray-800">{{ $item->updated_at ? $item->updated_at->format('d M Y') : '25 Okt 2026' }}</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-5">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-400">Ringkasan catatan</span>
                        <span class="font-bold text-gray-700">{{ $item->progress_dummy }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-emerald-700 h-1.5 rounded-full" style="width: {{ $item->progress_dummy }}%"></div>
                    </div>
                </div>

                <!-- Tombol Detail -->
                <div class="text-right">
                    <a href="{{ route('dosen.kelompok.show', $item->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-medium rounded-lg transition-colors">
                        Lihat detail →
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-500 text-sm">Tidak ada kelompok bimbingan yang ditemukan.</p>
            </div>
        @endforelse
    </div>

    <!-- Alert Catatan Bimbingan Aktif -->
    <div class="bg-neutral-800 text-white p-4 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-neutral-700 flex items-center justify-center text-amber-400 font-bold">
                ⚠️
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold block">CATATAN BIMBINGAN AKTIF</span>
                <p class="text-xs text-gray-200 mt-0.5">Sinyal perhatian: Kelompok 02 memerlukan review berkala pada logbook sprint 3.</p>
            </div>
        </div>
        <button class="bg-amber-100 hover:bg-amber-200 text-neutral-900 text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
            Kirim pengingat →
        </button>
    </div>
</div>
@endsection