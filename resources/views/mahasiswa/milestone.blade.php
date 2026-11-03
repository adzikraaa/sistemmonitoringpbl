@extends('layouts.mahasiswa')

@section('title', 'Milestone - Pilih Mata Kuliah')
@section('meta_description', 'Pilih mata kuliah untuk melihat peta perjalanan dan detail pengumpulan milestone.')

@section('content')

{{-- ─── Page Header (Standar SIM-PBL) ────────────────────────────────────── --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">PETA PERJALANAN</p>
        <h1 class="sim-page-header__title">Milestone - Pilih Mata Kuliah</h1>
        <p class="sim-page-header__desc">
            Pilih mata kuliah untuk melihat peta perjalanan dan detail pengumpulan.
        </p>
    </div>
</div>

{{-- ─── Main Content Wrap ────────────────────────────────────────────────── --}}
<div style="padding: 0 32px 40px;">
    <!-- Timeline List Mata Kuliah (Diberi pl-8 & margin kiri agar ada jarak lega dari card) -->
    <div class="relative pl-8 space-y-6 before:absolute before:left-3.5 before:top-4 before:bottom-4 before:w-0.5 before:bg-[#CFC7BC]">
        @foreach($mataKuliahList as $item)
            <div class="relative flex items-start gap-4">
                
                <!-- Badge Bulat MK1, MK2, MK3 (Berada tepat di tengah garis vertikal dengan jarak manis ke card) -->
                <span class="absolute -left-8 top-3.5 w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow-sm ring-4 ring-[#F6F4F1] z-10 {{ $item['status'] == 'Berjalan' ? ($loop->first ? 'bg-[#2F5C4A]' : 'bg-[#D7B268]') : 'bg-[#A77E62]' }}">
                    {{ $item['kode'] }}
                </span>

                <!-- Card Mata Kuliah -->
                <div class="w-full bg-white border border-[#CFC7BC] rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    
                    <!-- Title & Status Badge -->
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-serif font-normal text-[#1F1F1F]">{{ $item['nama'] }}</h2>
                        <span class="sim-badge {{ $item['status'] == 'Berjalan' ? 'sim-badge--approved' : 'sim-badge--warning' }}">
                            &#9679; {{ $item['status'] }}
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4 space-y-1.5">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-[#7D746D] font-medium">Progres Mata Kuliah</span>
                            <span class="text-[#1F1F1F] font-semibold">{{ $item['progress'] }}%</span>
                        </div>
                        <div class="w-full bg-[#E9E1D5] rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $loop->first ? 'bg-[#2F5C4A]' : 'bg-[#7A5C4A]' }}" style="width: {{ $item['progress'] }}%"></div>
                        </div>
                    </div>

                    <!-- Footer Card -->
                    <div class="flex items-center justify-between pt-4 border-t border-[#E4DED5] text-xs">
                        <span class="text-[#7D746D]">
                            Dosen Pengampu: <strong class="text-[#1F1F1F] font-semibold">{{ $item['dosen'] }}</strong>
                        </span>
                        <a href="{{ route('mahasiswa.milestone.detail', $item['slug']) }}" class="text-[#7A5C4A] hover:text-[#2B221D] font-semibold inline-flex items-center gap-1 transition-colors">
                            Buka mata kuliah &rsaquo;
                        </a>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection