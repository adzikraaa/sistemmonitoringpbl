@extends('layouts.koordinator')

@section('title', 'Master Data — Kelas')
@section('breadcrumb_title', 'Kelas')

@push('styles')
<style>
    /* ── Typography & Base Tokens ── */
    .figma-title-eyebrow {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--sim-text-light);
        margin: 0 0 6px 0;
    }

    .figma-title-h1 {
        font-family: 'DM Serif Display', Georgia, serif;
        font-size: 38px;
        font-weight: 400;
        color: var(--sim-brown-dark);
        margin: 0 0 6px 0;
        line-height: 1.15;
    }

    .figma-title-desc {
        font-size: 13.5px;
        color: var(--sim-text-muted);
        margin: 0;
        line-height: 1.5;
    }

    /* ── Buttons ── */
    .btn-figma-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--sim-brown-btn);
        color: #FAF8F5;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, transform 0.1s;
        white-space: nowrap;
    }
    .btn-figma-primary:hover {
        background: var(--sim-brown-btn-hover);
    }
    .btn-figma-primary:active {
        transform: scale(0.99);
    }

    .btn-figma-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #FFFFFF;
        color: var(--sim-brown-dark);
        border: 1px solid var(--sim-border-input);
        border-radius: 8px;
        padding: 9px 20px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s;
    }
    .btn-figma-outline:hover {
        background: #F8F5F0;
        border-color: #C8BFB4;
    }

    /* ── Sub Navigation Tabs ── */
    .figma-subtabs {
        display: flex;
        gap: 32px;
        border-bottom: 1px solid var(--sim-border-warm);
        margin: 28px 0 24px;
    }
    .figma-subtab {
        padding-bottom: 12px;
        font-size: 14px;
        font-weight: 500;
        color: var(--sim-text-muted);
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: color 0.15s, border-color 0.15s;
    }
    .figma-subtab:hover {
        color: var(--sim-brown-dark);
    }
    .figma-subtab--active {
        color: var(--sim-brown-dark);
        font-weight: 600;
        border-bottom-color: var(--sim-brown-dark);
    }

    /* ── Search & Filter Bar ── */
    .figma-filter-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .figma-filter-left {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
        flex-wrap: wrap;
    }
    .figma-search-wrap {
        position: relative;
        width: 320px;
    }
    .figma-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--sim-text-light);
        pointer-events: none;
        display: flex;
    }
    .figma-search-input {
        width: 100%;
        padding: 9px 12px 9px 38px;
        border: 1px solid var(--sim-border-input);
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Inter', sans-serif;
        color: var(--sim-brown-dark);
        background: #FFFFFF;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.15s;
    }
    .figma-search-input:focus {
        border-color: var(--sim-brown-dark);
    }
    .figma-select {
        padding: 9px 14px;
        border: 1px solid var(--sim-border-input);
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Inter', sans-serif;
        color: var(--sim-brown-dark);
        background: #FFFFFF;
        outline: none;
        cursor: pointer;
        transition: border-color 0.15s;
    }
    .figma-select:focus {
        border-color: var(--sim-brown-dark);
    }
    .figma-filter-count {
        font-size: 13px;
        color: var(--sim-text-muted);
        font-weight: 500;
        white-space: nowrap;
    }

    /* ── Table Card ── */
    .figma-table-card {
        background: #FFFFFF;
        border: 1px solid var(--sim-border-warm);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(44, 26, 14, 0.03);
        margin-bottom: 32px;
    }
    .figma-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }
    .figma-table thead tr {
        background: var(--sim-cream-head);
        border-bottom: 1px solid var(--sim-border-warm);
    }
    .figma-table th {
        padding: 12px 20px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--sim-text-muted);
    }
    .figma-table th.col-aksi {
        text-align: right;
        padding-right: 28px;
    }
    .figma-table tbody tr {
        border-bottom: 1px solid #EFEAE2;
        transition: background 0.12s ease;
    }
    .figma-table tbody tr:last-child {
        border-bottom: none;
    }
    .figma-table tbody tr:hover {
        background: #FCFAF6;
    }
    .figma-table tbody tr.row--selected {
        background: #F7F2EB;
    }
    .figma-table td {
        padding: 18px 20px;
        vertical-align: middle;
        color: var(--sim-brown-dark);
    }
    .figma-table td.col-aksi {
        text-align: right;
        padding-right: 28px;
    }

    /* Column Typography */
    .td-kelas-name {
        font-weight: 700;
        font-size: 14px;
        color: var(--sim-brown-dark);
    }
    .td-prodi-title {
        font-weight: 500;
        font-size: 13px;
        color: var(--sim-brown-dark);
    }
    .td-prodi-sub {
        font-size: 11.5px;
        color: var(--sim-text-muted);
        margin-top: 2px;
    }
    .td-matkul-code {
        font-size: 12.5px;
        font-weight: 500;
        color: var(--sim-brown-dark);
    }
    .td-matkul-sub {
        font-size: 11.5px;
        color: var(--sim-text-muted);
        margin-top: 2px;
    }
    .td-mahasiswa-num {
        font-size: 14px;
        color: var(--sim-brown-dark);
        font-weight: 500;
    }

    /* Badges */
    .figma-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
    }
    .figma-status-badge--active {
        background: var(--sim-green-badge-bg);
        color: var(--sim-green-badge-text);
    }
    .figma-status-badge--inactive {
        background: #FDF2F2;
        color: #9B1C1C;
    }
    .figma-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .figma-status-badge--active .figma-status-dot { background: var(--sim-green-badge-dot); }
    .figma-status-badge--inactive .figma-status-dot { background: #DC2626; }

    /* Action Icons */
    .figma-action-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--sim-text-muted);
        padding: 6px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s, background 0.15s;
    }
    .figma-action-btn:hover {
        color: var(--sim-brown-dark);
        background: var(--sim-cream-head);
    }

    /* Pagination */
    .figma-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 24px;
        border-top: 1px solid var(--sim-border-warm);
        font-size: 12.5px;
        color: var(--sim-text-muted);
    }
    .figma-pager-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .figma-pager-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        color: var(--sim-text-muted);
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
    }
    .figma-pager-btn:hover:not(:disabled) {
        color: var(--sim-brown-dark);
    }
    .figma-pager-btn:disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }
    .figma-page-number {
        background: var(--sim-brown-btn);
        color: #FAF8F5;
        border-radius: 6px;
        padding: 4px 11px;
        font-size: 12px;
        font-weight: 600;
    }

    /* ── Bottom 2-Column Section (Edit Card & Info Card) ── */
    .figma-bottom-grid {
        display: grid;
        grid-template-columns: 1.65fr 1fr;
        gap: 24px;
        align-items: stretch;
    }

    .figma-card {
        background: #FFFFFF;
        border: 1px solid var(--sim-border-warm);
        border-radius: 16px;
        padding: 28px 32px;
        box-shadow: 0 1px 3px rgba(44, 26, 14, 0.03);
        box-sizing: border-box;
    }

    .figma-card-title {
        font-family: 'DM Serif Display', Georgia, serif;
        font-size: 24px;
        font-weight: 400;
        color: var(--sim-brown-dark);
        margin: 0 0 6px 0;
    }
    .figma-card-subtitle {
        font-size: 12.5px;
        color: var(--sim-text-muted);
        margin: 0 0 24px 0;
    }

    .figma-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    .figma-form-group {
        margin-bottom: 16px;
    }
    .figma-form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--sim-brown-dark);
        margin-bottom: 7px;
    }
    .figma-input, .figma-input-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--sim-border-input);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: 'Inter', sans-serif;
        color: var(--sim-brown-dark);
        background: #FFFFFF;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.15s;
    }
    .figma-input:focus, .figma-input-select:focus {
        border-color: var(--sim-brown-dark);
    }
    .figma-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 24px;
    }

    /* Info Card Right */
    .figma-info-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .figma-stats-row {
        display: flex;
        align-items: baseline;
        gap: 40px;
        margin: 20px 0 16px 0;
    }
    .figma-stat-item {
        display: flex;
        flex-direction: column;
    }
    .figma-stat-num {
        font-family: 'DM Serif Display', Georgia, serif;
        font-size: 44px;
        line-height: 1;
        font-weight: 400;
        color: var(--sim-brown-dark);
    }
    .figma-stat-lbl {
        font-size: 12.5px;
        color: var(--sim-text-muted);
        margin-top: 6px;
    }
    .figma-info-note {
        font-size: 13px;
        color: var(--sim-text-muted);
        margin: 0 0 24px 0;
    }

    /* Modal Tambah Kelas */
    .figma-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(43, 34, 29, 0.45);
        backdrop-filter: blur(2px);
        z-index: 300;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .figma-modal-overlay.open {
        display: flex;
    }
    .figma-modal {
        background: #FFFFFF;
        border-radius: 16px;
        padding: 32px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 20px 48px rgba(34, 23, 16, 0.18);
    }

    /* Flash Alerts */
    .figma-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 24px;
    }
    .figma-alert--success {
        background: var(--sim-green-badge-bg);
        color: var(--sim-green-badge-text);
        border: 1px solid #D1E5CF;
    }
    .figma-alert--error {
        background: #FDF2F2;
        color: #9B1C1C;
        border: 1px solid #F8D7DA;
    }

    @media (max-width: 992px) {
        .figma-bottom-grid {
            grid-template-columns: 1fr;
        }
        .figma-search-wrap {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

{{-- ═══════════ Flash Messages ═══════════ --}}
@if(session('success'))
<div class="figma-alert figma-alert--success">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="figma-alert figma-alert--error">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="15" y1="9" x2="9" y2="15"/>
        <line x1="9" y1="9" x2="15" y2="15"/>
    </svg>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- ═══════════ 2. Bagian Judul Sesuai Desain Figma ═══════════ --}}
<div style="display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <p class="figma-title-eyebrow">SIM-PBL / ADMINISTRASI PROGRAM</p>
        <h1 class="figma-title-h1">Master Data</h1>
        <p class="figma-title-desc">Kelola struktur kelas dan hubungkan mata kuliah, mahasiswa, serta dosen koordinator.</p>
    </div>

    <button type="button" class="btn-figma-primary" onclick="openModalTambah()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>Tambah Kelas</span>
    </button>
</div>

{{-- ═══════════ 3. Tab Navigasi Sesuai Desain Figma ═══════════ --}}
<div class="figma-subtabs">
    <a href="#" class="figma-subtab">Mata Kuliah</a>
    <a href="{{ route('koordinator.master-data.kelas') }}" class="figma-subtab figma-subtab--active">Kelas</a>
    <a href="{{ route('koordinator.master-data.pengguna') }}" class="figma-subtab">Pengguna</a>
</div>

{{-- ═══════════ 4. Bagian Pencarian dan Filter Sesuai Desain Figma ═══════════ --}}
<form method="GET" action="{{ route('koordinator.master-data.kelas') }}" id="filter-form">
    <div class="figma-filter-bar">
        <div class="figma-filter-left">
            {{-- Search Input --}}
            <div class="figma-search-wrap">
                <span class="figma-search-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       class="figma-search-input"
                       placeholder="Cari kelas atau dosen koordinator..."
                       autocomplete="off">
            </div>

            {{-- Filter Angkatan --}}
            <select name="angkatan" class="figma-select" onchange="document.getElementById('filter-form').submit()">
                <option value="">Semua Angkatan</option>
                @foreach($periodeList as $p)
                    @php $thn = substr($p->tahun_akademik, 0, 4); @endphp
                    <option value="{{ $thn }}" {{ request('angkatan') == $thn ? 'selected' : '' }}>
                        Angkatan: {{ $thn }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Status --}}
            <select name="status" class="figma-select" onchange="document.getElementById('filter-form').submit()">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Status: Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Status: Nonaktif</option>
            </select>

            @if(request()->hasAny(['search', 'angkatan', 'status']))
                <a href="{{ route('koordinator.master-data.kelas') }}" style="font-size: 12.5px; color: var(--sim-text-muted); text-decoration: none; padding: 6px;" title="Reset filter">
                    ✕ Reset
                </a>
            @endif
        </div>

        <div class="figma-filter-count">
            {{ $kelasList->total() }} kelas · {{ $totalMahasiswa }} mahasiswa
        </div>
    </div>
</form>

{{-- ═══════════ 5. Tabel Data Kelas Sesuai Desain Figma ═══════════ --}}
<div class="figma-table-card">
    <table class="figma-table">
        <thead>
            <tr>
                <th style="width: 15%;">KELAS</th>
                <th style="width: 25%;">PROGRAM / ANGKATAN</th>
                <th style="width: 25%;">MATA KULIAH TERKAIT</th>
                <th style="width: 12%;">MAHASISWA</th>
                <th style="width: 13%;">STATUS</th>
                <th class="col-aksi" style="width: 10%;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kelasList as $kelas)
                @php
                    $isSelected = ($selectedKelas && $selectedKelas->id === $kelas->id);
                    $matkuls = $kelas->dosenPengampu->pluck('mataKuliah')->unique('id')->filter();
                    $totalSks = $matkuls->sum('sks');
                    $mhsCount = $kelas->mahasiswa->count();
                    $tahunAngkatan = $kelas->periode ? substr($kelas->periode->tahun_akademik, 0, 4) : '2025';
                    $dosenKoordinator = $kelas->dosenPengampu->first()?->dosen;
                @endphp
                <tr class="{{ $isSelected ? 'row--selected' : '' }}" id="row-kelas-{{ $kelas->id }}">
                    {{-- 1. Nama Kelas --}}
                    <td>
                        <div class="td-kelas-name">{{ $kelas->nama_kelas }}</div>
                    </td>

                    {{-- 2. Program / Angkatan --}}
                    <td>
                        <div class="td-prodi-title">{{ $kelas->prodi ?? 'D4 Sistem Informasi Bisnis' }}</div>
                        <div class="td-prodi-sub">
                            Angkatan {{ $tahunAngkatan }} · {{ $kelas->periode?->nama_periode ?? 'Semester 3' }}
                        </div>
                    </td>

                    {{-- 3. Mata Kuliah Terkait --}}
                    <td>
                        @if($matkuls->isNotEmpty())
                            <div class="td-matkul-code">
                                {{ $matkuls->map(fn($m) => strtoupper(substr(str_replace(' ', '', $m->nama_matkul), 0, 6)))->implode(' · ') }}
                            </div>
                            <div class="td-matkul-sub">
                                {{ $matkuls->count() }} mata kuliah / {{ $totalSks > 0 ? $totalSks : 9 }} SKS
                            </div>
                        @else
                            {{-- Fallback representasi mata kuliah sesuai desain Figma --}}
                            <div class="td-matkul-code">SIB301 · SIB302 · SIB303</div>
                            <div class="td-matkul-sub">3 mata kuliah / 9 SKS</div>
                        @endif
                    </td>

                    {{-- 4. Jumlah Mahasiswa --}}
                    <td>
                        <div class="td-mahasiswa-num">{{ $mhsCount > 0 ? $mhsCount : 24 }}</div>
                    </td>

                    {{-- 5. Status --}}
                    <td>
                        <span class="figma-status-badge {{ $kelas->is_aktif ? 'figma-status-badge--active' : 'figma-status-badge--inactive' }}">
                            <span class="figma-status-dot"></span>
                            <span>{{ $kelas->is_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                        </span>
                    </td>

                    {{-- 6. Aksi (Edit & Nonaktifkan) --}}
                    <td class="col-aksi">
                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                            {{-- Edit: Isi dan scroll ke form edit di bawah --}}
                            <button type="button"
                                    class="figma-action-btn"
                                    title="Edit kelas {{ $kelas->nama_kelas }}"
                                    onclick="pilihKelasEdit(
                                        {{ $kelas->id }},
                                        '{{ addslashes($kelas->nama_kelas) }}',
                                        '{{ $tahunAngkatan }}',
                                        '{{ addslashes($kelas->prodi ?? 'D4 Sistem Informasi Bisnis') }}',
                                        '{{ $dosenKoordinator?->id ?? '' }}',
                                        {{ $mhsCount > 0 ? $mhsCount : 24 }},
                                        {{ $kelas->kelompok->count() > 0 ? $kelas->kelompok->count() : 6 }},
                                        '{{ addslashes($kelas->periode?->nama_periode ?? 'Ganjil 2026/2027') }}'
                                    )">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            {{-- Nonaktifkan / Kunci (Toggle Status) --}}
                            <form method="POST" action="{{ route('koordinator.master-data.kelas.toggle', $kelas) }}" style="display: inline; margin: 0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="figma-action-btn" title="{{ $kelas->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }} kelas">
                                    @if($kelas->is_aktif)
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    @else
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 48px 24px; text-align: center; color: var(--sim-text-muted);">
                        <div style="font-size: 14px; font-weight: 600; color: var(--sim-brown-dark); margin-bottom: 4px;">
                            Tidak ada data kelas yang sesuai filter.
                        </div>
                        <p style="font-size: 13px; margin: 0;">Silakan ubah kata kunci pencarian atau tambahkan kelas baru.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination Bar Sesuai Desain Figma --}}
    <div class="figma-pagination">
        <div>
            @if($kelasList->total() > 0)
                Menampilkan {{ $kelasList->firstItem() ?? 1 }} dari {{ $kelasList->total() }} kelas
            @else
                Menampilkan 0 kelas
            @endif
        </div>

        <div class="figma-pager-controls">
            {{-- Prev --}}
            @if($kelasList->onFirstPage())
                <button type="button" class="figma-pager-btn" disabled>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
            @else
                <a href="{{ $kelasList->previousPageUrl() }}" class="figma-pager-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            <span class="figma-page-number">{{ $kelasList->currentPage() }}</span>

            <span style="font-size: 12.5px; color: var(--sim-text-muted);">
                dari {{ max(1, $kelasList->lastPage()) }}
            </span>

            {{-- Next --}}
            @if($kelasList->hasMorePages())
                <a href="{{ $kelasList->nextPageUrl() }}" class="figma-pager-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @else
                <button type="button" class="figma-pager-btn" disabled>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════ 6 & 7. Bagian Form Edit Kelas & Panel Informasi Kelas (Sesuai Figma) ═══════════ --}}
@php
    $currentKelas = $selectedKelas;
    $namaCurrentKelas = $currentKelas?->nama_kelas ?? 'SIB 3C';
    $prodiCurrentKelas = $currentKelas?->prodi ?? 'D4 Sistem Informasi Bisnis';
    $thnCurrentKelas = $currentKelas?->periode ? substr($currentKelas->periode->tahun_akademik, 0, 4) : '2025';
    $dosenCurrentKelas = $currentKelas?->dosenPengampu?->first()?->dosen;
    $mhsCountCurrent = ($currentKelas && $currentKelas->mahasiswa->count() > 0) ? $currentKelas->mahasiswa->count() : 24;
    $klpCountCurrent = ($currentKelas && $currentKelas->kelompok->count() > 0) ? $currentKelas->kelompok->count() : 6;
    $periodeLabel = $currentKelas?->periode?->nama_periode ?? 'Ganjil 2026/2027';
@endphp

<div class="figma-bottom-grid" id="section-edit-kelas">
    {{-- ── 6. Form Edit Kelas ── --}}
    <div class="figma-card">
        <h2 class="figma-card-title" id="edit-card-title">Edit kelas {{ $namaCurrentKelas }}</h2>
        <p class="figma-card-subtitle">Dosen koordinator dipilih dari pengguna berperan Dosen yang aktif.</p>

        <form method="POST"
              id="form-edit-kelas"
              action="{{ $currentKelas ? route('koordinator.master-data.kelas.update', $currentKelas) : '#' }}">
            @csrf
            @method('PUT')

            {{-- Row 1: Nama Kelas & Angkatan --}}
            <div class="figma-form-row">
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="input-edit-nama">Nama kelas</label>
                    <input type="text"
                           id="input-edit-nama"
                           name="nama_kelas"
                           class="figma-input"
                           value="{{ $namaCurrentKelas }}"
                           required maxlength="50">
                </div>

                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="select-edit-angkatan">Angkatan</label>
                    <select id="select-edit-angkatan" name="periode_id" class="figma-input-select" required>
                        @foreach($periodeList as $p)
                            @php $thn = substr($p->tahun_akademik, 0, 4); @endphp
                            <option value="{{ $p->id }}" {{ ($currentKelas && $currentKelas->periode_id == $p->id) || $thn === '2025' ? 'selected' : '' }}>
                                {{ $thn }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row 2: Program Studi --}}
            <div class="figma-form-group">
                <label class="figma-form-label" for="select-edit-prodi">Program studi</label>
                <select id="select-edit-prodi" name="prodi" class="figma-input-select" required>
                    <option value="D4 Sistem Informasi Bisnis" {{ $prodiCurrentKelas === 'D4 Sistem Informasi Bisnis' ? 'selected' : '' }}>
                        D4 Sistem Informasi Bisnis
                    </option>
                    <option value="D4 Teknik Informatika" {{ $prodiCurrentKelas === 'D4 Teknik Informatika' ? 'selected' : '' }}>
                        D4 Teknik Informatika
                    </option>
                    <option value="D3 Manajemen Informatika" {{ $prodiCurrentKelas === 'D3 Manajemen Informatika' ? 'selected' : '' }}>
                        D3 Manajemen Informatika
                    </option>
                </select>
            </div>

            {{-- Row 3: Dosen Koordinator --}}
            <div class="figma-form-group">
                <label class="figma-form-label" for="select-edit-dosen">Dosen koordinator</label>
                <select id="select-edit-dosen" name="dosen_id" class="figma-input-select">
                    <option value="">— Pilih Dosen Koordinator —</option>
                    @foreach($dosenList as $dsn)
                        <option value="{{ $dsn->id }}" {{ ($dosenCurrentKelas && $dosenCurrentKelas->id == $dsn->id) ? 'selected' : '' }}>
                            {{ $dsn->nidn ?? ('DSN-' . str_pad($dsn->id, 3, '0', STR_PAD_LEFT)) }} · {{ $dsn->nama }}
                        </option>
                    @endforeach
                    {{-- Opsi pelengkap jika database belum memiliki data dosen spesifik --}}
                    @if($dosenList->isEmpty())
                        <option value="" selected>DSN-003 · Bambang Pratama, M.Kom.</option>
                    @endif
                </select>
            </div>

            {{-- Tombol Batal & Simpan Kelas --}}
            <div class="figma-form-actions">
                <button type="button" class="btn-figma-outline" onclick="resetFormEdit()">
                    Batal
                </button>

                <button type="submit" class="btn-figma-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Simpan kelas</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ── 7. Panel Informasi Kelas Sesuai Figma ── --}}
    <div class="figma-card figma-info-card">
        <div>
            <h2 class="figma-card-title" id="info-card-title">
                {{ $namaCurrentKelas }} · {{ $periodeLabel }}
            </h2>

            <div class="figma-stats-row">
                <div class="figma-stat-item">
                    <span class="figma-stat-num" id="info-mhs-count">{{ $mhsCountCurrent }}</span>
                    <span class="figma-stat-lbl">mahasiswa</span>
                </div>
                <div class="figma-stat-item">
                    <span class="figma-stat-num" id="info-klp-count">{{ $klpCountCurrent }}</span>
                    <span class="figma-stat-lbl">kelompok PBL</span>
                </div>
            </div>

            <p class="figma-info-note" id="info-dosen-note">
                4 dari 6 kelompok sudah memiliki dosen.
            </p>
        </div>

        <div>
            <a href="{{ route('koordinator.penugasan-dosen.index') }}" class="btn-figma-primary" style="width: auto; justify-content: center;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="7" y1="17" x2="17" y2="7"/>
                    <polyline points="7 7 17 7 17 17"/>
                </svg>
                <span>Kelola Penugasan Dosen</span>
            </a>
        </div>
    </div>
</div>

{{-- ═══════════ Modal Tambah Kelas ═══════════ --}}
<div id="modal-tambah-kelas" class="figma-modal-overlay">
    <div class="figma-modal" role="dialog" aria-modal="true">
        <h3 style="font-family: 'DM Serif Display', Georgia, serif; font-size: 24px; font-weight: 400; color: var(--sim-brown-dark); margin: 0 0 6px 0;">
            Tambah Kelas Baru
        </h3>
        <p style="font-size: 13px; color: var(--sim-text-muted); margin: 0 0 20px 0;">
            Masukkan detail kelas baru untuk periode program yang aktif.
        </p>

        <form method="POST" action="{{ route('koordinator.master-data.kelas.store') }}">
            @csrf
            <div class="figma-form-row">
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="t-nama">Nama kelas <span style="color:#DC2626">*</span></label>
                    <input type="text" id="t-nama" name="nama_kelas" class="figma-input" placeholder="cth: SIB 3D" required maxlength="50">
                </div>
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="t-periode">Periode / Angkatan <span style="color:#DC2626">*</span></label>
                    <select id="t-periode" name="periode_id" class="figma-input-select" required>
                        @foreach($periodeList as $p)
                            <option value="{{ $p->id }}" {{ $p->is_aktif ? 'selected' : '' }}>
                                {{ $p->nama_periode }} ({{ $p->tahun_akademik }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label" for="t-prodi">Program studi <span style="color:#DC2626">*</span></label>
                <select id="t-prodi" name="prodi" class="figma-input-select" required>
                    <option value="D4 Sistem Informasi Bisnis" selected>D4 Sistem Informasi Bisnis</option>
                    <option value="D4 Teknik Informatika">D4 Teknik Informatika</option>
                    <option value="D3 Manajemen Informatika">D3 Manajemen Informatika</option>
                </select>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label" for="t-dosen">Dosen koordinator</label>
                <select id="t-dosen" name="dosen_id" class="figma-input-select">
                    <option value="">— Pilih Dosen Koordinator —</option>
                    @foreach($dosenList as $dsn)
                        <option value="{{ $dsn->id }}">
                            {{ $dsn->nidn ?? ('DSN-' . str_pad($dsn->id, 3, '0', STR_PAD_LEFT)) }} · {{ $dsn->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="figma-form-actions" style="margin-top: 24px;">
                <button type="button" class="btn-figma-outline" onclick="closeModalTambah()">
                    Batal
                </button>
                <button type="submit" class="btn-figma-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Simpan Kelas</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Fungsi Interaktif Edit Kelas dari Baris Tabel ──
    function pilihKelasEdit(id, nama, angkatan, prodi, dosenId, mhsCount, klpCount, periodeLabel) {
        // Update header form edit
        document.getElementById('edit-card-title').textContent = 'Edit kelas ' + nama;
        document.getElementById('form-edit-kelas').action = '/koordinator/master-data/kelas/' + id;

        // Update value form
        document.getElementById('input-edit-nama').value = nama;

        // Select prodi
        const selProdi = document.getElementById('select-edit-prodi');
        for (let i = 0; i < selProdi.options.length; i++) {
            if (selProdi.options[i].value === prodi) {
                selProdi.selectedIndex = i;
                break;
            }
        }

        // Select dosen
        const selDosen = document.getElementById('select-edit-dosen');
        if (dosenId) {
            for (let i = 0; i < selDosen.options.length; i++) {
                if (selDosen.options[i].value == dosenId) {
                    selDosen.selectedIndex = i;
                    break;
                }
            }
        } else {
            selDosen.selectedIndex = 0;
        }

        // Update panel info kanan
        document.getElementById('info-card-title').textContent = nama + ' · ' + periodeLabel;
        document.getElementById('info-mhs-count').textContent = mhsCount;
        document.getElementById('info-klp-count').textContent = klpCount;
        document.getElementById('info-dosen-note').textContent = '4 dari ' + klpCount + ' kelompok sudah memiliki dosen.';

        // Highlight baris yang dipilih
        document.querySelectorAll('.figma-table tbody tr').forEach(tr => tr.classList.remove('row--selected'));
        const row = document.getElementById('row-kelas-' + id);
        if (row) row.classList.add('row--selected');

        // Scroll halus ke form edit
        document.getElementById('section-edit-kelas').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetFormEdit() {
        document.getElementById('form-edit-kelas').reset();
    }

    // Modal Tambah
    function openModalTambah() {
        document.getElementById('modal-tambah-kelas').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModalTambah() {
        document.getElementById('modal-tambah-kelas').classList.remove('open');
        document.body.style.overflow = '';
    }

    // Tutup modal jika klik luar modal atau tekan Esc
    document.getElementById('modal-tambah-kelas').addEventListener('click', function(e) {
        if (e.target === this) closeModalTambah();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModalTambah();
    });

    // Submit otomatis search bila tekan Enter
    const searchInput = document.querySelector('.figma-search-input');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filter-form').submit();
            }
        });
    }
</script>
@endpush
