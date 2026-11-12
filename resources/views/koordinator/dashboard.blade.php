@extends('layouts.koordinator')

@section('title', 'Dashboard & Rekap Global - SIM-PBL')
@section('breadcrumb_title', 'Dashboard')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════
   DASHBOARD KOORDINATOR — PAGE STYLES
   ═══════════════════════════════════════════════════ */

/* ── Page Header ── */
.krd-dash-eyebrow {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.12em;
    color: var(--krd-text-muted);
    text-transform: uppercase;
    margin: 0 0 6px 0;
}
.krd-dash-title {
    font-family: 'DM Serif Display', Georgia, serif;
    font-size: 30px;
    font-weight: 400;
    color: var(--krd-brown-dark);
    margin: 0 0 6px 0;
    line-height: 1.15;
}
.krd-dash-desc {
    font-size: 13.5px;
    color: var(--krd-text-muted);
    margin: 0;
}
.krd-dash-page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

/* ── Ekspor Button ── */
.krd-btn-ekspor {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border: 1.5px solid var(--krd-border-warm);
    border-radius: 8px;
    background: #fff;
    color: var(--krd-text-dark);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s;
    white-space: nowrap;
}
.krd-btn-ekspor:hover {
    background: #F3EDE4;
    border-color: #C8BFB4;
}

/* ── Filter Bar ── */
.krd-filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 24px;
    padding: 12px 16px;
    background: #fff;
    border: 1.5px solid var(--krd-border-warm);
    border-radius: 10px;
}
.krd-filter-select {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1.5px solid var(--krd-border-input);
    border-radius: 7px;
    background: #FDFAF7;
    color: var(--krd-text-dark);
    font-size: 12.5px;
    font-weight: 500;
    cursor: pointer;
    transition: border-color 0.15s;
    user-select: none;
}
.krd-filter-select:hover {
    border-color: #B09A88;
}
.krd-filter-divider {
    width: 1px;
    height: 20px;
    background: var(--krd-border-warm);
}
.krd-filter-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: #EAF3EC;
    color: #2A5A3B;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 999px;
}
.krd-filter-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #2E7D32;
    display: inline-block;
}
.krd-filter-updated {
    font-size: 11.5px;
    color: var(--krd-text-muted);
    margin-left: auto;
}

/* ── Stats Cards ── */
.krd-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
.krd-stat-card {
    background: #fff;
    border: 1.5px solid var(--krd-border-warm);
    border-radius: 12px;
    padding: 20px 22px;
}
.krd-stat-label {
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 0.11em;
    color: var(--krd-text-muted);
    text-transform: uppercase;
    margin: 0 0 8px 0;
}
.krd-stat-number {
    font-family: 'DM Serif Display', Georgia, serif;
    font-size: 38px;
    font-weight: 400;
    color: var(--krd-brown-dark);
    line-height: 1;
    margin: 0 0 6px 0;
}
.krd-stat-sub {
    font-size: 11.5px;
    color: var(--krd-text-muted);
    margin: 0;
    line-height: 1.4;
}

/* ── Two-column Charts Row ── */
.krd-charts-row {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 16px;
    margin-bottom: 24px;
}
.krd-chart-card {
    background: #fff;
    border: 1.5px solid var(--krd-border-warm);
    border-radius: 12px;
    padding: 24px;
}
.krd-chart-title {
    font-family: 'DM Serif Display', Georgia, serif;
    font-size: 18px;
    font-weight: 400;
    color: var(--krd-brown-dark);
    margin: 0 0 4px 0;
}
.krd-chart-subtitle {
    font-size: 12px;
    color: var(--krd-text-muted);
    margin: 0 0 20px 0;
}

/* ── Bar Chart (Milestone Progress) ── */
.krd-bar-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}
.krd-bar-label {
    font-size: 12.5px;
    color: var(--krd-text-dark);
    min-width: 110px;
    flex-shrink: 0;
}
.krd-bar-track {
    flex: 1;
    height: 10px;
    background: #F0EBE4;
    border-radius: 999px;
    overflow: hidden;
}
.krd-bar-fill {
    height: 100%;
    border-radius: 999px;
}
.krd-bar-val {
    font-size: 12px;
    font-weight: 600;
    color: var(--krd-text-dark);
    min-width: 36px;
    text-align: right;
    flex-shrink: 0;
}
.krd-bar-axis {
    display: flex;
    justify-content: space-between;
    padding-left: 120px;
    margin-top: 6px;
}
.krd-bar-axis span {
    font-size: 10px;
    color: var(--krd-text-muted);
}

/* ── Donut Chart (Capaian Program) ── */
.krd-donut-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 14px;
}
.krd-donut-chart {
    position: relative;
    width: 150px;
    height: 150px;
}
.krd-donut-svg {
    transform: rotate(-90deg);
    width: 150px;
    height: 150px;
}
.krd-donut-label {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-family: 'DM Serif Display', Georgia, serif;
    font-size: 26px;
    color: var(--krd-brown-dark);
    line-height: 1;
}
.krd-donut-label small {
    font-family: 'Inter', sans-serif;
    font-size: 10px;
    color: var(--krd-text-muted);
    font-weight: 400;
    margin-top: 2px;
    display: block;
}
.krd-donut-info {
    text-align: center;
}
.krd-donut-score {
    font-family: 'DM Serif Display', Georgia, serif;
    font-size: 20px;
    color: var(--krd-brown-dark);
}
.krd-donut-score small {
    font-family: 'Inter', sans-serif;
    font-size: 11.5px;
    color: var(--krd-text-muted);
    font-weight: 400;
    display: block;
    margin-top: 2px;
}
.krd-badge-sisa {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    background: #FFF3E0;
    color: #B45309;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 999px;
    margin-top: 6px;
}
.krd-donut-note {
    font-size: 10.5px;
    color: var(--krd-text-muted);
    text-align: center;
    margin: 0;
    line-height: 1.5;
}

/* ── Table Rekapitulasi ── */
.krd-rekap-card {
    background: #fff;
    border: 1.5px solid var(--krd-border-warm);
    border-radius: 12px;
    margin-bottom: 20px;
    overflow: hidden;
}
.krd-rekap-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1.5px solid var(--krd-border-warm);
}
.krd-rekap-title {
    font-family: 'DM Serif Display', Georgia, serif;
    font-size: 18px;
    font-weight: 400;
    color: var(--krd-brown-dark);
    margin: 0;
}
.krd-btn-kelola {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border: 1.5px solid var(--krd-border-warm);
    border-radius: 7px;
    background: #FDFAF7;
    color: var(--krd-text-dark);
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.15s;
}
.krd-btn-kelola:hover {
    background: #F3EDE4;
}
.krd-rekap-table {
    width: 100%;
    border-collapse: collapse;
}
.krd-rekap-table th {
    padding: 10px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--krd-text-muted);
    text-transform: uppercase;
    background: #FDFAF7;
    border-bottom: 1.5px solid var(--krd-border-warm);
}
.krd-rekap-table td {
    padding: 14px 16px;
    font-size: 13px;
    color: var(--krd-text-dark);
    border-bottom: 1px solid #F0EBE4;
    vertical-align: middle;
}
.krd-rekap-table tr:last-child td {
    border-bottom: none;
}
.krd-rekap-table tr:hover td {
    background: #FDFAF7;
}
.krd-kelas-name {
    font-weight: 700;
    color: var(--krd-brown-dark);
    display: block;
}
.krd-kelas-pic {
    font-size: 11.5px;
    color: var(--krd-text-muted);
    display: block;
    margin-top: 2px;
}
.krd-mini-bar-track {
    width: 100px;
    height: 6px;
    background: #F0EBE4;
    border-radius: 999px;
    overflow: hidden;
    display: inline-block;
    vertical-align: middle;
    margin-right: 6px;
}
.krd-mini-bar-fill {
    height: 100%;
    border-radius: 999px;
}
.krd-mini-bar-pct {
    font-size: 12px;
    font-weight: 600;
    color: var(--krd-text-dark);
    vertical-align: middle;
}

/* ── Status Badges ── */
.krd-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
}
.krd-status-badge--ok {
    background: #EAF3EC;
    color: #2A5A3B;
}
.krd-status-badge--warn {
    background: #FFF3E0;
    color: #9A5B08;
}
.krd-status-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    display: inline-block;
}
.krd-status-badge--ok .krd-status-dot { background: #2E7D32; }
.krd-status-badge--warn .krd-status-dot { background: #D97706; }

/* ── Detail Link ── */
.krd-detail-link {
    font-size: 12.5px;
    font-weight: 600;
    color: #7A5C4A;
    text-decoration: none;
    white-space: nowrap;
}
.krd-detail-link:hover {
    color: var(--krd-brown-dark);
    text-decoration: underline;
}

/* ── Table Footer ── */
.krd-rekap-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 24px;
    border-top: 1.5px solid var(--krd-border-warm);
    background: #FDFAF7;
}
.krd-rekap-footer-text {
    font-size: 12px;
    color: var(--krd-text-muted);
}
.krd-pagination {
    display: flex;
    align-items: center;
    gap: 6px;
}
.krd-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: 1.5px solid var(--krd-border-input);
    border-radius: 6px;
    background: #fff;
    color: var(--krd-text-dark);
    font-size: 12px;
    cursor: pointer;
    transition: background 0.12s;
}
.krd-page-btn:hover, .krd-page-btn--active {
    background: var(--krd-brown-dark);
    color: #fff;
    border-color: var(--krd-brown-dark);
}

/* ── Alert Bar ── */
.krd-alert-bar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 20px;
    background: #FFFBF0;
    border: 1.5px solid #F0D080;
    border-radius: 10px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}
.krd-alert-left {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.krd-alert-icon {
    color: #D97706;
    flex-shrink: 0;
    margin-top: 1px;
}
.krd-alert-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #92400E;
    margin: 0 0 3px 0;
}
.krd-alert-desc {
    font-size: 12px;
    color: #78350F;
    margin: 0;
    line-height: 1.5;
}
.krd-btn-kelola-milestone {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    background: #fff;
    border: 1.5px solid #E5C870;
    border-radius: 8px;
    color: #92400E;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.15s;
    flex-shrink: 0;
    align-self: center;
}
.krd-btn-kelola-milestone:hover {
    background: #FEF3C7;
}

/* ── Responsive ── */
@media (max-width: 1100px) {
    .krd-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .krd-charts-row {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 640px) {
    .krd-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .krd-dash-title {
        font-size: 24px;
    }
    .krd-rekap-table th:nth-child(5),
    .krd-rekap-table td:nth-child(5) {
        display: none;
    }
}
</style>
@endpush

@section('content')

{{-- ─── Page Header ────────────────────────────────── --}}
<div class="krd-dash-page-header">
    <div>
        <p class="krd-dash-eyebrow">SIM-PBL · ADMINISTRASI PROGRAM</p>
        <h1 class="krd-dash-title">Dashboard &amp; Rekap Global</h1>
        <p class="krd-dash-desc">Satu pandangan untuk mengelola pelaksanaan PBL di seluruh kelas dan kelompok.</p>
    </div>
    <a href="#" class="krd-btn-ekspor">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Ekspor Rekap
    </a>
</div>

{{-- ─── Filter Bar ─────────────────────────────────── --}}
<div class="krd-filter-bar">
    <div class="krd-filter-select">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Periode: 31 Agu 2026 – 15 Jan 2027
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>
    <div class="krd-filter-divider"></div>
    <div class="krd-filter-select">
        Semua kelas (3)
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>
    <div class="krd-filter-badge">
        <span class="krd-filter-dot"></span>
        Lingkup global · 18 kelompok
    </div>
    <span class="krd-filter-updated">Diperbarui 25 Okt, 10:42 WIB</span>
</div>

{{-- ─── Stats Cards ─────────────────────────────────── --}}
<div class="krd-stats-grid">
    <div class="krd-stat-card">
        <p class="krd-stat-label">Kelas Aktif</p>
        <p class="krd-stat-number">3</p>
        <p class="krd-stat-sub">SIB 3A &middot; SIB 3B &middot; SIB 3C</p>
    </div>
    <div class="krd-stat-card">
        <p class="krd-stat-label">Kelompok PBL</p>
        <p class="krd-stat-number">18</p>
        <p class="krd-stat-sub">6 kelompok pada setiap kelas</p>
    </div>
    <div class="krd-stat-card">
        <p class="krd-stat-label">Mahasiswa</p>
        <p class="krd-stat-number">72</p>
        <p class="krd-stat-sub">4 mahasiswa per kelompok</p>
    </div>
    <div class="krd-stat-card">
        <p class="krd-stat-label">Dosen Pembimbing</p>
        <p class="krd-stat-number">8</p>
        <p class="krd-stat-sub">16 dari 18 kelompok ditugaskan</p>
    </div>
</div>

{{-- ─── Charts Row ──────────────────────────────────── --}}
<div class="krd-charts-row">

    {{-- Bar Chart: Penyelesaian Milestone Global --}}
    <div class="krd-chart-card">
        <h2 class="krd-chart-title">Penyelesaian milestone global</h2>
        <p class="krd-chart-subtitle">Jumlah kelompok selesai per tahapan &middot; dari 18 kelompok</p>

        @php
        $milestoneData = [
            ['label' => 'Tim &amp; topik',   'val' => 18, 'max' => 18, 'pct' => 100, 'color' => '#4A3222'],
            ['label' => 'Proposal',          'val' => 16, 'max' => 18, 'pct' => 88.9,'color' => '#8B6240'],
            ['label' => 'Prototipe',         'val' => 10, 'max' => 18, 'pct' => 55.6,'color' => '#B08060'],
            ['label' => 'Implementasi',      'val' => 0,  'max' => 18, 'pct' => 0,   'color' => '#D6C4B2'],
            ['label' => 'Laporan akhir',     'val' => 0,  'max' => 18, 'pct' => 0,   'color' => '#D6C4B2'],
        ];
        @endphp

        @foreach($milestoneData as $row)
        <div class="krd-bar-row">
            <span class="krd-bar-label">{!! $row['label'] !!}</span>
            <div class="krd-bar-track">
                <div class="krd-bar-fill" style="width: {{ $row['pct'] }}%; background-color: {{ $row['color'] }};"></div>
            </div>
            <span class="krd-bar-val">{{ $row['val'] }}/{{ $row['max'] }}</span>
        </div>
        @endforeach

        <div class="krd-bar-axis">
            <span>0</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
        </div>
    </div>

    {{-- Donut Chart: Capaian Seluruh Program --}}
    <div class="krd-chart-card">
        <h2 class="krd-chart-title">Capaian seluruh program</h2>
        <p class="krd-chart-subtitle">Setiap milestone memiliki bobot yang sama.</p>

        @php
            $totalTarget = 90;
            $selesai = 44;
            $pctCapaian = round(($selesai / $totalTarget) * 100, 1);
            $circumference = 2 * M_PI * 54; // r=54
            $offset = $circumference * (1 - $pctCapaian / 100);
        @endphp

        <div class="krd-donut-wrap">
            <div class="krd-donut-chart">
                <svg class="krd-donut-svg" viewBox="0 0 120 120">
                    {{-- Track --}}
                    <circle cx="60" cy="60" r="54" fill="none" stroke="#F0EBE4" stroke-width="11"/>
                    {{-- Fill --}}
                    <circle cx="60" cy="60" r="54" fill="none"
                        stroke="#4A3222"
                        stroke-width="11"
                        stroke-linecap="round"
                        stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $offset }}"/>
                </svg>
                <div class="krd-donut-label">
                    {{ $pctCapaian }}%
                </div>
            </div>
            <div class="krd-donut-info">
                <div class="krd-donut-score">
                    {{ $selesai }} / {{ $totalTarget }}
                    <small>target telah selesai</small>
                </div>
                <div class="krd-badge-sisa">
                    &bull; {{ $totalTarget - $selesai }} target tersisa
                </div>
            </div>
            <p class="krd-donut-note">18 kelompok &times; 5 milestone.<br>Capaian dihitung dari penyelesaian,<br>bukan penilaian akademik.</p>
        </div>
    </div>
</div>

{{-- ─── Rekapitulasi Seluruh Kelas ─────────────────── --}}
<div class="krd-rekap-card">
    <div class="krd-rekap-header">
        <h2 class="krd-rekap-title">Rekapitulasi seluruh kelas</h2>
        <a href="#" class="krd-btn-kelola">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>
            </svg>
            Kelola kelas
        </a>
    </div>

    <table class="krd-rekap-table">
        <thead>
            <tr>
                <th>Kelas / PIC</th>
                <th>Mahasiswa</th>
                <th>Kelompok</th>
                <th>Milestone Selesai</th>
                <th>Capaian</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
            $kelasData = [
                [
                    'kelas' => 'SIB 3A',
                    'pic'   => 'Dr. Rian Hidayat',
                    'mhs'   => 24,
                    'kel'   => 6,
                    'ms_selesai' => '16 / 30 target',
                    'capaian_pct' => 53.3,
                    'status' => 'Sesuai jadwal',
                    'status_type' => 'ok',
                ],
                [
                    'kelas' => 'SIB 3B',
                    'pic'   => 'Dr. Farida, M.T.',
                    'mhs'   => 24,
                    'kel'   => 6,
                    'ms_selesai' => '15 / 30 target',
                    'capaian_pct' => 50.0,
                    'status' => 'Perlu perhatian',
                    'status_type' => 'warn',
                ],
                [
                    'kelas' => 'SIB 3C',
                    'pic'   => 'Bambang Pratama, M.Kom',
                    'mhs'   => 24,
                    'kel'   => 6,
                    'ms_selesai' => '13 / 30 target',
                    'capaian_pct' => 43.3,
                    'status' => 'Perlu perhatian',
                    'status_type' => 'warn',
                ],
            ];
            @endphp

            @foreach($kelasData as $row)
            <tr>
                <td>
                    <span class="krd-kelas-name">{{ $row['kelas'] }}</span>
                    <span class="krd-kelas-pic">{{ $row['pic'] }}</span>
                </td>
                <td>{{ $row['mhs'] }}</td>
                <td>{{ $row['kel'] }}</td>
                <td>{{ $row['ms_selesai'] }}</td>
                <td>
                    <span class="krd-mini-bar-track">
                        <span class="krd-mini-bar-fill" style="width: {{ $row['capaian_pct'] }}%; background: {{ $row['status_type'] === 'ok' ? '#4A7C59' : '#D97706' }};"></span>
                    </span>
                    <span class="krd-mini-bar-pct">{{ $row['capaian_pct'] }}%</span>
                </td>
                <td>
                    <span class="krd-status-badge krd-status-badge--{{ $row['status_type'] }}">
                        <span class="krd-status-dot"></span>
                        {{ $row['status'] }}
                    </span>
                </td>
                <td>
                    <a href="#" class="krd-detail-link">Detail &rarr;</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="krd-rekap-footer">
        <span class="krd-rekap-footer-text">3 dari 3 kelas &middot; 72 mahasiswa &middot; 18 kelompok</span>
        <div class="krd-pagination">
            <button class="krd-page-btn">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button class="krd-page-btn krd-page-btn--active">1</button>
            <span style="font-size:12px; color: var(--krd-text-muted);">dari 1</span>
            <button class="krd-page-btn">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</div>

{{-- ─── Alert Bar ───────────────────────────────────── --}}
<div class="krd-alert-bar">
    <div class="krd-alert-left">
        <svg class="krd-alert-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div>
            <p class="krd-alert-title">2 kelompok melewati tenggat proposal &middot; 16 Oktober 2026</p>
            <p class="krd-alert-desc">SIB 3B-06 dan SIB 3C-06 belum menyelesaikan proposal. Jadwal global tetap berlaku untuk seluruh kelompok.</p>
        </div>
    </div>
    <a href="#" class="krd-btn-kelola-milestone">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>
        </svg>
        Kelola milestone
    </a>
</div>

@endsection
