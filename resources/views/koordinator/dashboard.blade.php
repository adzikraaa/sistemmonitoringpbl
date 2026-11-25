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
    grid-template-columns: minmax(0, 1fr);
    margin-bottom: 24px;
}
.krd-chart-card {
    background: #fff;
    border: 1.5px solid var(--krd-border-warm);
    border-radius: 12px;
    padding: 28px 30px;
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
    display: grid;
    grid-template-columns: minmax(140px, 220px) minmax(0, 1fr) 56px;
    align-items: center;
    gap: 16px;
    margin-bottom: 18px;
}
.krd-bar-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--krd-text-dark);
    line-height: 1.4;
    overflow-wrap: anywhere;
}
.krd-bar-track {
    width: 100%;
    height: 14px;
    background: #F0EBE4;
    border-radius: 999px;
    overflow: hidden;
}
.krd-bar-fill {
    height: 100%;
    border-radius: 999px;
}
.krd-bar-val {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--krd-text-dark);
    text-align: right;
}
.krd-bar-axis {
    display: flex;
    justify-content: space-between;
    padding-left: 236px;
    padding-right: 72px;
    margin-top: 2px;
}
.krd-bar-axis span {
    font-size: 10px;
    color: var(--krd-text-muted);
}

/* ── Donut Chart (Capaian Program) ── */
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
    .krd-chart-card {
        padding: 20px 16px;
    }
    .krd-bar-row {
        grid-template-columns: minmax(0, 1fr) 48px;
        gap: 8px 12px;
    }
    .krd-bar-label {
        grid-column: 1 / -1;
    }
    .krd-bar-axis {
        display: none;
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
    <a href="{{ route('koordinator.dashboard.export', request()->query()) }}" class="krd-btn-ekspor">
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
    <div class="krd-filter-info-badge" style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; background: #FDFAF7; border: 1.5px solid var(--krd-border-input); border-radius: 7px; font-size: 12.5px; color: var(--krd-brown-dark); font-weight: 600;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--krd-text-muted);">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        @if($periodeAktif)
            <span>SEMESTER {{ strtoupper($periodeAktif->nama_periode) }} {{ $periodeAktif->tahun_akademik }} &middot; <span style="font-weight: 500; color: var(--krd-text-muted);">{{ $periodeAktif->tanggal_mulai ? $periodeAktif->tanggal_mulai->format('d M Y') : '' }} – {{ $periodeAktif->tanggal_selesai ? $periodeAktif->tanggal_selesai->format('d M Y') : '' }}</span></span>
        @else
            <span style="color: #9B1C1C; font-weight: 600;">Belum ada periode aktif</span>
        @endif
    </div>

    <div class="krd-filter-divider"></div>

    <form method="GET" action="{{ route('koordinator.dashboard') }}" id="form-filter-kelas" style="display: inline-flex; align-items: center; margin: 0;">
        <div class="krd-filter-select-wrap" style="position: relative; display: inline-flex; align-items: center;">
            <select name="kelas_id" class="krd-filter-select" onchange="document.getElementById('form-filter-kelas').submit()" style="padding-right: 28px; appearance: none; -webkit-appearance: none; cursor: pointer;">
                <option value="">Semua kelas ({{ $totalKelasCount }})</option>
                @foreach($kelasList as $kls)
                    <option value="{{ $kls->id }}" {{ (string)$selectedKelasId === (string)$kls->id ? 'selected' : '' }}>
                        Kelas {{ $kls->nama_kelas }}
                    </option>
                @endforeach
            </select>
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="position: absolute; right: 10px; pointer-events: none; color: var(--krd-text-muted);">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>
    </form>

    <div class="krd-filter-badge">
        <span class="krd-filter-dot"></span>
        @if($selectedKelasId)
            @php $klsTerpilih = $kelasList->firstWhere('id', (int)$selectedKelasId); @endphp
            Kelas {{ $klsTerpilih?->nama_kelas }} &middot; {{ $totalKelompok }} kelompok
        @else
            Lingkup global &middot; {{ $totalKelompok }} kelompok
        @endif
    </div>

    <span class="krd-filter-updated">Diperbarui {{ now()->format('d M, H:i') }} WIB</span>
</div>

{{-- ─── Stats Cards ─────────────────────────────────── --}}
<div class="krd-stats-grid">
    <div class="krd-stat-card">
        <p class="krd-stat-label">Kelas Aktif</p>
        <p class="krd-stat-number">{{ $kelasCount }}</p>
        <p class="krd-stat-sub">{{ $namaKelasList ?: 'Tidak ada kelas' }}</p>
    </div>
    <div class="krd-stat-card">
        <p class="krd-stat-label">Kelompok PBL</p>
        <p class="krd-stat-number">{{ $totalKelompok }}</p>
        <p class="krd-stat-sub">
            @if($kelasCount > 0)
                {{ round($totalKelompok / max(1, $kelasCount), 1) }} kelompok rata-rata per kelas
            @else
                Belum ada kelompok
            @endif
        </p>
    </div>
    <div class="krd-stat-card">
        <p class="krd-stat-label">Mahasiswa</p>
        <p class="krd-stat-number">{{ $mahasiswaCount }}</p>
        <p class="krd-stat-sub">
            @if($totalKelompok > 0)
                {{ round($mahasiswaCount / max(1, $totalKelompok), 1) }} mahasiswa per kelompok
            @else
                Terdaftar di sistem
            @endif
        </p>
    </div>
    <div class="krd-stat-card">
        <p class="krd-stat-label">Dosen Pembimbing</p>
        <p class="krd-stat-number">{{ $dosenPembimbingCount }}</p>
        <p class="krd-stat-sub">{{ $kelompokDitugaskanCount }} dari {{ $totalKelompok }} kelompok ditugaskan</p>
    </div>
</div>

{{-- ─── Charts Row ──────────────────────────────────── --}}
<div class="krd-charts-row">

    {{-- Bar Chart: Penyelesaian Milestone Global --}}
    <div class="krd-chart-card">
        <h2 class="krd-chart-title">Penyelesaian milestone global</h2>
        <p class="krd-chart-subtitle">Target selesai per tahapan berdasarkan {{ $totalKelompok }} kelompok dan seluruh mata kuliah aktif.</p>

        @forelse($milestoneChartData as $row)
            <div class="krd-bar-row">
                <span class="krd-bar-label">{{ $row['label'] }}</span>
                <div class="krd-bar-track">
                    <div class="krd-bar-fill" style="width: {{ $row['pct'] }}%; background-color: {{ $row['color'] }};"></div>
                </div>
                <span class="krd-bar-val">{{ $row['val'] }}/{{ $row['max'] }}</span>
            </div>
        @empty
            <p class="krd-chart-subtitle" style="padding: 24px 0; text-align: center;">Belum ada milestone global pada periode aktif.</p>
        @endforelse

        @if($milestoneChartData !== [])
            <div class="krd-bar-axis">
                <span>0</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
            </div>
        @endif
    </div>

</div>

{{-- ─── Rekapitulasi Seluruh Kelas ─────────────────── --}}
<div class="krd-rekap-card">
    <div class="krd-rekap-header">
        <h2 class="krd-rekap-title">Rekapitulasi seluruh kelas</h2>
        <a href="{{ route('koordinator.master-data.kelas') }}" class="krd-btn-kelola">
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
            @forelse($rekapKelas as $row)
            <tr>
                <td>
                    <span class="krd-kelas-name">{{ $row->nama_kelas }}</span>
                    <span class="krd-kelas-pic">{{ $row->pic }}</span>
                </td>
                <td>{{ $row->mhs }}</td>
                <td>{{ $row->kel }}</td>
                <td>{{ $row->ms_selesai }}</td>
                <td>
                    <span class="krd-mini-bar-track">
                        <span class="krd-mini-bar-fill" style="width: {{ $row->capaian_pct }}%; background: {{ $row->status_type === 'ok' ? '#4A7C59' : '#D97706' }};"></span>
                    </span>
                    <span class="krd-mini-bar-pct">{{ $row->capaian_pct }}%</span>
                </td>
                <td>
                    <span class="krd-status-badge krd-status-badge--{{ $row->status_type }}">
                        <span class="krd-status-dot"></span>
                        {{ $row->status }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('koordinator.kelompok', ['kelas_id' => $row->id]) }}" class="krd-detail-link">Detail &rarr;</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 32px; color: var(--krd-text-muted);">
                    Tidak ada data kelas untuk periode atau filter yang dipilih.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="krd-rekap-footer">
        <span class="krd-rekap-footer-text">
            @if($rekapKelas->total() > 0)
                Menampilkan {{ $rekapKelas->firstItem() }}–{{ $rekapKelas->lastItem() }} dari {{ $rekapKelas->total() }} kelas &middot; {{ $mahasiswaCount }} mahasiswa &middot; {{ $totalKelompok }} kelompok
            @else
                0 kelas
            @endif
        </span>
        <div class="krd-pagination">
            {{-- Prev --}}
            @if($rekapKelas->onFirstPage())
                <button type="button" class="krd-page-btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
            @else
                <a href="{{ $rekapKelas->previousPageUrl() }}" class="krd-page-btn" title="Halaman sebelumnya">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            {{-- Numbers --}}
            @foreach(range(1, max(1, $rekapKelas->lastPage())) as $page)
                @if($page == $rekapKelas->currentPage())
                    <button type="button" class="krd-page-btn krd-page-btn--active">{{ $page }}</button>
                @else
                    <a href="{{ $rekapKelas->url($page) }}" class="krd-page-btn">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($rekapKelas->hasMorePages())
                <a href="{{ $rekapKelas->nextPageUrl() }}" class="krd-page-btn" title="Halaman berikutnya">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @else
                <button type="button" class="krd-page-btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            @endif
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
            @if($overdueAlert)
                <p class="krd-alert-title">{{ $overdueAlert['remaining'] }} target melewati tenggat {{ $overdueAlert['label'] }} &middot; {{ $overdueAlert['deadline']->format('d M Y') }}</p>
                <p class="krd-alert-desc">Target berasal dari kelompok dan mata kuliah pada lingkup filter yang belum dinyatakan selesai oleh dosen.</p>
            @elseif($milestoneChartData === [])
                <p class="krd-alert-title">Milestone global belum tersedia</p>
                <p class="krd-alert-desc">Tambahkan milestone global agar capaian program dapat dihitung dari data pelaksanaan.</p>
            @else
                <p class="krd-alert-title">Tidak ada target milestone yang melewati tenggat</p>
                <p class="krd-alert-desc">Seluruh target yang telah jatuh tempo pada lingkup filter sudah selesai.</p>
            @endif
        </div>
    </div>
    <a href="{{ route('koordinator.milestone-global.index') }}" class="krd-btn-kelola-milestone">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>
        </svg>
        Kelola milestone
    </a>
</div>

@endsection
