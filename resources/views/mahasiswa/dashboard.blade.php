@extends('layouts.mahasiswa')

@section('title', 'Dashboard')
@section('meta_description', 'Dashboard mahasiswa SIM-PBL - ringkasan proyek, aktivitas, dan jadwal mendatang.')

@push('styles')
<style>
/* Hero Banner */
.db-hero {
    position: relative;
    margin: 24px 28px 0;
    border-radius: 20px;
    overflow: hidden;
    min-height: 176px;
    background: linear-gradient(120deg, #F5EFE6 0%, #EDE4D8 60%, #D9CFC4 100%);
    display: flex;
    align-items: flex-end;
    border: 1px solid var(--sim-border);
}
.db-hero__bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/bg_dashboard_mhs.png');
    background-size: cover;
    background-position: right center;
    background-repeat: no-repeat;
    opacity: 0.9;
}
.db-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg,
        rgba(245,239,230,0.97) 0%,
        rgba(245,239,230,0.88) 35%,
        rgba(245,239,230,0.3) 65%,
        transparent 100%);
}
.db-hero__content {
    position: relative;
    z-index: 2;
    padding: 28px 32px;
    flex: 1;
}
.db-hero__greeting {
    font-family: 'DM Serif Display', serif;
    font-size: 30px;
    font-weight: 400;
    color: var(--sim-text-dark);
    margin: 0 0 6px;
    line-height: 1.15;
}
.db-hero__sub {
    font-size: 13.5px;
    color: var(--sim-text-body);
    margin: 0;
    line-height: 1.5;
}
.db-hero__date {
    position: absolute;
    right: 24px;
    bottom: 22px;
    z-index: 3;
    display: flex;
    align-items: center;
    gap: 7px;
    background: rgba(255,255,255,0.72);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.6);
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--sim-text-dark);
    box-shadow: 0 2px 8px rgba(44,26,14,0.06);
}
/* Stats Row */
.db-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    padding: 20px 28px 0;
}
.db-stat-card {
    background: var(--sim-white);
    border: 1px solid var(--sim-border);
    border-radius: 14px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    transition: box-shadow 0.18s, transform 0.18s, border-color 0.18s;
    text-decoration: none;
    color: inherit;
}
.db-stat-card:hover {
    box-shadow: 0 4px 16px rgba(44,26,14,0.08);
    transform: translateY(-2px);
    border-color: var(--sim-cream-dark);
}
.db-stat-card__icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.db-stat-card__body { flex: 1; min-width: 0; }
.db-stat-card__label { font-size: 11px; color: var(--sim-text-muted); margin: 0 0 2px; font-weight: 500; }
.db-stat-card__value { font-family: 'DM Serif Display', serif; font-size: 26px; font-weight: 400; color: var(--sim-text-dark); line-height: 1; margin: 0 0 2px; }
.db-stat-card__sub { font-size: 11.5px; color: var(--sim-text-muted); margin: 0; }
.db-stat-card__arrow { color: var(--sim-text-light); flex-shrink: 0; }
/* Grid Layout */
.db-grid {
    display: grid;
    grid-template-columns: 1.25fr 1fr 0.85fr;
    gap: 14px;
    padding: 16px 28px 0;
}
.db-grid-bottom {
    display: grid;
    grid-template-columns: 1.35fr 0.65fr;
    gap: 14px;
    padding: 14px 28px 32px;
}
/* Card Base */
.db-card {
    background: var(--sim-white);
    border: 1px solid var(--sim-border);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    flex-direction: column;
}
.db-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}
.db-card__title {
    font-size: 14px;
    font-weight: 600;
    color: var(--sim-text-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 7px;
}
.db-card__link {
    font-size: 12px;
    font-weight: 500;
    color: var(--sim-brown-medium);
    text-decoration: none;
    transition: opacity 0.15s;
}
.db-card__link:hover { opacity: 0.7; }
/* Circular Progress */
.db-project { display: flex; align-items: center; gap: 18px; margin-bottom: 20px; }
.db-project__circle { position: relative; width: 78px; height: 78px; flex-shrink: 0; }
.db-project__circle svg { transform: rotate(-90deg); }
.db-project__circle-bg { fill: none; stroke: #F0E8DC; stroke-width: 8; }
.db-project__circle-fg { fill: none; stroke: #4A7C59; stroke-width: 8; stroke-linecap: round; }
.db-project__circle-label {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    line-height: 1.1;
}
.db-project__circle-pct { font-size: 15px; font-weight: 700; color: var(--sim-text-dark); }
.db-project__circle-txt { font-size: 9px; color: var(--sim-text-muted); font-weight: 500; }
.db-project__name { font-size: 13.5px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 2px; }
.db-project__desc { font-size: 12px; color: var(--sim-text-muted); margin: 0; line-height: 1.4; }
/* Steps */
.db-steps { display: flex; align-items: flex-start; gap: 0; position: relative; margin-top: 4px; }
.db-step { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
.db-step__line { position: absolute; top: 7px; left: 50%; width: 100%; height: 2px; z-index: 0; }
.db-step__line--done { background: #4A7C59; }
.db-step__line--active { background: linear-gradient(90deg, #4A7C59, #E8DDD0); }
.db-step__line--todo { background: #E8DDD0; }
.db-step:last-child .db-step__line { display: none; }
.db-step__dot { width: 14px; height: 14px; border-radius: 50%; border: 2px solid; position: relative; z-index: 1; margin-bottom: 5px; }
.db-step__dot--done { background: #4A7C59; border-color: #4A7C59; }
.db-step__dot--active { background: #fff; border-color: #B8934A; box-shadow: 0 0 0 3px rgba(184,147,74,0.2); }
.db-step__dot--todo { background: #fff; border-color: #D5C8BA; }
.db-step__lbl { font-size: 10px; color: var(--sim-text-muted); text-align: center; line-height: 1.3; }
.db-step__sub { font-size: 9px; font-weight: 500; text-align: center; line-height: 1.2; }
.db-step__sub--done { color: #4A7C59; }
.db-step__sub--active { color: #B8934A; }
.db-step__sub--todo { color: var(--sim-text-light); }
/* Pengumuman */
.db-announce-item { display: flex; align-items: flex-start; gap: 10px; padding: 9px 0; border-bottom: 1px solid var(--sim-border-light); }
.db-announce-item:last-of-type { border-bottom: none; }
.db-announce-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
.db-announce-text { font-size: 13px; color: var(--sim-text-dark); margin: 0 0 1px; font-weight: 500; line-height: 1.3; }
.db-announce-time { font-size: 11px; color: var(--sim-text-muted); margin: 0; }
/* Notifikasi */
.db-notif-item { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 10px; cursor: pointer; transition: background 0.15s; text-decoration: none; }
.db-notif-item:hover { background: var(--sim-cream); }
.db-notif-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.db-notif-body { flex: 1; min-width: 0; }
.db-notif-title { font-size: 12.5px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.db-notif-time { font-size: 11px; color: var(--sim-text-muted); margin: 0; }
.db-notif-chevron { color: var(--sim-text-light); flex-shrink: 0; }
/* Aktivitas */
.db-activity-item { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid var(--sim-border-light); }
.db-activity-item:last-child { border-bottom: none; }
.db-activity-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: var(--sim-cream); }
.db-activity-body { flex: 1; min-width: 0; }
.db-activity-text { font-size: 13px; font-weight: 500; color: var(--sim-text-dark); margin: 0; line-height: 1.3; }
.db-activity-time { font-size: 11px; color: var(--sim-text-muted); margin: 0; }
.db-activity-badge { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 20px; flex-shrink: 0; white-space: nowrap; }
/* Jadwal */
.db-jadwal-item { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--sim-border-light); }
.db-jadwal-item:last-child { border-bottom: none; }
.db-jadwal-date { display: flex; flex-direction: column; align-items: center; justify-content: center; width: 38px; height: 44px; background: var(--sim-cream); border-radius: 10px; flex-shrink: 0; border: 1px solid var(--sim-border); }
.db-jadwal-date__day { font-family: 'DM Serif Display', serif; font-size: 17px; font-weight: 400; color: var(--sim-text-dark); line-height: 1; }
.db-jadwal-date__month { font-size: 9px; font-weight: 600; color: var(--sim-text-muted); text-transform: uppercase; line-height: 1.2; }
.db-jadwal-title { font-size: 13px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 2px; line-height: 1.3; }
.db-jadwal-meta { font-size: 11.5px; color: var(--sim-text-muted); margin: 0; line-height: 1.4; }
/* Badge colors */
.badge-logbook  { background: #EBF4EC; color: #2E6B3E; }
.badge-proposal { background: #FFF9E6; color: #7A5A00; }
.badge-milestone{ background: #F0ECF8; color: #5A3B82; }
.badge-kelompok { background: #FEF3EC; color: #8A3E14; }
/* Responsive */
@media (max-width: 1100px) {
    .db-grid { grid-template-columns: 1fr 1fr; }
    .db-grid > .db-card:last-child { grid-column: 1 / -1; }
}
@media (max-width: 860px) {
    .db-stats { grid-template-columns: repeat(2, 1fr); }
    .db-grid { grid-template-columns: 1fr; }
    .db-grid-bottom { grid-template-columns: 1fr; }
    .db-hero__greeting { font-size: 22px; }
}
</style>
@endpush

@section('content')

{{-- HERO BANNER --}}
<div class="db-hero">
    <div class="db-hero__bg"></div>
    <div class="db-hero__overlay"></div>
    <div class="db-hero__content">
        <h1 class="db-hero__greeting">Halo, {{ $userName ?? 'Mahasiswa' }}</h1>
        <p class="db-hero__sub">
            Semangat terus mengerjakan proyek PBL-mu!<br>
            Setiap progres kecil itu berarti.
        </p>
    </div>
    <div class="db-hero__date">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>{{ now()->locale('id')->translatedFormat('l, j F Y') }}</span>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="db-stats">
    <a href="{{ route('mahasiswa.proposal') }}" class="db-stat-card">
        <div class="db-stat-card__icon" style="background:#FFF9E6;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#B8934A" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="db-stat-card__body">
            <p class="db-stat-card__label">Proposal</p>
            <p class="db-stat-card__value">1</p>
            <p class="db-stat-card__sub">Menunggu revisi</p>
        </div>
        <div class="db-stat-card__arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></div>
    </a>
    <a href="{{ route('mahasiswa.logbook') }}" class="db-stat-card">
        <div class="db-stat-card__icon" style="background:#EBF4EC;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4A7C59" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <div class="db-stat-card__body">
            <p class="db-stat-card__label">Logbook</p>
            <p class="db-stat-card__value">4</p>
            <p class="db-stat-card__sub">Minggu ini</p>
        </div>
        <div class="db-stat-card__arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></div>
    </a>
    <a href="{{ route('mahasiswa.milestone') }}" class="db-stat-card">
        <div class="db-stat-card__icon" style="background:#F0ECF8;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6B4FA0" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="db-stat-card__body">
            <p class="db-stat-card__label">Milestone</p>
            <p class="db-stat-card__value">2</p>
            <p class="db-stat-card__sub">Dalam proses</p>
        </div>
        <div class="db-stat-card__arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></div>
    </a>
    <a href="{{ route('mahasiswa.kelompok') }}" class="db-stat-card">
        <div class="db-stat-card__icon" style="background:#FEF3EC;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8A3E14" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="db-stat-card__body">
            <p class="db-stat-card__label">Anggota Kelompok</p>
            <p class="db-stat-card__value">4</p>
            <p class="db-stat-card__sub">Orang</p>
        </div>
        <div class="db-stat-card__arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></div>
    </a>
</div>

{{-- ROW 2 : 3 KOLOM --}}
<div class="db-grid">

    {{-- Status Proyek Kelompok --}}
    <div class="db-card">
        <div class="db-card__header">
            <h2 class="db-card__title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Status Proyek Kelompok
            </h2>
            <a href="{{ route('mahasiswa.kelompok') }}" class="db-card__link">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
        <div class="db-project">
            <div class="db-project__circle">
                <svg width="78" height="78" viewBox="0 0 78 78">
                    <circle class="db-project__circle-bg" cx="39" cy="39" r="32"/>
                    <circle class="db-project__circle-fg" cx="39" cy="39" r="32"
                        stroke-dasharray="201.06"
                        stroke-dashoffset="201.06"
                        id="db-progress-circle"/>
                </svg>
                <div class="db-project__circle-label">
                    <span class="db-project__circle-pct">60%</span>
                    <span class="db-project__circle-txt">Progres</span>
                </div>
            </div>
            <div class="db-project__info">
                <p class="db-project__name">Kelompok 2 - SIB 3B</p>
                <p class="db-project__desc">Pengembangan Sistem<br>Monitoring PBL</p>
            </div>
        </div>
        <div class="db-steps">
            <div class="db-step">
                <div class="db-step__line db-step__line--done"></div>
                <div class="db-step__dot db-step__dot--done"></div>
                <span class="db-step__lbl">Proposal</span>
                <span class="db-step__sub db-step__sub--done">Disetujui</span>
            </div>
            <div class="db-step">
                <div class="db-step__line db-step__line--active"></div>
                <div class="db-step__dot db-step__dot--active"></div>
                <span class="db-step__lbl">Analisis</span>
                <span class="db-step__sub db-step__sub--active">Dalam Proses</span>
            </div>
            <div class="db-step">
                <div class="db-step__line db-step__line--todo"></div>
                <div class="db-step__dot db-step__dot--todo"></div>
                <span class="db-step__lbl">Perancangan</span>
                <span class="db-step__sub db-step__sub--todo">Belum Mulai</span>
            </div>
            <div class="db-step">
                <div class="db-step__line db-step__line--todo"></div>
                <div class="db-step__dot db-step__dot--todo"></div>
                <span class="db-step__lbl">Implementasi</span>
                <span class="db-step__sub db-step__sub--todo">Belum Mulai</span>
            </div>
        </div>
    </div>

    {{-- Pengumuman Terbaru --}}
    <div class="db-card">
        <div class="db-card__header">
            <h2 class="db-card__title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Pengumuman Terbaru
            </h2>
        </div>
        <div class="db-announce-item">
            <div class="db-announce-dot" style="background:#4A7C59;"></div>
            <div>
                <p class="db-announce-text">Proposal kelompokmu telah disetujui</p>
                <p class="db-announce-time">2 hari yang lalu</p>
            </div>
        </div>
        <div class="db-announce-item">
            <div class="db-announce-dot" style="background:#B8934A;"></div>
            <div>
                <p class="db-announce-text">Jadwal bimbingan kelompok diperbarui</p>
                <p class="db-announce-time">4 hari yang lalu</p>
            </div>
        </div>
        <div class="db-announce-item">
            <div class="db-announce-dot" style="background:#C0C0C0;"></div>
            <div>
                <p class="db-announce-text">Pengingat untuk mengisi logbook</p>
                <p class="db-announce-time">6 hari yang lalu</p>
            </div>
        </div>
        <a href="{{ route('mahasiswa.notifikasi') }}" class="db-card__link" style="margin-top:12px; font-size:12.5px; display:inline-flex; align-items:center; gap:4px;">
            Lihat semua pengumuman
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </div>

    {{-- Notifikasi --}}
    <div class="db-card">
        <div class="db-card__header">
            <h2 class="db-card__title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notifikasi
            </h2>
            <a href="{{ route('mahasiswa.notifikasi') }}" class="db-card__link">Lihat semua</a>
        </div>
        <a href="{{ route('mahasiswa.proposal') }}" class="db-notif-item">
            <div class="db-notif-icon" style="background:#FFF9E6;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#B8934A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="db-notif-body">
                <p class="db-notif-title">Proposal disetujui</p>
                <p class="db-notif-time">2 hari yang lalu</p>
            </div>
            <div class="db-notif-chevron"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></div>
        </a>
        <a href="{{ route('mahasiswa.proposal') }}" class="db-notif-item">
            <div class="db-notif-icon" style="background:#FEF3EC;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8A3E14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="db-notif-body">
                <p class="db-notif-title">Revisi proposal diperlukan</p>
                <p class="db-notif-time">3 hari yang lalu</p>
            </div>
            <div class="db-notif-chevron"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></div>
        </a>
        <a href="{{ route('mahasiswa.kelompok') }}" class="db-notif-item">
            <div class="db-notif-icon" style="background:#EBF4EC;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4A7C59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="db-notif-body">
                <p class="db-notif-title">Jadwal bimbingan diperbarui</p>
                <p class="db-notif-time">4 hari yang lalu</p>
            </div>
            <div class="db-notif-chevron"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></div>
        </a>
    </div>
</div>

{{-- ROW 3 : 2 KOLOM --}}
<div class="db-grid-bottom">

    {{-- Aktivitas Terbaru --}}
    <div class="db-card">
        <div class="db-card__header">
            <h2 class="db-card__title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Aktivitas Terbaru
            </h2>
            <a href="{{ route('mahasiswa.logbook') }}" class="db-card__link">Lihat semua</a>
        </div>
        <div class="db-activity-item">
            <div class="db-activity-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A7C59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div class="db-activity-body">
                <p class="db-activity-text">Logbook minggu 4 berhasil dikirim</p>
                <p class="db-activity-time">2 hari yang lalu</p>
            </div>
            <span class="db-activity-badge badge-logbook">Logbook</span>
        </div>
        <div class="db-activity-item">
            <div class="db-activity-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#B8934A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="db-activity-body">
                <p class="db-activity-text">Proposal kelompok diperbarui</p>
                <p class="db-activity-time">3 hari yang lalu</p>
            </div>
            <span class="db-activity-badge badge-proposal">Proposal</span>
        </div>
        <div class="db-activity-item">
            <div class="db-activity-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6B4FA0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div class="db-activity-body">
                <p class="db-activity-text">Milestone "Perancangan Sistem" diupdate</p>
                <p class="db-activity-time">4 hari yang lalu</p>
            </div>
            <span class="db-activity-badge badge-milestone">Milestone</span>
        </div>
        <div class="db-activity-item">
            <div class="db-activity-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#8A3E14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="db-activity-body">
                <p class="db-activity-text">Diskusi kelompok baru ditambahkan</p>
                <p class="db-activity-time">5 hari yang lalu</p>
            </div>
            <span class="db-activity-badge badge-kelompok">Kelompok</span>
        </div>
    </div>

    {{-- Jadwal Mendatang --}}
    <div class="db-card">
        <div class="db-card__header">
            <h2 class="db-card__title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Jadwal Mendatang
            </h2>
            <a href="#" class="db-card__link">Lihat semua</a>
        </div>
        <div class="db-jadwal-item">
            <div class="db-jadwal-date">
                <span class="db-jadwal-date__day">8</span>
                <span class="db-jadwal-date__month">Okt</span>
            </div>
            <div class="db-jadwal-body">
                <p class="db-jadwal-title">Bimbingan kelompok</p>
                <p class="db-jadwal-meta">13.00 - 14.40 WIB · Ruang 3.2</p>
            </div>
        </div>
        <div class="db-jadwal-item">
            <div class="db-jadwal-date">
                <span class="db-jadwal-date__day">12</span>
                <span class="db-jadwal-date__month">Okt</span>
            </div>
            <div class="db-jadwal-body">
                <p class="db-jadwal-title">Deadline logbook minggu 5</p>
                <p class="db-jadwal-meta">23.59 WIB · Online</p>
            </div>
        </div>
        <div class="db-jadwal-item">
            <div class="db-jadwal-date">
                <span class="db-jadwal-date__day">15</span>
                <span class="db-jadwal-date__month">Okt</span>
            </div>
            <div class="db-jadwal-body">
                <p class="db-jadwal-title">Presentasi kemajuan proyek</p>
                <p class="db-jadwal-meta">09.00 - 11.00 WIB · Ruang 2.1</p>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var circle = document.getElementById('db-progress-circle');
    if (circle) {
        var circumference = 2 * Math.PI * 32;
        var target = circumference * (1 - 0.60);
        circle.style.strokeDashoffset = circumference;
        requestAnimationFrame(function() {
            circle.style.transition = 'stroke-dashoffset 1s cubic-bezier(0.4,0,0.2,1)';
            circle.style.strokeDashoffset = target;
        });
    }
});
</script>
@endpush
