@extends('layouts.dosen')

@section('title', 'Dashboard - Ruang Dosen')
@section('breadcrumb', 'Dashboard')

@push('styles')
<style>
/* ── Dashboard Dosen — page-level styles ─────────────────────────── */
.dd-container {
    padding: 28px 32px 56px;
    width: 100%;
    box-sizing: border-box;
}

/* Eyebrow */
.dd-eyebrow {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: #8C7A6A;
    margin: 0 0 6px;
}

/* Header row */
.dd-header-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 28px;
}

.dd-greeting {
    font-family: 'DM Serif Display', serif;
    font-size: 40px;
    font-weight: 400;
    line-height: 1.1;
    color: #1C1008;
    margin: 0 0 6px;
}

.dd-subtitle {
    font-size: 14px;
    color: #8C7A6A;
    margin: 0;
}

/* Refresh button */
.dd-refresh-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    background: #fff;
    border: 1.5px solid #CFC7BC;
    border-radius: 10px;
    color: #2D2621;
    font-size: 13.5px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
    align-self: flex-start;
    margin-top: 8px;
    transition: background .15s, border-color .15s;
    text-decoration: none;
}
.dd-refresh-btn:hover { background: #F5EFE6; border-color: #B5AA9C; }

/* ── Summary grid ─────────────────────────────────────────────────── */
.dd-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 28px;
}

.dd-summary-card {
    background: #fff;
    border: 1px solid #E8DDD0;
    border-radius: 14px;
    padding: 20px 22px;
    position: relative;
}

.dd-summary-card__label {
    font-size: 12.5px;
    color: #8C7A6A;
    margin: 0 0 10px;
    font-weight: 500;
}

.dd-summary-card__value {
    font-family: 'DM Serif Display', serif;
    font-size: 40px;
    font-weight: 400;
    line-height: 1;
    color: #1C1008;
    margin: 0 0 8px;
}

.dd-summary-card__sub {
    font-size: 12px;
    color: #8C7A6A;
    margin: 0;
}

.dd-summary-card__dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    position: absolute;
    top: 20px;
    right: 20px;
}

/* ── Peta Bimbingan card ──────────────────────────────────────────── */
.dd-peta-card {
    background: #fff;
    border: 1px solid #E8DDD0;
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 24px;
}

.dd-peta-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.dd-section-label {
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #8C7A6A;
    margin: 0 0 4px;
}

.dd-section-title {
    font-family: 'DM Serif Display', serif;
    font-size: 22px;
    font-weight: 400;
    color: #1C1008;
    margin: 0;
}

/* Filter tabs */
.dd-tabs {
    display: inline-flex;
    background: #F5EFE6;
    border-radius: 10px;
    padding: 4px;
    gap: 2px;
    flex-shrink: 0;
}

.dd-tab {
    padding: 7px 16px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    border: none;
    background: transparent;
    color: #8C7A6A;
    transition: background .15s, color .15s;
    white-space: nowrap;
}

.dd-tab--active {
    background: #2C1A0E;
    color: #FAF7F3;
    font-weight: 600;
}

/* Table */
.dd-table {
    width: 100%;
    border-collapse: collapse;
}

.dd-table thead tr {
    border-bottom: 1px solid #E8DDD0;
}

.dd-table th {
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #8C7A6A;
    padding: 0 0 12px;
    text-align: left;
}

.dd-table th:not(:first-child) { padding-left: 16px; }

.dd-table tbody tr {
    border-bottom: 1px solid #F0EAE2;
    transition: background .1s;
}

.dd-table tbody tr:last-child { border-bottom: none; }
.dd-table tbody tr:hover { background: #FAF7F3; }

.dd-table td {
    padding: 16px 0;
    font-size: 13.5px;
    color: #1C1008;
    vertical-align: middle;
}

.dd-table td:not(:first-child) { padding-left: 16px; }

.dd-kelompok-name {
    font-weight: 600;
    font-size: 14px;
    margin: 0 0 2px;
}

.dd-kelompok-kelas {
    font-size: 12px;
    color: #8C7A6A;
    margin: 0;
}

/* Progress bar */
.dd-progress-wrap {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 120px;
}

.dd-progress-pct {
    font-size: 12.5px;
    font-weight: 600;
    color: #1C1008;
}

.dd-progress-bar-bg {
    width: 100%;
    height: 6px;
    background: #E8DDD0;
    border-radius: 99px;
    overflow: hidden;
}

.dd-progress-bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width .4s ease;
}

/* Status badge */
.dd-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 99px;
    font-size: 12.5px;
    font-weight: 600;
    white-space: nowrap;
}

.dd-badge--berjalan  { background: #E8F3EC; color: #2E7D4F; }
.dd-badge--terlambat { background: #FDECEA; color: #C0392B; }
.dd-badge--selesai   { background: #EDE8E2; color: #5C4B35; }

.dd-badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.dd-badge--berjalan  .dd-badge-dot { background: #2E7D4F; }
.dd-badge--terlambat .dd-badge-dot { background: #C0392B; }
.dd-badge--selesai   .dd-badge-dot { background: #5C4B35; }

.dd-time {
    font-size: 12.5px;
    color: #8C7A6A;
    white-space: nowrap;
}

/* ── Bottom 2-col grid ───────────────────────────────────────────── */
.dd-bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* Sinyal card (dark) */
.dd-sinyal-card {
    background: #2C1A0E;
    border-radius: 16px;
    padding: 28px 30px;
    color: #F5EFE6;
    display: flex;
    flex-direction: column;
    gap: 0;
    position: relative;
}

.dd-sinyal-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #A07C5A;
    margin: 0 0 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dd-sinyal-info-btn {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 1.5px solid #6B4F35;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: #A07C5A;
    cursor: default;
    flex-shrink: 0;
}

.dd-sinyal-title {
    font-family: 'DM Serif Display', serif;
    font-size: 26px;
    font-weight: 400;
    line-height: 1.2;
    color: #FAF7F3;
    margin: 0 0 12px;
}

.dd-sinyal-desc {
    font-size: 13.5px;
    color: #C4A882;
    line-height: 1.6;
    margin: 0 0 24px;
    flex: 1;
}

.dd-sinyal-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #F5EFE6;
    border: none;
    border-radius: 10px;
    color: #2C1A0E;
    font-size: 13px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    align-self: flex-start;
    transition: background .15s;
    text-decoration: none;
}
.dd-sinyal-btn:hover { background: #EDE4D8; }

.dd-sinyal-empty-title {
    font-family: 'DM Serif Display', serif;
    font-size: 26px;
    font-weight: 400;
    line-height: 1.2;
    color: #FAF7F3;
    margin: 0;
}

/* Review card (white) */
.dd-review-card {
    background: #fff;
    border: 1px solid #E8DDD0;
    border-radius: 16px;
    padding: 24px 28px;
    display: flex;
    flex-direction: column;
}

.dd-review-list {
    list-style: none;
    margin: 16px 0 0;
    padding: 0;
    display: flex;
    flex-direction: column;
}

.dd-review-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 15px 0;
    border-bottom: 1px solid #F0EAE2;
    cursor: pointer;
    transition: background .1s;
    border-radius: 8px;
    margin: 0 -8px;
    padding-left: 8px;
    padding-right: 8px;
}

.dd-review-item:last-child { border-bottom: none; }
.dd-review-item:hover { background: #FAF7F3; }

.dd-review-icon {
    width: 36px;
    height: 36px;
    background: #FEF3E8;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.dd-review-text {
    flex: 1;
    min-width: 0;
}

.dd-review-type {
    font-size: 13.5px;
    font-weight: 600;
    color: #1C1008;
    margin: 0 0 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dd-review-who {
    font-size: 12px;
    color: #8C7A6A;
    margin: 0;
}

.dd-review-chevron {
    color: #CFC7BC;
    flex-shrink: 0;
}

/* Empty state */
.dd-empty-row td {
    text-align: center;
    padding: 32px;
    color: #8C7A6A;
    font-size: 13.5px;
}

/* Responsive adjustments */
@media (max-width: 1100px) {
    .dd-summary-grid { grid-template-columns: repeat(2, 1fr); }
    .dd-bottom-grid  { grid-template-columns: 1fr; }
}

@media (max-width: 700px) {
    .dd-container { padding: 20px 16px 40px; }
    .dd-summary-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
    .dd-greeting { font-size: 28px; }
    .dd-peta-header { flex-direction: column; align-items: flex-start; }
}
</style>
@endpush

@section('content')

<div class="dd-container">

    {{-- ── Eyebrow ─────────────────────────────────────────────────── --}}
    <p class="dd-eyebrow">
        RUANG DOSEN
        @if(!empty($semester))
            · {{ strtoupper($semester['nama']) }} {{ $semester['tahun'] }}
        @else
            · SEMESTER GENAP 2026 / 2027
        @endif
    </p>

    {{-- ── Greeting row ────────────────────────────────────────────── --}}
    <div class="dd-header-row">
        <div>
            <h1 class="dd-greeting">
                Selamat datang, {{ \Illuminate\Support\Str::before($userName ?? 'Dosen', ',') }}.
            </h1>
            <p class="dd-subtitle">Ringkasan kelompok yang membutuhkan perhatianmu hari ini.</p>
        </div>
        <button class="dd-refresh-btn" onclick="window.location.reload()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"/>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
            </svg>
            Perbarui data
        </button>
    </div>

    {{-- ── 4 Stat Cards ────────────────────────────────────────────── --}}
    @php
        $cards = [
            [
                'label' => 'Kelompok bimbingan',
                'value' => $summaryData['total_kelompok'] ?? 0,
                'sub'   => ($summaryData['total_kelompok'] ?? 0) > 0
                            ? 'Semua kelompok aktif'
                            : 'Belum ada kelompok',
                'dot'   => '#4A7C59',
            ],
            [
                'label' => 'Laporan masuk',
                'value' => $summaryData['laporan_masuk'] ?? 0,
                'sub'   => ($summaryData['belum_review'] ?? 0) . ' belum direview',
                'dot'   => '#B8934A',
            ],
            [
                'label' => 'Perlu revisi',
                'value' => $summaryData['perlu_revisi'] ?? 0,
                'sub'   => 'Turun 1 dari minggu lalu',
                'dot'   => '#B8934A',
            ],
            [
                'label' => 'Kelompok terlambat',
                'value' => $summaryData['terlambat'] ?? 0,
                'sub'   => ($summaryData['terlambat'] ?? 0) > 0 ? 'Perlu perhatian' : 'Semua tepat waktu',
                'dot'   => ($summaryData['terlambat'] ?? 0) > 0 ? '#C0392B' : '#4A7C59',
            ],
        ];
    @endphp

    <div class="dd-summary-grid">
        @foreach($cards as $card)
        <div class="dd-summary-card">
            <div class="dd-summary-card__dot" style="background: {{ $card['dot'] }};"></div>
            <p class="dd-summary-card__label">{{ $card['label'] }}</p>
            <p class="dd-summary-card__value">{{ $card['value'] }}</p>
            <p class="dd-summary-card__sub">{{ $card['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Peta Bimbingan ──────────────────────────────────────────── --}}
    <div class="dd-peta-card">

        <div class="dd-peta-header">
            <div>
                <p class="dd-section-label">PETA BIMBINGAN</p>
                <h2 class="dd-section-title">Kelompok yang kamu dampingi</h2>
            </div>
            <div class="dd-tabs">
                <button class="dd-tab dd-tab--active" data-filter="semua">Semua</button>
                <button class="dd-tab" data-filter="perlu-perhatian">Perlu perhatian</button>
            </div>
        </div>

        <table class="dd-table">
            <thead>
                <tr>
                    <th>KELOMPOK</th>
                    <th>PROYEK</th>
                    <th>PROGRES</th>
                    <th>STATUS</th>
                    <th>UPDATE TERAKHIR</th>
                </tr>
            </thead>
            <tbody id="kelompokBody">
                @forelse($kelompokData ?? [] as $k)
                @php
                    $status = strtolower($k['status'] ?? 'berjalan');
                    $progress = $k['progress'] ?? 0;
                    $barColor = match($status) {
                        'berjalan' => '#4A7C59',
                        'terlambat' => '#B8934A',
                        'selesai'  => '#5C4B35',
                        default    => '#4A7C59',
                    };
                    $statusLabel = match($status) {
                        'berjalan' => 'Berjalan',
                        'terlambat' => 'Terlambat',
                        'selesai'  => 'Selesai',
                        default    => ucfirst($status),
                    };
                @endphp
                <tr data-status="{{ $status }}">
                    <td>
                        <p class="dd-kelompok-name">{{ $k['nama'] ?? 'Kelompok' }}</p>
                        <p class="dd-kelompok-kelas">#{{ $k['kelas'] ?? '-' }}</p>
                    </td>
                    <td>{{ $k['proyek'] ?? '-' }}</td>
                    <td>
                        <div class="dd-progress-wrap">
                            <span class="dd-progress-pct">{{ $progress }}%</span>
                            <div class="dd-progress-bar-bg">
                                <div class="dd-progress-bar-fill"
                                     style="width: {{ $progress }}%; background: {{ $barColor }};"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="dd-badge dd-badge--{{ $status }}">
                            <span class="dd-badge-dot"></span>
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="dd-time">
                        @if(isset($k['last_update']) && $k['last_update'] instanceof \Carbon\Carbon)
                            {{ $k['last_update']->diffForHumans() }}
                        @else
                            {{ $k['last_update'] ?? '-' }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="dd-empty-row">
                    <td colspan="5">Belum ada kelompok bimbingan untuk ditampilkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    {{-- ── Bottom 2-col ─────────────────────────────────────────────── --}}
    <div class="dd-bottom-grid">

        {{-- Sinyal Minggu Ini (dark card) --}}
        <div class="dd-sinyal-card">
            <p class="dd-sinyal-label">
                SINYAL MINGGU INI
                <span class="dd-sinyal-info-btn">i</span>
            </p>

            @if(!empty($sinyalKelompok))
                <h3 class="dd-sinyal-title">Satu kelompok<br>perlu disapa.</h3>
                <p class="dd-sinyal-desc">
                    {{ $sinyalKelompok['nama'] ?? 'Kelompok ini' }} belum mengirim pembaruan
                    sejak {{ $sinyalKelompok['hari_lalu'] ?? 3 }} hari lalu.
                    Sedikit pertanyaan mungkin cukup untuk membantu mereka
                    kembali menemukan ritmenya.
                </p>
                <button class="dd-sinyal-btn">Kirim pengingat →</button>
            @else
                <h3 class="dd-sinyal-empty-title">Semua kelompok<br>aktif minggu ini! 🎉</h3>
            @endif
        </div>

        {{-- Yang Menunggu Review (white card) --}}
        <div class="dd-review-card">
            <p class="dd-section-label">YANG MENUNGGU REVIEW</p>

            <ul class="dd-review-list">
                @forelse($pendingReviews ?? [] as $review)
                <li class="dd-review-item">
                    <div class="dd-review-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#B8934A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>
                    <div class="dd-review-text">
                        <p class="dd-review-type">{{ $review['type'] ?? 'Dokumen' }} · {{ $review['label'] ?? '' }}</p>
                        <p class="dd-review-who">{{ $review['name'] ?? '' }}</p>
                    </div>
                    <svg class="dd-review-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </li>
                @empty
                <li style="padding: 32px 0; text-align: center; color: #8C7A6A; font-size: 13.5px;">
                    Tidak ada item yang menunggu review.
                </li>
                @endforelse
            </ul>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
// ── Tab filter: Semua vs Perlu perhatian ───────────────────────────
document.querySelectorAll('.dd-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        // Toggle active class
        document.querySelectorAll('.dd-tab').forEach(function(t) {
            t.classList.remove('dd-tab--active');
        });
        this.classList.add('dd-tab--active');

        var filter = this.dataset.filter;
        var rows = document.querySelectorAll('#kelompokBody tr:not(.dd-empty-row)');

        rows.forEach(function(row) {
            if (filter === 'semua') {
                row.style.display = '';
            } else {
                // "Perlu perhatian" = only show terlambat
                row.style.display = (row.dataset.status === 'terlambat') ? '' : 'none';
            }
        });

        // Show empty state if all rows hidden
        var visibleCount = 0;
        rows.forEach(function(r) { if (r.style.display !== 'none') visibleCount++; });
        var emptyRow = document.getElementById('ddEmptyFiltered');
        if (visibleCount === 0 && filter !== 'semua') {
            if (!emptyRow) {
                var tr = document.createElement('tr');
                tr.id = 'ddEmptyFiltered';
                tr.className = 'dd-empty-row';
                tr.innerHTML = '<td colspan="5">Tidak ada kelompok yang memerlukan perhatian.</td>';
                document.getElementById('kelompokBody').appendChild(tr);
            }
        } else if (emptyRow) {
            emptyRow.remove();
        }
    });
});
</script>
@endpush
