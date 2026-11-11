@extends('layouts.dosen')

@section('title', 'Kelompok Bimbingan')

@section('breadcrumb')
    Kelompok
@endsection

@section('content')

{{-- Page Header (Standar SIM-PBL rata kiri sejajar breadcrumb) --}}
<div class="sim-page-header" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">RUANG DOSEN &bull; KELOMPOK</p>
        <h1 class="sim-page-header__title">Kelompok bimbingan</h1>
        <p class="sim-page-header__desc">
            Pantau perkembangan proyek dari kelompok yang kamu dampingi.
        </p>
    </div>

    <div>
        <span class="inline-flex items-center gap-2 bg-white text-[#4A433D] text-xs font-medium px-3.5 py-1.5 rounded-full border border-[#E4DED5]">
            <span class="w-2 h-2 rounded-full bg-[#2F5C4A]"></span>
            {{ $totalTerdaftar ?? $kelompokList->total() }} Kelompok Terdaftar
        </span>
    </div>
</div>

{{-- Main Content Wrap (Standar SIM-PBL) --}}
<div style="padding: 0 32px 40px;">

    <!-- Tab Navigasi -->
    <div class="border-b border-[#E4DED5] mb-6">
        <div class="inline-block pb-3 border-b-2 border-[#2F5C4A] text-sm font-semibold text-[#1F1F1F]">
            Semua kelompok ({{ $kelompokList->total() }})
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <form method="GET" action="{{ route('dosen.kelompok.index') }}" class="mb-6">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Input Search -->
            <div class="relative w-full md:w-1/2">
                <span class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none text-[#8C7A6B]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama kelompok atau proyek..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-[#E4DED5] rounded-xl text-xs text-[#1F1F1F] placeholder-[#9C9288] focus:outline-none focus:border-[#2F5C4A]">
            </div>

            <!-- Select Filters -->
            <div class="flex items-center gap-3 flex-wrap">
                <select name="kelas" onchange="this.form.submit()" class="bg-white border border-[#E4DED5] text-[#1F1F1F] text-xs font-medium rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-[#2F5C4A] cursor-pointer">
                    <option value="">Semua Kelas</option>
                    @foreach(($daftarKelas ?? ['SIB 3A', 'SIB 3B']) as $kls)
                        <option value="{{ $kls }}" {{ request('kelas') == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="bg-white border border-[#E4DED5] text-[#1F1F1F] text-xs font-medium rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-[#2F5C4A] cursor-pointer">
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
            @php
                $namaKelas = is_object($item->kelas) ? ($item->kelas->nama_kelas ?? null) : $item->kelas;
                $statusClass = match($item->status ?? 'Berjalan') {
                    'Review'    => 'bg-[#FFF7ED] text-[#C2410C] border-[#FED7AA]',
                    'Terlambat' => 'bg-[#FDF2F2] text-[#B91C1C] border-[#FECACA]',
                    default     => 'bg-[#EAF3EC] text-[#2F5C4A] border-[#C6DFC8]',
                };
            @endphp

            <div class="bg-white border border-[#E4DED5] rounded-2xl p-6 shadow-2xs hover:shadow-md transition-shadow flex flex-col justify-between">
                <div>
                    <!-- Header Kartu -->
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-[#EAF3EC] text-[#2F5C4A] flex items-center justify-center flex-shrink-0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-[#1F1F1F] text-base leading-snug">{{ $item->nama_kelompok ?? 'Kelompok' }}</h3>
                                    @if($namaKelas)
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-[#F5F1EB] text-[#6E5B3E]">
                                            {{ $namaKelas }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-[#7D746D] mt-0.5">{{ $item->judul_proyek ?? 'Sistem Informasi Monitoring PBL' }}</p>
                            </div>
                        </div>

                        <span class="text-[11px] font-semibold px-3 py-1 rounded-full border flex-shrink-0 {{ $statusClass }}">
                            {{ $item->status ?? 'Berjalan' }}
                        </span>
                    </div>

                    <!-- Info Grid 3 Kolom -->
                    <div class="bg-[#FBF9F5] border border-[#EFEAE2] rounded-xl p-3.5 grid grid-cols-3 gap-2 text-xs mb-4">
                        <div>
                            <span class="text-[#8C7A6B] text-[11px] block mb-0.5">Anggota</span>
                            <span class="font-bold text-[#1F1F1F]">{{ $item->anggotaKelompok->count() }} mahasiswa</span>
                        </div>
                        <div>
                            <span class="text-[#8C7A6B] text-[11px] block mb-0.5">Catatan logbook</span>
                            <span class="font-bold text-[#1F1F1F]">{{ $item->logbookMilestone->count() }} catatan</span>
                        </div>
                        <div>
                            <span class="text-[#8C7A6B] text-[11px] block mb-0.5">Aktivitas terakhir</span>
                            <span class="font-bold text-[#1F1F1F]">{{ $item->updated_at ? $item->updated_at->format('d M Y') : '25 Okt 2026' }}</span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-5">
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-[#7D746D]">Ringkasan catatan</span>
                            <span class="font-bold text-[#1F1F1F]">{{ $item->progress_dummy }}%</span>
                        </div>
                        <div class="w-full bg-[#E9E1D5] rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#2F5C4A] h-1.5 rounded-full" style="width: {{ $item->progress_dummy }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Detail -->
                <div class="text-right">
                    <a href="{{ route('dosen.kelompok.show', $item->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#2F5C4A] hover:bg-[#234739] text-white text-xs font-semibold rounded-lg transition-colors">
                        Lihat detail &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 text-center py-12 bg-white rounded-2xl border border-dashed border-[#CFC7BC]">
                <p class="text-[#7D746D] text-sm">Tidak ada kelompok bimbingan yang ditemukan.</p>
                <a href="{{ route('dosen.kelompok.index') }}" class="inline-block mt-2 text-xs font-semibold text-[#2F5C4A] underline">
                    Reset filter
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($kelompokList->hasPages())
        <div class="mb-8">
            {{ $kelompokList->links() }}
        </div>
    @endif

    <!-- Alert Catatan Bimbingan Aktif -->
    <div class="bg-[#2B221D] text-white p-4 sm:p-5 rounded-2xl flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-[#3D312A] border border-[#52433A] flex items-center justify-center text-[#E3C184] font-bold flex-shrink-0">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] uppercase tracking-wider text-[#B8A99A] font-bold block">CATATAN BIMBINGAN AKTIF</span>
                <p class="text-xs text-[#F3EFEA] mt-0.5"><strong>Sinyal perhatian:</strong> Kelompok 02 memerlukan review berkala pada logbook sprint 3.</p>
            </div>
        </div>
        <a href="{{ route('dosen.kelompok.index') }}" class="bg-[#EAD4A7] hover:bg-[#DFC38B] text-[#2B221D] text-xs font-bold px-4 py-2.5 rounded-xl transition-colors flex-shrink-0">
            Kirim pengingat &rarr;
        </a>
    </div>

</div>
@endsection