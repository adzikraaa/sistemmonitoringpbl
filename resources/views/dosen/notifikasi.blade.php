@extends('layouts.dosen')

@section('title', 'Notifikasi - Ruang Dosen')
@section('breadcrumb', 'Notifikasi')

@push('styles')
<style>
.notif-container {
    padding: 28px 32px 56px;
    width: 100%;
    box-sizing: border-box;
    max-width: 720px;
}

.notif-eyebrow {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: #8C7A6A;
    margin: 0 0 6px;
}

.notif-title {
    font-family: 'DM Serif Display', serif;
    font-size: 32px;
    font-weight: 400;
    color: #1C1008;
    margin: 0 0 6px;
}

.notif-subtitle {
    font-size: 14px;
    color: #8C7A6A;
    margin: 0 0 28px;
}

.notif-card {
    background: #fff;
    border: 1px solid #E8DDD0;
    border-radius: 16px;
    overflow: hidden;
}

.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 18px 24px;
    border-bottom: 1px solid #F0EAE2;
    transition: background .1s;
}

.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background: #FAF7F3; }
.notif-item--unread { background: #FDFAF6; }

.notif-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
}

.notif-icon--logbook   { background: #E8F3EC; color: #2E7D4F; }
.notif-icon--proposal  { background: #FEF3E8; color: #B8934A; }
.notif-icon--pengingat { background: #FDECEA; color: #C0392B; }

.notif-body { flex: 1; min-width: 0; }

.notif-pesan {
    font-size: 13.5px;
    color: #1C1008;
    margin: 0 0 4px;
    line-height: 1.5;
}

.notif-waktu {
    font-size: 12px;
    color: #8C7A6A;
    margin: 0;
}

.notif-unread-dot {
    width: 8px;
    height: 8px;
    background: #B8934A;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 6px;
}

.notif-empty {
    padding: 48px 24px;
    text-align: center;
    color: #8C7A6A;
    font-size: 14px;
}
</style>
@endpush

@section('content')
<div class="notif-container">

    <p class="notif-eyebrow">RUANG DOSEN · NOTIFIKASI</p>
    <h1 class="notif-title">Notifikasi</h1>
    <p class="notif-subtitle">Pembaruan terbaru dari kelompok dan sistem.</p>

    <div class="notif-card">
        @forelse($notifikasi ?? [] as $item)
        @php
            $iconClass = 'notif-icon--' . ($item['jenis'] ?? 'logbook');
        @endphp
        <div class="notif-item {{ !($item['dibaca'] ?? true) ? 'notif-item--unread' : '' }}">
            <div class="notif-icon {{ $iconClass }}">
                @if(($item['jenis'] ?? '') === 'logbook')
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                @elseif(($item['jenis'] ?? '') === 'proposal')
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                @else
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                @endif
            </div>
            <div class="notif-body">
                <p class="notif-pesan">{{ $item['pesan'] }}</p>
                <p class="notif-waktu">
                    {{ $item['waktu'] instanceof \Carbon\Carbon ? $item['waktu']->diffForHumans() : $item['waktu'] }}
                </p>
            </div>
            @if(!($item['dibaca'] ?? true))
            <div class="notif-unread-dot"></div>
            @endif
        </div>
        @empty
        <div class="notif-empty">
            Tidak ada notifikasi saat ini.
        </div>
        @endforelse
    </div>

</div>
@endsection
