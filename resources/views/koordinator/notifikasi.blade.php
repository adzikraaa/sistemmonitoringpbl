@extends('layouts.koordinator')

@section('title', 'Notifikasi & Aktivitas Sistem')

@push('styles')
<style>
    .notif-container {
        padding: 8px 10px 48px;
        width: 100%;
        max-width: 980px;
        box-sizing: border-box;
    }

    .notif-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .notif-eyebrow {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #8C7A6A;
        margin: 0 0 4px;
    }

    .notif-title {
        font-family: 'DM Serif Display', Georgia, serif;
        font-size: 28px;
        font-weight: 400;
        color: #2D2621;
        margin: 0 0 6px;
    }

    .notif-desc {
        font-size: 13.5px;
        color: #7D7063;
        margin: 0;
    }

    /* Summary Stats Grid */
    .notif-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .notif-stat-card {
        background: #FFFFFF;
        border: 1px solid #E8E2D8;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .notif-stat-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8C7A6A;
        margin: 0 0 6px;
    }

    .notif-stat-value {
        font-family: 'DM Serif Display', Georgia, serif;
        font-size: 26px;
        color: #2D2621;
        margin: 0;
        line-height: 1.1;
    }

    /* Filter & Search Toolbar */
    .notif-toolbar {
        background: #FFFFFF;
        border: 1px solid #E8E2D8;
        border-radius: 14px;
        padding: 12px 18px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .notif-tabs {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .notif-tab-btn {
        background: transparent;
        border: none;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #7D7063;
        cursor: pointer;
        transition: all 0.15s ease;
        font-family: inherit;
    }

    .notif-tab-btn:hover {
        background: #F4EFEA;
        color: #2D2621;
    }

    .notif-tab-btn.active {
        background: #2D2621;
        color: #FFFFFF;
    }

    /* Notification Items List */
    .notif-list {
        background: #FFFFFF;
        border: 1px solid #E8E2D8;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.02);
    }

    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 18px 22px;
        border-bottom: 1px solid #F0EAE2;
        transition: background 0.15s ease;
    }

    .notif-item:last-child {
        border-bottom: none;
    }

    .notif-item:hover {
        background: #FAF8F5;
    }

    .notif-item--unread {
        background: #FDFBF8;
    }

    .notif-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .notif-icon--proposal {
        background: #FEF3E8;
        color: #B87333;
    }

    .notif-icon--dosen {
        background: #E8F1F5;
        color: #2B6CB0;
    }

    .notif-icon--milestone {
        background: #EAF3EC;
        color: #2F6B45;
    }

    .notif-icon--sistem {
        background: #F5EFEB;
        color: #6E5B4B;
    }

    .notif-content {
        flex: 1;
        min-width: 0;
    }

    .notif-content-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 4px;
        flex-wrap: wrap;
    }

    .notif-badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 2px 8px;
        border-radius: 6px;
    }

    .notif-badge--proposal { background: #FEF3E8; color: #B87333; }
    .notif-badge--penugasan { background: #E8F1F5; color: #2B6CB0; }
    .notif-badge--kelompok { background: #EAF3EC; color: #2F6B45; }
    .notif-badge--sistem { background: #F5EFEB; color: #6E5B4B; }

    .notif-item-title {
        font-size: 14px;
        font-weight: 700;
        color: #2D2621;
        margin: 0;
    }

    .notif-item-msg {
        font-size: 13px;
        color: #63584E;
        line-height: 1.5;
        margin: 4px 0 10px;
    }

    .notif-content-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .notif-time {
        font-size: 11.5px;
        color: #9C8E80;
    }

    .notif-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #7A5129;
        text-decoration: none;
        padding: 4px 10px;
        background: #F8F4EE;
        border-radius: 6px;
        transition: all 0.15s ease;
        border: 0;
        cursor: pointer;
        font-family: inherit;
    }

    .notif-action-btn:hover {
        background: #EFE8DE;
        color: #4A3118;
    }

    .notif-dot {
        width: 8px;
        height: 8px;
        background: #C0392B;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 6px;
    }

    .notif-empty {
        padding: 56px 24px;
        text-align: center;
        color: #8C7A6A;
    }

    @media (max-width: 768px) {
        .notif-stats {
            grid-template-columns: 1fr;
        }
        .notif-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
@endpush

@section('content')
<div class="notif-container">

    {{-- Success Alert --}}
    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 12px 18px; background: #EBF7EE; border: 1px solid #B8E4C4; border-radius: 12px; color: #1E6B37; font-size: 13px; display: flex; align-items: center; gap: 10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="notif-header">
        <div>
            <p class="notif-eyebrow">RUANG KOORDINATOR · PUSAT PEMBERITAHUAN</p>
            <h1 class="notif-title">Notifikasi & Aktivitas Sistem</h1>
            <p class="notif-desc">Pantau aktivitas review, revisi, persetujuan, dan pembaruan progres yang dilakukan dosen.</p>
        </div>

        @if($unreadCount > 0)
            <form action="{{ route('koordinator.notifikasi.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" style="display: inline-flex; align-items: center; gap: 6px; background: #FFFFFF; border: 1px solid #D6CEC3; border-radius: 999px; padding: 8px 16px; font-size: 12px; font-weight: 600; color: #2D2621; cursor: pointer; transition: all 0.15s ease; font-family: inherit;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="notif-stats">
        <div class="notif-stat-card">
            <p class="notif-stat-label">Total Notifikasi</p>
            <p class="notif-stat-value">{{ $totalNotif }}</p>
        </div>
        <div class="notif-stat-card">
            <p class="notif-stat-label">Belum Dibaca</p>
            <p class="notif-stat-value" style="color: {{ $unreadCount > 0 ? '#C0392B' : '#2D2621' }};">{{ $unreadCount }}</p>
        </div>
        <div class="notif-stat-card">
            <p class="notif-stat-label">Menunggu Tindakan</p>
            <p class="notif-stat-value" style="color: #7A5129;">{{ $actionCount }}</p>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="notif-toolbar">
        <div class="notif-tabs">
            <button type="button" class="notif-tab-btn active" onclick="filterNotif('all', this)">Semua</button>
            <button type="button" class="notif-tab-btn" onclick="filterNotif('unread', this)">Belum Dibaca ({{ $unreadCount }})</button>
            <button type="button" class="notif-tab-btn" onclick="filterNotif('proposal', this)">Proposal</button>
            <button type="button" class="notif-tab-btn" onclick="filterNotif('penugasan', this)">Dosen Penilai</button>
            <button type="button" class="notif-tab-btn" onclick="filterNotif('kelompok', this)">Milestone</button>
            <button type="button" class="notif-tab-btn" onclick="filterNotif('sistem', this)">Sistem</button>
        </div>

        <span style="font-size: 12px; color: #8C7A6A;" id="notifCounterText">Menampilkan {{ count($notifikasi) }} notifikasi</span>
    </div>

    {{-- Notifications List --}}
    <div class="notif-list" id="notifListContainer">
        @forelse($notifikasi as $item)
            @php
                $catClass = match($item['kategori']) {
                    'proposal'  => 'proposal',
                    'penugasan' => 'penugasan',
                    'kelompok'  => 'kelompok',
                    default     => 'sistem',
                };
            @endphp
            <div class="notif-item {{ !$item['is_read'] ? 'notif-item--unread' : '' }}" 
                 data-kategori="{{ $item['kategori'] }}" 
                 data-read="{{ $item['is_read'] ? 'true' : 'false' }}">
                
                {{-- Icon --}}
                <div class="notif-icon-wrap notif-icon--{{ $catClass }}">
                    @if($item['icon_type'] === 'proposal')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        </svg>
                    @elseif($item['icon_type'] === 'dosen')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    @elseif($item['icon_type'] === 'milestone')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    @else
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                    @endif
                </div>

                {{-- Content Body --}}
                <div class="notif-content">
                    <div class="notif-content-top">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="notif-badge notif-badge--{{ $catClass }}">{{ $item['kategori_label'] }}</span>
                            <h3 class="notif-item-title">{{ $item['judul'] }}</h3>
                        </div>
                    </div>

                    <p class="notif-item-msg">{{ $item['pesan'] }}</p>

                    <div class="notif-content-bottom">
                        <span class="notif-time">{{ $item['waktu'] }}</span>

                        @if(!empty($item['action_url']))
                            <form method="POST" action="{{ route('koordinator.notifikasi.mark-read', $item['id']) }}">
                                @csrf
                                <button type="submit" class="notif-action-btn">
                                    <span>{{ $item['action_label'] }}</span>
                                    <span style="font-size: 13px;">→</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Dot unread --}}
                @if(!$item['is_read'])
                    <div class="notif-dot" title="Belum dibaca"></div>
                @endif
            </div>
        @empty
            <div class="notif-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A3978A" stroke-width="1.5" style="margin: 0 auto 12px; display: block;">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <p style="font-size: 15px; font-weight: 600; color: #2D2621; margin: 0 0 4px;">Tidak ada notifikasi</p>
                <p style="font-size: 13px; color: #8C7A6A; margin: 0;">Semua aktivitas sistem telah diperbarui dan tidak ada pemberitahuan baru.</p>
            </div>
        @endforelse
    </div>

</div>

<script>
function filterNotif(type, btn) {
    // Active class
    document.querySelectorAll('.notif-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const items = document.querySelectorAll('.notif-item');
    let visibleCount = 0;

    items.forEach(item => {
        const cat = item.getAttribute('data-kategori');
        const isRead = item.getAttribute('data-read') === 'true';

        let show = false;
        if (type === 'all') {
            show = true;
        } else if (type === 'unread') {
            show = !isRead;
        } else {
            show = (cat === type);
        }

        if (show) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    const counter = document.getElementById('notifCounterText');
    if (counter) {
        counter.textContent = 'Menampilkan ' + visibleCount + ' notifikasi';
    }
}
</script>
@endsection

