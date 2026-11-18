@extends('layouts.mahasiswa')

@section('title', 'Kelompok')
@section('meta_description', 'Ruang kolaborasi kelompok mahasiswa SIM-PBL')

@push('styles')
<style>
/* Page-scoped responsive layout */
.klp-shell { padding: 0 32px 40px; }
.klp-banner { padding: 40px; margin-bottom: 32px; max-width: 712px; }
.klp-banner__title { font-size: clamp(22px, 4.4vw, 32px); }
.klp-banner-meta { display: flex; }
.klp-banner-meta__item { min-width: 266px; }
.klp-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 32px; align-items: start; }
.klp-card { padding: 28px; }
.klp-member { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 0; min-width: 0; }
.klp-member--bordered { border-bottom: 1px solid var(--sim-border-light); }
.klp-member__id { display: flex; align-items: center; gap: 14px; min-width: 0; }
.klp-avatar { flex-shrink: 0; }
.klp-member__txt { min-width: 0; }
.klp-member__txt p { overflow-wrap: anywhere; }
.klp-msg-btn { display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
@media (max-width: 900px) {
    .klp-grid { grid-template-columns: 1fr; gap: 24px; }
}
@media (max-width: 768px) {
    .klp-shell { padding: 0 20px 32px; }
    .klp-banner { padding: 24px; margin-bottom: 24px; max-width: none; }
    .klp-card { padding: 24px; }
    .klp-msg-btn { min-width: 44px; min-height: 44px; }
    .klp-link { min-height: 44px; }
}
@media (max-width: 600px) {
    .klp-banner-meta { flex-direction: column; gap: 20px; }
    .klp-banner-meta__item { min-width: 0; }
}
@media (max-width: 480px) {
    .klp-shell { padding: 0 16px 32px; }
    .klp-card { padding: 20px; }
}
</style>
@endpush

@section('content')

<div style="font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;">

{{-- Page Header Section --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">KELOMPOK SAYA</p>
        <h1 class="sim-page-header__title">{{ $kelompok?->nama_kelompok ?? 'Belum ada kelompok' }}</h1>
        <p class="sim-page-header__desc">Ruang kolaborasi untuk menjaga proyek tetap bergerak.</p>
    </div>
</div>

{{-- Main Container --}}
<div class="klp-shell">

    {{-- Banner Milestone Selanjutnya (lebar dibatasi, ada bayangan) --}}
    <div class="klp-banner" style="background: linear-gradient(135deg, #3A291F 0%, #34241A 100%); border-radius: 24px; color: #FAF7F3; box-shadow: 0 18px 40px rgba(58,38,26,0.20);">

        <p style="font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: #A08C7D; margin: 0 0 8px;">
            MILESTONE PERIODE
        </p>

        <h2 class="klp-banner__title" style="font-family: 'DM Serif Display', serif; font-weight: 400; margin: 0 0 20px; color: #FAF7F3; line-height: 1.2;">
            {{ $nextMilestone ? $nextMilestone->kode.' - '.$nextMilestone->judul : 'Belum ada milestone' }}
        </h2>

        <div class="klp-banner-meta" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 24px;">
            <div class="klp-banner-meta__item">
                <p style="font-size: 10px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: #A08C7D; margin: 0 0 6px;">
                    JADWAL MILESTONE
                </p>
                <p style="font-size: 14px; font-weight: 500; margin: 0; color: #FAF7F3;">
                    {{ $nextMilestone?->deadline?->translatedFormat('l, d F Y') ?? 'Belum ditetapkan' }}
                </p>
            </div>

            <div>
                <p style="font-size: 10px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: #A08C7D; margin: 0 0 6px;">
                    PROGRES PROJECT
                </p>
                <p style="font-size: 14px; font-weight: 500; margin: 0; color: #FAF7F3;">
                    {{ $progres['persen'] }}%
                </p>
            </div>
        </div>
    </div>

    {{-- Grid 2 Kolom --}}
    <div class="klp-grid">

        {{-- CARD KIRI: Anggota Kelompok --}}
        <div class="klp-card" style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 28px; box-shadow: 0 8px 24px rgba(58,38,26,0.05);">

            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 20px; border-bottom: 1px solid var(--sim-border-light);">
                <div>
                    <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sim-text-muted); margin: 0 0 4px;">
                        ORANG-ORANG DI DALAMNYA
                    </p>
                    <h2 style="font-family: 'DM Serif Display', serif; font-size: 24px; font-weight: 400; color: var(--sim-text-dark); margin: 0;">
                        Anggota kelompok
                    </h2>
                </div>

                <div style="color: var(--sim-text-light);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.75"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>

            {{-- List Anggota --}}
            <div style="display: flex; flex-direction: column; padding-top: 0;">
                @forelse ($anggota as $a)
                    <div class="klp-member {{ $loop->last ? '' : 'klp-member--bordered' }}">
                        <div class="klp-member__id">
                            <div class="klp-avatar" style="width: 38px; height: 38px; border-radius: 50%; background: #EBE1CC; color: #6E5B3E; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                {{ $a['inisial'] }}
                            </div>
                            <div class="klp-member__txt">
                                <p style="font-size: 14px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 1px;">
                                    Anggota {{ $loop->iteration }} &mdash; {{ $a['nama'] }}
                                </p>
                                <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0;">
                                    {{ $a['peran'] }} &bull; {{ $a['nim'] }}
                                </p>
                            </div>
                        </div>
                        <button type="button" class="klp-msg-btn" aria-label="Pesan {{ $a['nama'] }}"
                            style="border: none; background: transparent; color: var(--sim-text-light); cursor: pointer; padding: 6px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.75"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                <line x1="8.5" y1="10.5" x2="15.5" y2="10.5"></line>
                                <line x1="8.5" y1="13.5" x2="13.5" y2="13.5"></line>
                            </svg>
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-[#7D746D] py-4">Belum ada anggota kelompok.</p>
                @endforelse
            </div>
        </div>

        {{-- CARD KANAN: Titik Fokus (tinggi mengikuti isi) --}}
        <div class="klp-card" style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 28px; box-shadow: 0 8px 24px rgba(58,38,26,0.05);">

            <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sim-text-muted); margin: 0 0 4px;">
                TITIK FOKUS
            </p>

            <h2 style="font-family: 'DM Serif Display', serif; font-size: 24px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 18px;">
                Implementasi
            </h2>

            <p style="font-size: 13px; color: var(--sim-text-body); line-height: 1.45; margin: 0 0 24px;">
                Pekerjaan kelompok saat ini adalah menerjemahkan temuan lapangan menjadi prototipe yang bisa dipelajari bersama.
            </p>

            {{-- Progress Bar --}}
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--sim-text-muted); margin-bottom: 8px;">
                    <span>Progress keseluruhan</span>
                    <span style="font-weight: 500;">
                        {{ $progres['persen'] }}%
                        ({{ $progres['selesai'] }}/{{ $progres['total'] }})
                    </span>
                </div>

                <div style="width: 100%; height: 8px; background: #E8E2D9; border-radius: 999px; overflow: hidden;">
                    <div style="width: {{ $progres['persen'] }}%; height: 100%; background: #3F6B4E; border-radius: 999px;"></div>
                </div>
                @if(! $progres['punya_data'])
                    <p style="font-size: 11px; color: var(--sim-text-muted); margin: 8px 0 0;">Belum ada data</p>
                @endif
            </div>

            {{-- Aktivitas Tercatat --}}
            <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--sim-text-body); margin-bottom: 34px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="#3F6B4E" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
                <span>{{ $logbookStats['total'] }} aktivitas tercatat</span>
            </div>

            {{-- Link Buka Catatan Proses --}}
            <a href="{{ route('mahasiswa.logbook') }}" class="klp-link"
                style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: #5C3317; text-decoration: none;">
                Buka catatan proses &rarr;
            </a>
        </div>

    </div>
</div>

</div>

@endsection