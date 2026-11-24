@extends('layouts.mahasiswa')

@section('title', 'Notifikasi')
@section('breadcrumb', 'Notifikasi')
@section('meta_description', 'Pusat aktivitas dan pemberitahuan SIM-PBL')

@push('styles')
<style>
/* Page-scoped responsive layout */
.notif-shell { padding: 0 32px 40px; }
.notif-summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
.notif-sum { padding: 20px 24px; }
.notif-filter { padding: 18px 24px; }
.notif-tabs { display: flex; flex-wrap: wrap; background: #F2ECE4; padding: 4px; border-radius: 20px; gap: 4px; }
.notif-search { position: relative; min-width: min(280px, 100%); }
.notif-card { padding: 20px 24px; }
.notif-item__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
.notif-item__main { display: flex; align-items: flex-start; gap: 14px; min-width: 0; }
.notif-item__main > div:last-child { min-width: 0; }
.notif-item__main h4,
.notif-item__main p { overflow-wrap: anywhere; }
.notif-title-row { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 4px; }
.notif-actions { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px 16px; }
.notif-actions a,
.notif-actions button { overflow-wrap: anywhere; }
.notif-time { white-space: nowrap; }
.notif-markall[disabled],
.notif-action-btn[disabled] { opacity: 0.7; cursor: not-allowed; }
.notif-card.is-read {
    background: #F9F7F5 !important;
    border-color: var(--sim-border) !important;
    opacity: 0.78;
}
.notif-card.is-read .notif-unread-dot {
    display: none !important;
}
.notif-card.is-read .notif-title-row h4,
.notif-card.is-read .notif-item__main p,
.notif-card.is-read .notif-time,
.notif-card.is-read .notif-actions a,
.notif-card.is-read .notif-actions span {
    color: #8A8A8A !important;
}
.notif-card.is-read .notif-item__main > div:first-child {
    background: #EBE5DC !important;
    color: var(--sim-text-muted) !important;
}
.notif-card.is-read .notif-title-row span:first-child {
    background: #EBE5DC !important;
    color: var(--sim-text-muted) !important;
}
@media (max-width: 900px) {
    .notif-summary { grid-template-columns: 1fr; gap: 12px; }
}
@media (max-width: 768px) {
    .notif-shell { padding: 0 20px 32px; }
    .notif-tab,
    .notif-pill,
    .notif-markall,
    .notif-actions a,
    .notif-actions button { min-height: 44px; }
    .notif-tab,
    .notif-pill { display: inline-flex; align-items: center; justify-content: center; }
    .notif-actions a,
    .notif-actions button { display: inline-flex; align-items: center; }
    .notif-search input { min-height: 44px; }
}
@media (max-width: 600px) {
    .notif-item__top { flex-direction: column; align-items: flex-start; gap: 8px; }
    .notif-time { white-space: normal; }
    .notif-actions { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 480px) {
    .notif-shell { padding: 0 16px 32px; }
    .notif-sum { padding: 16px; }
    .notif-filter { padding: 14px; }
    .notif-card { padding: 16px; }
    .notif-tabs { flex-direction: column; width: 100%; border-radius: 16px; }
    .notif-tab { width: 100%; text-align: left; justify-content: flex-start; }
}
</style>
@endpush

@section('content')
@if(session('success'))
    <div style="margin: 0 32px 20px; padding: 14px 20px; background: #EBF7EE; border: 1px solid #B8E4C4; border-radius: 12px; color: #1E6B37; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- Page Header --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">PUSAT AKTIVITAS</p>
        <h1 class="sim-page-header__title">Notifikasi</h1>
        <p class="sim-page-header__desc">Pantau pembaruan proposal, bimbingan, logbook, dan perkembangan proyek kelompokmu.</p>
    </div>
    <div class="sim-page-header__right">
        <form action="{{ route('mahasiswa.notifikasi.baca-semua') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="sim-btn-secondary notif-markall" style="border: 1px solid var(--sim-border); border-radius: 20px; padding: 8px 16px; background: #fff; gap: 8px; font-size: 13px; display: inline-flex; align-items: center; cursor: pointer;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Tandai semua dibaca
            </button>
        </form>
    </div>
</div>

{{-- Main Container --}}
<div class="notif-shell">

    {{-- Daftar Notifikasi (List Cards) --}}
    <div style="display: flex; flex-direction: column; gap: 12px;">

        @forelse($notifikasiList ?? [] as $item)
            @php
                $isRead = !empty($item->dibaca_pada);
                $judulLower = strtolower($item->judul);
                $category = 'Aktivitas';
                $catBg = '#EBE5DC';
                $catColor = 'var(--sim-text-muted)';
                $iconBg = '#EBE5DC';
                $iconColor = 'var(--sim-text-muted)';
                $actionLink = $item->tautan ?: route('mahasiswa.dashboard');

                if (str_contains($judulLower, 'proposal')) {
                    $category = 'Proposal';
                    if (str_contains($judulLower, 'revisi')) {
                        $catBg = '#FDF2F2'; $catColor = '#C0392B';
                        $iconBg = '#FDF2F2'; $iconColor = '#C0392B';
                    } else {
                        $catBg = '#EBF4EC'; $catColor = '#4A7C59';
                        $iconBg = '#EBF4EC'; $iconColor = '#4A7C59';
                    }
                    $actionLink = $item->tautan ?: route('mahasiswa.proposal');
                } elseif (str_contains($judulLower, 'logbook')) {
                    $category = 'Logbook';
                    if (str_contains($judulLower, 'revisi')) {
                        $catBg = '#FDF2F2'; $catColor = '#C0392B';
                        $iconBg = '#FDF2F2'; $iconColor = '#C0392B';
                    } else {
                        $catBg = '#EBF4EC'; $catColor = '#4A7C59';
                        $iconBg = '#EBF4EC'; $iconColor = '#4A7C59';
                    }
                    $actionLink = $item->tautan ?: route('mahasiswa.logbook');
                } elseif (str_contains($judulLower, 'milestone')) {
                    $category = 'Milestone';
                    $catBg = '#EDE8DE'; $catColor = '#7A5C4A';
                    $iconBg = '#EDE8DE'; $iconColor = '#7A5C4A';
                    $actionLink = $item->tautan ?: route('mahasiswa.milestone');
                } elseif (str_contains($judulLower, 'bimbingan')) {
                    $category = 'Bimbingan';
                    $catBg = '#FFF9E6'; $catColor = '#8A6A00';
                    $iconBg = '#FFF9E6'; $iconColor = '#8A6A00';
                }
            @endphp

            <div class="notif-card {{ $isRead ? 'is-read' : '' }}" style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
                <div class="notif-item__top">
                    <div class="notif-item__main">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $iconBg }}; color: {{ $iconColor }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                            @if(str_contains($judulLower, 'proposal'))
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            @elseif(str_contains($judulLower, 'logbook'))
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            @elseif(str_contains($judulLower, 'milestone'))
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            @else
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            @endif
                        </div>
                        <div>
                            <div class="notif-title-row">
                                <span style="font-size: 10px; font-weight: 700; background: {{ $catBg }}; color: {{ $catColor }}; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">{{ $category }}</span>
                                <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                                    {{ $item->judul }}
                                    @if(! $isRead)
                                        <span class="notif-unread-dot" style="width: 6px; height: 6px; background: #C0392B; border-radius: 50%; display: inline-block;"></span>
                                    @endif
                                </h4>
                            </div>
                            <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                                {{ $item->isi }}
                            </p>
                        </div>
                    </div>
                    <span class="notif-time" style="font-size: 12px; color: var(--sim-text-muted);">
                        {{ $item->created_at ? $item->created_at->diffForHumans() : '-' }}
                    </span>
                </div>
                <div class="notif-actions" style="border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                    <a href="{{ $actionLink }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        Buka tautan terkait &rarr;
                    </a>
                    @if(! $isRead)
                        <form action="{{ route('mahasiswa.notifikasi.baca', $item->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="notif-action-btn" style="border: none; background: transparent; color: var(--sim-text-muted); cursor: pointer; font-weight: 500;">
                                Tandai dibaca
                            </button>
                        </form>
                    @else
                        <span style="font-size: 12px; color: var(--sim-text-light);">Sudah dibaca</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 48px 20px; background: #FFFFFF; border-radius: 16px; border: 1.5px dashed var(--sim-border);">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A8A29E" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px; display: block;">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <h3 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 6px;">Belum Ada Notifikasi</h3>
                <p style="color: var(--sim-text-muted); font-size: 13px; margin: 0;">Pemberitahuan terkait proposal, logbook, dan milestone kelompok Anda akan muncul di sini.</p>
            </div>
        @endforelse

    </div>

</div>
@endsection
