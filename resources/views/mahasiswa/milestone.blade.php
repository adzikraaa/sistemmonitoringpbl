@extends('layouts.mahasiswa')

@section('title', 'Milestone - Pilih Mata Kuliah')
@section('meta_description', 'Pilih mata kuliah untuk melihat peta perjalanan dan detail pengumpulan milestone.')

@push('styles')
<style>
    .sim-badge--inprogress {
        background: #F4EAE1;
        color: #6E472B;
        border: 1px solid #DBC4AE;
    }
</style>
@endpush

@section('content')

@php
    $circleTone = function (array $item, bool $isFirst): string {
        return match ($item['status']) {
            'Selesai' => 'green',
            'Berjalan' => $isFirst ? 'green' : 'gold',
            'Menunggu review' => 'gold',
            'Perlu revisi' => 'red',
            default => 'gray',
        };
    };

    $circleToneClass = fn (string $tone): string => match ($tone) {
        'green' => 'bg-[#2F5C4A]',
        'gold' => 'bg-[#C28A3A]',
        'red' => 'bg-[#9B1C1C]',
        default => 'bg-[#9C9188]',
    };

    $badgeClass = fn (string $status): string => match ($status) {
        'Selesai' => 'sim-badge--approved',
        'Berjalan' => 'sim-badge--inprogress',
        'Menunggu review' => 'sim-badge--pending',
        'Perlu revisi' => 'sim-badge--danger',
        default => 'sim-badge--warning',
    };
@endphp

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
    <!-- Timeline List Mata Kuliah -->
    <div class="relative pl-8 space-y-6">
        @forelse($mataKuliahList as $item)
            <div class="relative flex items-center">
                {{-- Garis Penghubung antar-Card Mata Kuliah (menghubungkan tepat dari tengah ke tengah) --}}
                @if(!$loop->last)
                    <div class="pointer-events-none absolute -left-[17px] top-1/2 -bottom-6 w-0.5 bg-[#CFC7BC] z-0"></div>
                @endif

                <!-- Badge Bulat MK1, MK2, MK3 (Tepat di tengah vertikal card) -->
                @php($cTone = $circleTone($item, $loop->first))
                <span class="pointer-events-none absolute -left-8 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold text-white shadow-sm ring-4 ring-[#F6F4F1] z-10 {{ $circleToneClass($cTone) }}">
                    MK{{ $loop->iteration }}
                </span>

                <!-- Seluruh Card Mata Kuliah Dibuat Klikabel (Tag <a>) -->
                <a href="{{ route('mahasiswa.milestone.detail', $item['slug']) }}"
                   class="group block w-full bg-white border border-[#CFC7BC] hover:border-[#7A5C4A] rounded-2xl p-6 shadow-sm hover:shadow-md transition-all cursor-pointer no-underline relative z-10">
                    
                    <!-- Title & Status Badge -->
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-serif font-normal text-[#1F1F1F] group-hover:text-[#7A5C4A] transition-colors">
                            {{ $item['nama'] }}
                        </h2>
                        <span class="sim-badge {{ $badgeClass($item['status']) }}">
                            {{ $item['status'] }}
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4 space-y-1.5">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-[#7D746D] font-medium">Progres Mata Kuliah</span>
                            <span class="text-[#1F1F1F] font-semibold">
                                {{ $item['progress'] }}%
                                ({{ $item['selesai'] }}/{{ $item['total'] }} selesai{{ ($item['terkumpul'] ?? 0) > 0 && $item['selesai'] < $item['total'] ? ', '.$item['terkumpul'].' terkumpul' : '' }})
                            </span>
                        </div>
                        <div class="w-full bg-[#E9E1D5] rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $loop->first ? 'bg-[#2F5C4A]' : 'bg-[#7A5C4A]' }}" style="width: {{ $item['progress'] }}%"></div>
                        </div>
                        @if(! $item['punya_data'])
                            <p class="text-[11px] text-[#7D746D]">Belum ada data mapping milestone.</p>
                        @endif
                    </div>

                    <!-- Footer Card -->
                    <div class="flex items-center justify-between pt-4 border-t border-[#E4DED5] text-xs">
                        <span class="text-[#7D746D]">
                            Dosen Pengampu: <strong class="text-[#1F1F1F] font-semibold">{{ $item['dosen'] }}</strong>
                        </span>
                        <span class="text-[#7A5C4A] group-hover:text-[#2B221D] font-semibold inline-flex items-center gap-1 transition-colors">
                            Buka mata kuliah &rsaquo;
                        </span>
                    </div>

                </a>
            </div>
        @empty
            <div class="bg-white border border-dashed border-[#CFC7BC] rounded-2xl p-8 text-center">
                <h2 class="font-serif text-lg font-bold text-[#1F1F1F] mb-1">Belum ada mata kuliah</h2>
                <p class="text-xs text-[#7D746D]">Belum ada mata kuliah aktif dengan milestone pada periode kelompok.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection