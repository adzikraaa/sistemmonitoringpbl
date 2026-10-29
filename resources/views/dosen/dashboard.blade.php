@extends('layouts.dosen')

@section('title', 'Dashboard - Ruang Dosen')
@section('breadcrumb', 'Ruang dosen')

@push('styles')
<style>
/* ============================================================
   DASHBOARD DOSEN – page-specific styles
   ============================================================ */

/* ─── Page header ──────────────────────────────────────────── */
.dsn-hero {
    padding: 32px 32px 0;
}

.dsn-hero__meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--sim-text-muted);
    margin-bottom: 6px;
}

.dsn-hero__meta span { color: var(--sim-text-light); }

.dsn-hero__title {
    font-family: 'DM Serif Display', serif;
    font-size: 32px;
    font-weight: 400;
    color: var(--sim-text-dark);
    line-height: 1.2;
    margin: 0 0 6px;
}

.dsn-hero__subtitle {
    font-size: 14px;
    color: var(--sim-text-muted);
    margin: 0 0 24px;
}

.dsn-hero__actions {
    display: flex;
    justify-content: flex-end;
    margin-top: -40px;
}

.dsn-btn-refresh {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    border: 1px solid var(--sim-border);
    background: var(--sim-white);
    font-size: 13px;
    font-weight: 500;
    color: var(--sim-text-body);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
}

.dsn-btn-refresh:hover {
    background: var(--sim-cream);
    border-color: var(--sim-border-light);
}

.dsn-btn-refresh svg {
    transition: transform 0.3s;
}

.dsn-btn-refresh:hover svg {
    transform: rotate(45deg);
}

/* ─── Stat cards ───────────────────────────────────────────── */
.dsn-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    margin: 0 32px 24px;
    background: var(--sim-white);
    border: 1px solid var(--sim-border);
    border-radius: 14px;
    overflow: hidden;
}

.dsn-stat {
    padding: 20px 24px;
    position: relative;
}

.dsn-stat:not(:last-child)::after {
    content: '';
    position: absolute;
    right: 0;
    top: 20%;
    height: 60%;
    width: 1px;
    background: var(--sim-border);
}

.dsn-stat__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.dsn-stat__label {
    font-size: 12px;
    font-weight: 500;
    color: var(--sim-text-muted);
}

.dsn-stat__dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.dsn-stat__dot--green  { background: var(--sim-green); }
.dsn-stat__dot--gold   { background: var(--sim-gold); }
.dsn-stat__dot--brown  { background: var(--sim-brown-warm); }
.dsn-stat__dot--red    { background: #C44B3F; }

.dsn-stat__value {
    font-family: 'DM Serif Display', serif;
    font-size: 36px;
    font-weight: 400;
    color: var(--sim-text-dark);
    line-height: 1;
    margin-bottom: 4px;
}

.dsn-stat__desc {
    font-size: 12px;
    color: var(--sim-text-light);
}

/* ─── Peta Bimbingan section ───────────────────────────────── */
.dsn-section {
    margin: 0 32px 24px;
    background: var(--sim-white);
    border: 1px solid var(--sim-border);
    border-radius: 14px;
    overflow: hidden;
}

.dsn-section__header {
    padding: 24px 24px 20px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.dsn-section__label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--sim-text-light);
    margin-bottom: 4px;
}

.dsn-section__title {
    font-family: 'DM Serif Display', serif;
    font-size: 22px;
    font-weight: 400;
    color: var(--sim-text-dark);
    line-height: 1.2;
    margin: 0;
}

.dsn-filter-tabs {
    display: flex;
    gap: 0;
    border: 1px solid var(--sim-border);
    border-radius: 8px;
    overflow: hidden;
    align-self: center;
}

.dsn-filter-tab {
    padding: 7px 16px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--sim-text-muted);
    background: var(--sim-white);
    border: none;
    cursor: pointer;
    transition: all 0.15s;
    border-right: 1px solid var(--sim-border);
}

.dsn-filter-tab:last-child { border-right: none; }

.dsn-filter-tab:hover {
    background: var(--sim-cream);
}

.dsn-filter-tab--active {
    background: var(--sim-brown-dark);
    color: var(--sim-cream);
}

.dsn-filter-tab--active:hover {
    background: var(--sim-brown);
    color: var(--sim-cream);
}

/* ─── Table ────────────────────────────────────────────────── */
.dsn-table {
    width: 100%;
    border-collapse: collapse;
}

.dsn-table thead th {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--sim-text-light);
    text-align: left;
    padding: 0 24px 14px;
    border-bottom: 1px solid var(--sim-border);
}

.dsn-table tbody tr {
    transition: background 0.1s;
}

.dsn-table tbody tr:hover {
    background: var(--sim-off-white);
}

.dsn-table tbody td {
    padding: 16px 24px;
    border-bottom: 1px solid var(--sim-border-light);
    vertical-align: middle;
    font-size: 13.5px;
    color: var(--sim-text-body);
}

.dsn-table tbody tr:last-child td {
    border-bottom: none;
}

.dsn-table__kelompok-name {
    font-weight: 600;
    color: var(--sim-text-dark);
    margin-bottom: 2px;
}

.dsn-table__kelompok-kelas {
    font-size: 12px;
    color: var(--sim-text-light);
}

.dsn-table__proyek {
    max-width: 220px;
}

/* Progress bar */
.dsn-progress {
    display: flex;
    align-items: center;
    gap: 10px;
}

.dsn-progress__bar {
    width: 80px;
    height: 6px;
    background: var(--sim-cream-dark);
    border-radius: 3px;
    overflow: hidden;
    flex-shrink: 0;
}

.dsn-progress__fill {
    height: 100%;
    border-radius: 3px;
    background: var(--sim-brown-warm);
    transition: width 0.5s ease;
}

.dsn-progress__text {
    font-size: 13px;
    font-weight: 500;
    color: var(--sim-text-body);
    white-space: nowrap;
}

/* Status badges */
.dsn-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 500;
    padding: 4px 12px;
    border-radius: 20px;
    white-space: nowrap;
}

.dsn-badge::before {
    content: '';
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}

.dsn-badge--berjalan {
    color: var(--sim-green);
    background: var(--sim-green-bg);
}
.dsn-badge--berjalan::before { background: var(--sim-green); }

.dsn-badge--terlambat {
    color: #A0522D;
    background: #FFF3E6;
}
.dsn-badge--terlambat::before { background: #C44B3F; }

.dsn-badge--selesai {
    color: var(--sim-text-muted);
    background: var(--sim-cream);
}
.dsn-badge--selesai::before { background: var(--sim-text-muted); }

.dsn-table__update {
    font-size: 12.5px;
    color: var(--sim-text-light);
    white-space: nowrap;
}

/* ─── Bottom row: Sinyal + Review ──────────────────────────── */
.dsn-bottom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin: 0 32px 32px;
}

/* Sinyal card */
.dsn-sinyal {
    background: var(--sim-brown-dark);
    color: var(--sim-cream);
    border-radius: 14px;
    padding: 28px 28px 24px;
    position: relative;
    overflow: hidden;
}

.dsn-sinyal__label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.4);
    margin-bottom: 14px;
}

.dsn-sinyal__badge {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 28px;
    height: 28px;
    background: rgba(255,255,255,0.12);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    color: var(--sim-cream);
}

.dsn-sinyal__title {
    font-family: 'DM Serif Display', serif;
    font-size: 26px;
    font-weight: 400;
    line-height: 1.25;
    margin-bottom: 14px;
    max-width: 280px;
}

.dsn-sinyal__desc {
    font-size: 13px;
    line-height: 1.6;
    color: rgba(255,255,255,0.55);
    margin-bottom: 20px;
    max-width: 320px;
}

.dsn-sinyal__btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: 8px;
    border: none;
    background: var(--sim-brown-warm);
    color: var(--sim-cream);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
}

.dsn-sinyal__btn:hover {
    background: var(--sim-brown-light);
}

.dsn-sinyal__btn svg {
    transition: transform 0.2s;
}

.dsn-sinyal__btn:hover svg {
    transform: translateX(3px);
}

/* Empty sinyal state */
.dsn-sinyal--empty {
    background: var(--sim-green-bg);
    color: var(--sim-text-dark);
}

.dsn-sinyal--empty .dsn-sinyal__label {
    color: var(--sim-green);
}

.dsn-sinyal--empty .dsn-sinyal__title {
    color: var(--sim-green);
    font-size: 22px;
}

.dsn-sinyal--empty .dsn-sinyal__desc {
    color: var(--sim-text-muted);
}

/* Review card */
.dsn-review {
    background: var(--sim-white);
    border: 1px solid var(--sim-border);
    border-radius: 14px;
    padding: 24px;
}

.dsn-review__label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--sim-text-light);
    margin-bottom: 16px;
}

.dsn-review__list {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.dsn-review__item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid var(--sim-border-light);
    text-decoration: none;
    transition: background 0.1s;
    border-radius: 8px;
    cursor: pointer;
}

.dsn-review__item:last-child {
    border-bottom: none;
}

.dsn-review__item:hover {
    background: var(--sim-off-white);
    padding-left: 8px;
    padding-right: 8px;
}

.dsn-review__icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 14px;
}

.dsn-review__icon--logbook {
    background: #FFF3E6;
    color: var(--sim-brown-warm);
}

.dsn-review__icon--proposal {
    background: var(--sim-green-bg);
    color: var(--sim-green);
}

.dsn-review__info {
    flex: 1;
    min-width: 0;
}

.dsn-review__title {
    font-size: 13.5px;
    font-weight: 500;
    color: var(--sim-text-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dsn-review__arrow {
    color: var(--sim-text-light);
    flex-shrink: 0;
    transition: transform 0.15s;
}

.dsn-review__item:hover .dsn-review__arrow {
    transform: translateX(3px);
    color: var(--sim-text-body);
}

/* Empty review state */
.dsn-review__empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
}

.dsn-review__empty-icon {
    font-size: 32px;
    margin-bottom: 12px;
}

.dsn-review__empty-text {
    font-size: 14px;
    color: var(--sim-text-muted);
}

/* ─── Empty table ──────────────────────────────────────────── */
.dsn-table__empty {
    text-align: center;
    padding: 40px 24px !important;
    color: var(--sim-text-muted);
    font-size: 14px;
}

/* ─── Responsive ───────────────────────────────────────────── */
@media (max-width: 1024px) {
    .dsn-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .dsn-stat:nth-child(2)::after {
        display: none;
    }

    .dsn-stat:nth-child(1),
    .dsn-stat:nth-child(2) {
        border-bottom: 1px solid var(--sim-border);
    }

    .dsn-bottom {
        grid-template-columns: 1fr;
    }

    .dsn-table {
        display: block;
        overflow-x: auto;
    }
}

@media (max-width: 768px) {
    .dsn-hero {
        padding: 20px 16px 0;
    }

    .dsn-hero__title {
        font-size: 24px;
    }

    .dsn-hero__actions {
        margin-top: 8px;
    }

    .dsn-stats {
        grid-template-columns: 1fr 1fr;
        margin: 0 16px 16px;
    }

    .dsn-section {
        margin: 0 16px 16px;
    }

    .dsn-bottom {
        margin: 0 16px 16px;
        grid-template-columns: 1fr;
    }

    .dsn-sinyal__title {
        font-size: 22px;
    }
}

@media (max-width: 480px) {
    .dsn-stats {
        grid-template-columns: 1fr;
    }

    .dsn-stat::after {
        display: none !important;
    }

    .dsn-stat:not(:last-child) {
        border-bottom: 1px solid var(--sim-border);
    }
}
</style>
@endpush

@section('content')

{{-- ─── Hero header ──────────────────────────────────────────── --}}
<div class="dsn-hero">
    <div class="dsn-hero__meta">
        RUANG DOSEN
        <span>·</span>
        {{ $periode?->nama_periode ?? 'Semester Aktif' }}
    </div>

    <h1 class="dsn-hero__title">Selamat datang, {{ explode(' ', $dosen->nama)[0] }}.</h1>
    <p class="dsn-hero__subtitle">Ringkasan kelompok yang membutuhkan perhatianmu hari ini.</p>

    <div class="dsn-hero__actions">
        <a href="{{ route('dosen.dashboard') }}" class="dsn-btn-refresh">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"/>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
            </svg>
            Perbarui data
        </a>
    </div>
</div>

{{-- ─── Stat cards ───────────────────────────────────────────── --}}
<div class="dsn-stats">
    <div class="dsn-stat">
        <div class="dsn-stat__header">
            <span class="dsn-stat__label">Kelompok bimbingan</span>
            <span class="dsn-stat__dot dsn-stat__dot--green"></span>
        </div>
        <div class="dsn-stat__value">{{ $totalKelompok }}</div>
        <div class="dsn-stat__desc">Semua kelompok aktif</div>
    </div>

    <div class="dsn-stat">
        <div class="dsn-stat__header">
            <span class="dsn-stat__label">Laporan masuk</span>
            <span class="dsn-stat__dot dsn-stat__dot--gold"></span>
        </div>
        <div class="dsn-stat__value">{{ $laporanMasuk }}</div>
        <div class="dsn-stat__desc">
            @if($laporanMasuk > 0)
                {{ $laporanMasuk }} belum direview
            @else
                Semua sudah direview
            @endif
        </div>
    </div>

    <div class="dsn-stat">
        <div class="dsn-stat__header">
            <span class="dsn-stat__label">Perlu revisi</span>
            <span class="dsn-stat__dot dsn-stat__dot--brown"></span>
        </div>
        <div class="dsn-stat__value">{{ $perluRevisi }}</div>
        <div class="dsn-stat__desc">
            @if($perluRevisi > 0)
                Turun 1 dari minggu lalu
            @else
                Tidak ada revisi pending
            @endif
        </div>
    </div>

    <div class="dsn-stat">
        <div class="dsn-stat__header">
            <span class="dsn-stat__label">Kelompok terlambat</span>
            <span class="dsn-stat__dot dsn-stat__dot--red"></span>
        </div>
        <div class="dsn-stat__value">{{ $kelompokTerlambat }}</div>
        <div class="dsn-stat__desc">
            @if($kelompokTerlambat > 0)
                Perlu perhatian
            @else
                Semua on track
            @endif
        </div>
    </div>
</div>

{{-- ─── Peta Bimbingan ───────────────────────────────────────── --}}
<div class="dsn-section">
    <div class="dsn-section__header">
        <div>
            <p class="dsn-section__label">PETA BIMBINGAN</p>
            <h2 class="dsn-section__title">Kelompok yang kamu dampingi</h2>
        </div>

        <div class="dsn-filter-tabs">
            <button class="dsn-filter-tab dsn-filter-tab--active" data-filter="semua" onclick="filterKelompok('semua', this)">Semua</button>
            <button class="dsn-filter-tab" data-filter="perlu-perhatian" onclick="filterKelompok('perlu-perhatian', this)">Perlu perhatian</button>
        </div>
    </div>

    <table class="dsn-table" id="kelompok-table">
        <thead>
            <tr>
                <th>Kelompok</th>
                <th>Proyek</th>
                <th>Progres</th>
                <th>Status</th>
                <th>Update Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kelompokData as $kelompok)
                <tr data-status="{{ $kelompok['status'] }}">
                    <td>
                        <div class="dsn-table__kelompok-name">{{ $kelompok['nama'] }}</div>
                        <div class="dsn-table__kelompok-kelas">#{{ $kelompok['kelas'] }}</div>
                    </td>
                    <td class="dsn-table__proyek">{{ $kelompok['proyek'] }}</td>
                    <td>
                        <div class="dsn-progress">
                            <div class="dsn-progress__bar">
                                <div class="dsn-progress__fill" style="width: {{ $kelompok['progress'] }}%"></div>
                            </div>
                            <span class="dsn-progress__text">{{ $kelompok['progress'] }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="dsn-badge dsn-badge--{{ $kelompok['status'] }}">
                            {{ ucfirst($kelompok['status']) }}
                        </span>
                    </td>
                    <td class="dsn-table__update">
                        {{ $kelompok['last_update']?->diffForHumans() ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="dsn-table__empty">
                        Belum ada kelompok bimbingan di periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ─── Bottom: Sinyal + Review ──────────────────────────────── --}}
<div class="dsn-bottom">

    {{-- Sinyal Minggu Ini --}}
    @if($sinyalKelompok)
        <div class="dsn-sinyal">
            <p class="dsn-sinyal__label">SINYAL MINGGU INI</p>
            <span class="dsn-sinyal__badge">1</span>

            <h3 class="dsn-sinyal__title">Satu kelompok<br>perlu disapa.</h3>

            <p class="dsn-sinyal__desc">
                {{ $sinyalKelompok->nama_kelompok }} belum mengirim pembaruan sejak
                {{ $sinyalKelompok->logbookMilestone->sortByDesc('updated_at')->first()?->updated_at?->diffForHumans() ?? '3 hari lalu' }}.
                Sedikit pertanyaan mungkin cukup untuk membantu mereka kembali menemukan ritmenya.
            </p>

            <button class="dsn-sinyal__btn" onclick="alert('Fitur kirim pengingat akan segera tersedia')">
                Kirim pengingat
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </div>
    @else
        <div class="dsn-sinyal dsn-sinyal--empty">
            <p class="dsn-sinyal__label">SINYAL MINGGU INI</p>

            <h3 class="dsn-sinyal__title">Semua kelompok<br>aktif minggu ini! 🎉</h3>

            <p class="dsn-sinyal__desc">
                Tidak ada kelompok yang memerlukan perhatian khusus. Semua berjalan sesuai ritme.
            </p>
        </div>
    @endif

    {{-- Yang Menunggu Review --}}
    <div class="dsn-review">
        <p class="dsn-review__label">YANG MENUNGGU REVIEW</p>

        @if($pendingReviews->count() > 0)
            <div class="dsn-review__list">
                @foreach($pendingReviews as $review)
                    <div class="dsn-review__item">
                        <div class="dsn-review__icon dsn-review__icon--{{ $review['type'] === 'Logbook' ? 'logbook' : 'proposal' }}">
                            @if($review['type'] === 'Logbook')
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                            @else
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="dsn-review__info">
                            <div class="dsn-review__title">{{ $review['label'] }}</div>
                        </div>
                        <svg class="dsn-review__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </div>
                @endforeach
            </div>
        @else
            <div class="dsn-review__empty">
                <div class="dsn-review__empty-icon">✅</div>
                <div class="dsn-review__empty-text">Semua sudah direview. Kerja bagus!</div>
            </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script>
function filterKelompok(filter, btn) {
    // Update active tab
    document.querySelectorAll('.dsn-filter-tab').forEach(t => t.classList.remove('dsn-filter-tab--active'));
    btn.classList.add('dsn-filter-tab--active');

    // Filter rows
    const rows = document.querySelectorAll('#kelompok-table tbody tr[data-status]');
    rows.forEach(row => {
        if (filter === 'semua') {
            row.style.display = '';
        } else if (filter === 'perlu-perhatian') {
            const status = row.getAttribute('data-status');
            row.style.display = (status === 'terlambat') ? '' : 'none';
        }
    });
}
</script>
@endpush
