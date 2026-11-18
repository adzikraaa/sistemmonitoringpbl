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

        {{-- Item 1: Proposal disetujui (Belum dibaca) --}}
        <div class="notif-card" data-notif-id="proposal-disetujui" style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
            <div class="notif-item__top">
                <div class="notif-item__main">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EBF4EC; color: #4A7C59; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div class="notif-title-row">
                            <span style="font-size: 10px; font-weight: 700; background: #EBF4EC; color: #4A7C59; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Proposal</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                                Proposal disetujui <span class="notif-unread-dot" style="width: 6px; height: 6px; background: #C0392B; border-radius: 50%; display: inline-block;"></span>
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Proposal kelompokmu telah disetujui oleh dosen pembimbing.
                        </p>
                    </div>
                </div>
                <span class="notif-time" style="font-size: 12px; color: var(--sim-text-muted);">10 menit yang lalu</span>
            </div>
            <div class="notif-actions" style="border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.proposal') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Buka halaman proposal &rarr;
                </a>
                <button type="button" class="notif-action-btn" data-mark-read style="border: none; background: transparent; color: var(--sim-text-muted); cursor: pointer; font-weight: 500;">Tandai dibaca</button>
            </div>
        </div>

        {{-- Item 2: Jadwal bimbingan diperbarui (Belum dibaca) --}}
        <div class="notif-card" data-notif-id="jadwal-bimbingan" style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
            <div class="notif-item__top">
                <div class="notif-item__main">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #FFF9E6; color: #8A6A00; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <div>
                        <div class="notif-title-row">
                            <span style="font-size: 10px; font-weight: 700; background: #FFF9E6; color: #8A6A00; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Bimbingan</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                                Jadwal bimbingan diperbarui <span class="notif-unread-dot" style="width: 6px; height: 6px; background: #C0392B; border-radius: 50%; display: inline-block;"></span>
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Jadwal bimbingan kelompok telah diperbarui oleh Dr. Ahmad Fikri untuk sesi tinjauan sprint pertama.
                        </p>
                    </div>
                </div>
                <span class="notif-time" style="font-size: 12px; color: var(--sim-text-muted);">Hari ini, 09.30</span>
            </div>
            <div class="notif-actions" style="border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="#" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Lihat detail jadwal bimbingan &rarr;
                </a>
                <button type="button" class="notif-action-btn" data-mark-read style="border: none; background: transparent; color: var(--sim-text-muted); cursor: pointer; font-weight: 500;">Tandai dibaca</button>
            </div>
        </div>

        {{-- Item 3: Logbook perlu diperbaiki (Belum dibaca) --}}
        <div class="notif-card" data-notif-id="logbook-perlu-diperbaiki" style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
            <div class="notif-item__top">
                <div class="notif-item__main">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #FDF2F2; color: #C0392B; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div>
                        <div class="notif-title-row">
                            <span style="font-size: 10px; font-weight: 700; background: #FDF2F2; color: #C0392B; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Logbook</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                                Logbook perlu diperbaiki <span class="notif-unread-dot" style="width: 6px; height: 6px; background: #C0392B; border-radius: 50%; display: inline-block;"></span>
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Logbook minggu ini perlu dilengkapi sesuai catatan dosen pembimbing: tambahkan diagram ERD dan relasi modul otentikasi.
                        </p>
                    </div>
                </div>
                <span class="notif-time" style="font-size: 12px; color: var(--sim-text-muted);">Kemarin, 16.45</span>
            </div>
            <div class="notif-actions" style="border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.logbook') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Buka halaman logbook &rarr;
                </a>
                <button type="button" class="notif-action-btn" data-mark-read style="border: none; background: transparent; color: var(--sim-text-muted); cursor: pointer; font-weight: 500;">Tandai dibaca</button>
            </div>
        </div>

        {{-- Item 4: Milestone diperbarui (Sudah dibaca) --}}
        <div class="notif-card is-read" data-notif-id="milestone-diperbarui" style="background: #F9F7F5; border: 1px solid var(--sim-border); border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
            <div class="notif-item__top">
                <div class="notif-item__main">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EBE5DC; color: var(--sim-text-muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div>
                        <div class="notif-title-row">
                            <span style="font-size: 10px; font-weight: 700; background: #EBE5DC; color: var(--sim-text-muted); padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Milestone</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0;">
                                Milestone diperbarui
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Status milestone Perancangan Sistem telah diperbarui menjadi selesai 100%.
                        </p>
                    </div>
                </div>
                <span class="notif-time" style="font-size: 12px; color: var(--sim-text-muted);">3 hari lalu</span>
            </div>
            <div class="notif-actions" style="border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.milestone') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Buka halaman milestone &rarr;
                </a>
                <span style="font-size: 12px; color: var(--sim-text-light);">Tandai belum dibaca</span>
            </div>
        </div>

        {{-- Item 5: Revisi proposal diperlukan --}}
        <div class="notif-card is-read" data-notif-id="proposal-revisi" style="background: #F9F7F5; border: 1px solid var(--sim-border); border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
            <div class="notif-item__top">
                <div class="notif-item__main">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EBE5DC; color: var(--sim-text-muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    </div>
                    <div>
                        <div class="notif-title-row">
                            <span style="font-size: 10px; font-weight: 700; background: #EBE5DC; color: var(--sim-text-muted); padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Proposal</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0;">
                                Revisi proposal diperlukan
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Dosen pembimbing memberikan catatan revisi pada proposal kelompokmu terkait batasan ruang lingkup implementasi.
                        </p>
                    </div>
                </div>
                <span class="notif-time" style="font-size: 12px; color: var(--sim-text-muted);">5 hari lalu</span>
            </div>
            <div class="notif-actions" style="border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.proposal') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Lihat catatan revisi proposal &rarr;
                </a>
                <span style="font-size: 12px; color: var(--sim-text-light);">Tandai belum dibaca</span>
            </div>
        </div>

        {{-- Item 6: Pengingat aktivitas sistem --}}
        <div class="notif-card is-read" data-notif-id="pengingat-aktivitas" style="background: #F9F7F5; border: 1px solid var(--sim-border); border-radius: 16px; display: flex; flex-direction: column; gap: 14px;">
            <div class="notif-item__top">
                <div class="notif-item__main">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EBE5DC; color: var(--sim-text-muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <div class="notif-title-row">
                            <span style="font-size: 10px; font-weight: 700; background: #EBE5DC; color: var(--sim-text-muted); padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Sistem</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0;">
                                Pengingat aktivitas
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Jangan lupa memperbarui logbook aktivitas proyekmu sebelum batas rekapitulasi mingguan hari Jumat pukul 23.59 WIB.
                        </p>
                    </div>
                </div>
                <span class="notif-time" style="font-size: 12px; color: var(--sim-text-muted);">1 minggu lalu</span>
            </div>
            <div class="notif-actions" style="border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.logbook') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Pelajari panduan pengisian logbook &rarr;
                </a>
                <span style="font-size: 12px; color: var(--sim-text-light);">Tandai belum dibaca</span>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const storageKey = 'sim_mahasiswa_notifikasi_read';

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
        const actionText = card.querySelector('.notif-actions span');

        if (isRead) {
            card.classList.add('is-read');
            if (button) {
                button.disabled = true;
                button.textContent = 'Sudah dibaca';
                button.style.color = '#7B7B7B';
            }
            if (actionText && actionText.textContent.includes('Tandai belum dibaca')) {
                actionText.textContent = 'Sudah dibaca';
                actionText.style.color = '#7B7B7B';
            }
        } else {
            card.classList.remove('is-read');
            if (button) {
                button.disabled = false;
                button.textContent = 'Tandai dibaca';
                button.style.color = 'var(--sim-text-muted)';
            }
            if (actionText && actionText.textContent.includes('Sudah dibaca')) {
                actionText.textContent = 'Tandai belum dibaca';
                actionText.style.color = 'var(--sim-text-light)';
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
            this.textContent = 'Semua sudah dibaca';
        });
    });

    refreshFromStorage();
});
</script>
@endpush