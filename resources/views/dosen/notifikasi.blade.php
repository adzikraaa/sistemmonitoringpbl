@extends('layouts.dosen')

@section('title', 'Notifikasi')
@section('breadcrumb', 'Notifikasi')
@section('meta_description', 'Pusat aktivitas dan pemberitahuan bimbingan SIM-PBL')

@push('styles')
<style>
/* Page-scoped responsive layout matching Notifikasi Mahasiswa */
.notif-shell { padding: 0 32px 40px; }
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

@media (max-width: 768px) {
    .notif-shell { padding: 0 20px 32px; }
    .notif-markall,
    .notif-actions a,
    .notif-actions button { min-height: 44px; }
    .notif-actions a,
    .notif-actions button { display: inline-flex; align-items: center; }
}
@media (max-width: 600px) {
    .notif-item__top { flex-direction: column; align-items: flex-start; gap: 8px; }
    .notif-time { white-space: normal; }
    .notif-actions { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 480px) {
    .notif-shell { padding: 0 16px 32px; }
    .notif-card { padding: 16px; }
}
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">PUSAT AKTIVITAS</p>
        <h1 class="sim-page-header__title">Notifikasi</h1>
        <p class="sim-page-header__desc">Pantau pembaruan proposal, bimbingan, logbook, dan perkembangan proyek kelompokmu.</p>
    </div>
    <div class="sim-page-header__right">
        <button type="button" class="sim-btn-secondary notif-markall" data-mark-all-read style="border: 1px solid var(--sim-border); border-radius: 20px; padding: 8px 16px; background: #fff; gap: 8px; font-size: 13px; display: inline-flex; align-items: center; cursor: pointer;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Tandai semua dibaca
        </button>
    </div>
</div>

{{-- Main Container --}}
<div class="notif-shell">

    {{-- Daftar Notifikasi (List Cards) --}}
    <div style="display: flex; flex-direction: column; gap: 12px;">

        @forelse($notifikasi ?? [] as $index => $item)
            @php
                $id = $item['id'] ?? ('notif-' . $index);
                $jenis = strtolower($item['jenis'] ?? 'logbook');
                $isRead = !empty($item['dibaca']);
                $judul = $item['judul'] ?? match($jenis) {
                    'proposal'  => 'Proposal kelompok',
                    'bimbingan' => 'Jadwal bimbingan',
                    'logbook'   => 'Logbook kelompok',
                    'milestone' => 'Milestone proyek',
                    default     => 'Pemberitahuan sistem',
                };
                $pesan = $item['pesan'] ?? '';
                $waktu = $item['waktu'] instanceof \Carbon\Carbon ? $item['waktu']->diffForHumans() : ($item['waktu'] ?? 'Baru saja');
                $link = $item['link'] ?? match($jenis) {
                    'proposal'  => route('dosen.laporan-aktivitas'),
                    'logbook'   => route('dosen.laporan-aktivitas'),
                    default     => route('dosen.kelompok'),
                };
                $linkText = $item['link_text'] ?? match($jenis) {
                    'proposal'  => 'Buka halaman proposal',
                    'bimbingan' => 'Lihat detail jadwal bimbingan',
                    'logbook'   => 'Buka halaman logbook',
                    'milestone' => 'Buka halaman milestone',
                    default     => 'Lihat detail',
                };

                $badgeLabel = match($jenis) {
                    'proposal'  => 'Proposal',
                    'bimbingan' => 'Bimbingan',
                    'logbook'   => 'Logbook',
                    'milestone' => 'Milestone',
                    default     => ucfirst($jenis),
                };

                $iconBg = match($jenis) {
                    'proposal'  => '#EBF4EC',
                    'bimbingan' => '#FFF9E6',
                    'logbook'   => '#FDF2F2',
                    'milestone' => '#EBE5DC',
                    default     => '#EBE5DC',
                };

                $iconColor = match($jenis) {
                    'proposal'  => '#4A7C59',
                    'bimbingan' => '#8A6A00',
                    'logbook'   => '#C0392B',
                    'milestone' => 'var(--sim-text-muted)',
                    default     => 'var(--sim-text-muted)',
                };
            @endphp

            <div class="notif-card {{ $isRead ? 'is-read' : '' }}" data-notif-id="{{ $id }}" style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
                <div class="notif-item__top">
                    <div class="notif-item__main">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $iconBg }}; color: {{ $iconColor }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                            @if($jenis === 'proposal')
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @elseif($jenis === 'bimbingan')
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            @elseif($jenis === 'logbook')
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            @else
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            @endif
                        </div>
                        <div>
                            <div class="notif-title-row">
                                <span style="font-size: 10px; font-weight: 700; background: {{ $iconBg }}; color: {{ $iconColor }}; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">{{ $badgeLabel }}</span>
                                <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                                    {{ $judul }}
                                    <span class="notif-unread-dot" style="width: 6px; height: 6px; background: #C0392B; border-radius: 50%; display: {{ $isRead ? 'none' : 'inline-block' }};"></span>
                                </h4>
                            </div>
                            <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                                {{ $pesan }}
                            </p>
                        </div>
                    </div>
                    <span class="notif-time" style="font-size: 12px; color: var(--sim-text-muted);">{{ $waktu }}</span>
                </div>
                <div class="notif-actions" style="border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                    <a href="{{ $link }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                        {{ $linkText }} &rarr;
                    </a>
                    @if($isRead)
                        <span style="font-size: 12px; color: #7B7B7B; font-weight: 500;">Sudah dibaca</span>
                    @else
                        <button type="button" class="notif-action-btn" data-mark-read style="border: none; background: transparent; color: var(--sim-text-muted); cursor: pointer; font-weight: 500;">Tandai dibaca</button>
                    @endif
                </div>
            </div>
        @empty
            <div style="padding: 48px 24px; text-align: center; color: var(--sim-text-muted); background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px;">
                <p style="font-size: 14px; margin: 0 0 6px; font-weight: 500; color: var(--sim-text-dark);">Tidak ada notifikasi saat ini.</p>
                <p style="font-size: 12.5px; margin: 0;">Seluruh pembaruan bimbingan akan ditampilkan di sini.</p>
            </div>
        @endforelse

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const storageKey = 'sim_dosen_notifikasi_read';

    const getSavedReads = () => {
        try {
            return JSON.parse(localStorage.getItem(storageKey) || '[]');
        } catch (error) {
            return [];
        }
    };

    const saveReads = (ids) => {
        localStorage.setItem(storageKey, JSON.stringify(ids));
    };

    const applyReadState = (card, isRead) => {
        if (!card) return;

        const id = card.dataset.notifId;
        if (!id) return;

        const button = card.querySelector('[data-mark-read]');
        const actionSpan = card.querySelector('.notif-actions span');

        if (isRead) {
            card.classList.add('is-read');
            if (button) {
                button.disabled = true;
                button.textContent = 'Sudah dibaca';
                button.style.color = '#7B7B7B';
            }
            if (actionSpan) {
                actionSpan.textContent = 'Sudah dibaca';
                actionSpan.style.color = '#7B7B7B';
            }
        } else {
            card.classList.remove('is-read');
            if (button) {
                button.disabled = false;
                button.textContent = 'Tandai dibaca';
                button.style.color = 'var(--sim-text-muted)';
            }
        }
    };

    const refreshFromStorage = () => {
        const readIds = new Set(getSavedReads());
        document.querySelectorAll('.notif-card').forEach((card) => {
            const id = card.dataset.notifId;
            const isInitiallyRead = card.classList.contains('is-read');
            applyReadState(card, id ? readIds.has(id) || isInitiallyRead : isInitiallyRead);
        });
    };

    const setAsRead = (button) => {
        if (!button) return;

        const card = button.closest('.notif-card');
        const id = card?.dataset.notifId;
        if (!id) return;

        const saved = new Set(getSavedReads());
        saved.add(id);
        saveReads([...saved]);
        applyReadState(card, true);
    };

    document.querySelectorAll('[data-mark-read]').forEach((button) => {
        button.addEventListener('click', function () {
            setAsRead(this);
        });
    });

    document.querySelectorAll('[data-mark-all-read]').forEach((button) => {
        button.addEventListener('click', function () {
            const allIds = Array.from(document.querySelectorAll('.notif-card')).map((card) => card.dataset.notifId).filter(Boolean);
            saveReads(allIds);
            document.querySelectorAll('.notif-card').forEach((card) => applyReadState(card, true));

            this.disabled = true;
            this.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Semua sudah dibaca';
            this.style.opacity = '0.7';
            this.style.cursor = 'default';
        });
    });

    refreshFromStorage();
});
</script>
@endpush
