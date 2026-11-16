@extends('layouts.koordinator')

@section('title', 'Master Data — Mata Kuliah')
@section('breadcrumb_title', 'Mata Kuliah')

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
    .figma-table-wrap {
        overflow-x: auto;
        width: 100%;
    }
    .figma-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
        min-width: 900px;
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
    .td-mk-code {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--sim-brown-dark);
    }
    .td-mk-name {
        font-weight: 700;
        font-size: 14px;
        color: var(--sim-brown-dark);
    }
    .td-mk-desc {
        font-size: 11.5px;
        color: var(--sim-text-muted);
        margin-top: 2px;
    }
    .td-mk-sks {
        font-size: 14px;
        font-weight: 600;
        color: var(--sim-brown-dark);
    }
    .td-mk-semester {
        font-size: 13px;
        color: var(--sim-brown-dark);
    }
    .td-mk-kelas {
        font-size: 12.5px;
        color: var(--sim-brown-dark);
        font-weight: 500;
    }
    .td-mk-kelas-sub {
        font-size: 11px;
        color: var(--sim-text-muted);
        margin-top: 2px;
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

    /* ── Bottom Section (Form Edit Only) ── */
    .figma-bottom-grid {
        display: flex;
        flex-direction: column;
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
        width: 100%;
        max-width: none;
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

    /* Modal Tambah Mata Kuliah */
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

{{-- ═══════════ Header Sesuai Desain ═══════════ --}}
<div style="display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <p class="figma-title-eyebrow">SIM-PBL / ADMINISTRASI PROGRAM</p>
        <h1 class="figma-title-h1">Master Data</h1>
        <p class="figma-title-desc">Sumber data akademik yang terhubung ke kelas, pengguna, dan pelaksanaan PBL.</p>
    </div>

    <button type="button" class="btn-figma-primary" onclick="openModalTambah()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>Tambah Mata Kuliah</span>
    </button>
</div>

{{-- ═══════════ Tab Navigasi Sesuai Desain ═══════════ --}}
<div class="figma-subtabs">
    <a href="{{ route('koordinator.master-data.mata-kuliah') }}" class="figma-subtab figma-subtab--active">Mata Kuliah</a>
    <a href="{{ route('koordinator.master-data.kelas') }}" class="figma-subtab">Kelas</a>
    <a href="{{ route('koordinator.master-data.pengguna') }}" class="figma-subtab">Pengguna</a>
</div>

{{-- ═══════════ Pencarian dan Filter Sesuai Desain ═══════════ --}}
<form method="GET" action="{{ route('koordinator.master-data.mata-kuliah') }}" id="filter-form">
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
                       placeholder="Cari kode atau nama mata kuliah..."
                       autocomplete="off">
            </div>

            {{-- Filter Status --}}
            <select name="status" class="figma-select" onchange="document.getElementById('filter-form').submit()">
                <option value="">Status: Semua</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Status: Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Status: Nonaktif</option>
            </select>

            {{-- Filter Semester --}}
            <select name="semester" class="figma-select" onchange="document.getElementById('filter-form').submit()">
                <option value="">Semester: Semua</option>
                @foreach($semesterList as $smt)
                    <option value="{{ $smt }}" {{ request('semester') == $smt ? 'selected' : '' }}>Semester: {{ $smt }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'status', 'semester']))
                <a href="{{ route('koordinator.master-data.mata-kuliah') }}" style="font-size: 12.5px; color: var(--sim-text-muted); text-decoration: none; padding: 6px;" title="Reset filter">
                    ✕ Reset
                </a>
            @endif
        </div>

        <div class="figma-filter-count">
            {{ $mataKuliahList->total() }} mata kuliah · {{ $totalSks }} SKS
        </div>
    </div>
</form>

{{-- ═══════════ Tabel Mata Kuliah Sesuai Desain ═══════════ --}}
<div class="figma-table-card">
    <div class="figma-table-wrap">
        <table class="figma-table">
        <thead>
            <tr>
                <th style="width: 10%; white-space: nowrap;">KODE</th>
                <th style="width: 30%;">MATA KULIAH</th>
                <th style="width: 6%;">SKS</th>
                <th style="width: 8%;">SEMESTER</th>
                <th style="width: 10%;">STATUS</th>
                <th style="width: 24%;">KELAS TERHUBUNG</th>
                <th class="col-aksi" style="width: 12%;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mataKuliahList as $mk)
                @php
                    $kelasTerhubung = $mk->dosenPengampu->pluck('kelas.nama_kelas')->unique()->filter()->values();
                    $kelasCount = $kelasTerhubung->count();
                @endphp
                <tr id="row-mk-{{ $mk->id }}">
                    {{-- 1. Kode --}}
                    <td>
                        <div class="td-mk-code">{{ strtoupper($mk->kode) }}</div>
                    </td>

                    {{-- 2. Mata Kuliah --}}
                    <td>
                        <div class="td-mk-name">{{ $mk->nama_matkul }}</div>
                        <div class="td-mk-desc">{{ $mk->prodi ?? 'D4 Sistem Informasi Bisnis' }}</div>
                    </td>

                    {{-- 3. SKS --}}
                    <td>
                        <div class="td-mk-sks">{{ $mk->sks }}</div>
                    </td>

                    {{-- 4. Semester --}}
                    <td>
                        <div class="td-mk-semester">{{ $mk->semester }}</div>
                    </td>

                    {{-- 5. Status --}}
                    <td>
                        <span class="figma-status-badge {{ $mk->is_aktif ? 'figma-status-badge--active' : 'figma-status-badge--inactive' }}">
                            <span class="figma-status-dot"></span>
                            <span>{{ $mk->is_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                        </span>
                    </td>

                    {{-- 6. Kelas Terhubung --}}
                    <td>
                        @if($kelasCount > 0)
                            <div class="td-mk-kelas">{{ $kelasTerhubung->implode(', ') }}</div>
                            <div class="td-mk-kelas-sub">{{ $kelasCount }} kelas terhubung</div>
                        @else
                            <div class="td-mk-kelas" style="color: var(--sim-text-muted);">Belum ada kelas</div>
                        @endif
                    </td>

                    {{-- 7. Aksi --}}
                    <td class="col-aksi">
                        <div style="display: inline-flex; align-items: center; gap: 6px;">
                            {{-- Edit --}}
                            <button type="button"
                                    class="figma-action-btn"
                                    title="Edit mata kuliah {{ strtoupper($mk->kode) }}"
                                    onclick="pilihMkEdit(
                                        {{ $mk->id }},
                                        '{{ addslashes(strtoupper($mk->kode)) }}',
                                        '{{ addslashes($mk->nama_matkul) }}',
                                        {{ $mk->sks }},
                                        {{ $mk->semester }},
                                        {{ $mk->is_aktif ? 'true' : 'false' }},
                                        '{{ $kelasTerhubung->implode(',') }}'
                                    )">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            {{-- Nonaktifkan / Aktifkan (Toggle Status) --}}
                            <form method="POST" action="{{ route('koordinator.master-data.mata-kuliah.toggle', $mk) }}" style="display: inline; margin: 0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="figma-action-btn" title="{{ $mk->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }} mata kuliah">
                                    @if($mk->is_aktif)
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

                            {{-- Hapus --}}
                            <form method="POST" action="{{ route('koordinator.master-data.mata-kuliah.destroy', $mk) }}" style="display: inline; margin: 0;" onsubmit="return confirm('Yakin ingin menghapus mata kuliah {{ addslashes(strtoupper($mk->kode)) }}? Data penugasan dosen terkait juga akan ikut terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="figma-action-btn" title="Hapus mata kuliah">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 48px 24px; text-align: center; color: var(--sim-text-muted);">
                        <div style="font-size: 14px; font-weight: 600; color: var(--sim-brown-dark); margin-bottom: 4px;">
                            Tidak ada data mata kuliah yang sesuai filter.
                        </div>
                        <p style="font-size: 13px; margin: 0;">Silakan ubah kata kunci pencarian atau tambahkan mata kuliah baru.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Pagination Bar Sesuai Desain --}}
    <div class="figma-pagination">
        <div>
            @if($mataKuliahList->total() > 0)
                Menampilkan {{ $mataKuliahList->firstItem() ?? 1 }} dari {{ $mataKuliahList->total() }} mata kuliah
            @else
                Menampilkan 0 mata kuliah
            @endif
        </div>

        <div class="figma-pager-controls">
            {{-- Prev --}}
            @if($mataKuliahList->onFirstPage())
                <button type="button" class="figma-pager-btn" disabled>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
            @else
                <a href="{{ $mataKuliahList->previousPageUrl() }}" class="figma-pager-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            <span class="figma-page-number">{{ $mataKuliahList->currentPage() }}</span>

            <span style="font-size: 12.5px; color: var(--sim-text-muted);">
                dari {{ max(1, $mataKuliahList->lastPage()) }}
            </span>

            {{-- Next --}}
            @if($mataKuliahList->hasMorePages())
                <a href="{{ $mataKuliahList->nextPageUrl() }}" class="figma-pager-btn">
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

{{-- ═══════════ Form Edit Mata Kuliah & Panel Informasi ═══════════ --}}
<div class="figma-bottom-grid" id="section-edit-mk">
    {{-- ── Form Edit Mata Kuliah ── --}}
    <div class="figma-card">
        <h2 class="figma-card-title" id="edit-mk-title">Edit mata kuliah</h2>
        <p class="figma-card-subtitle" id="edit-mk-subtitle">Pilih mata kuliah dari tabel untuk mengedit.</p>

        <form method="POST"
              id="form-edit-mk"
              action="{{ '#' }}">
            @csrf
            @method('PUT')

            {{-- Row 1: Kode & Nama Mata Kuliah --}}
            <div class="figma-form-row">
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="input-edit-kode">Kode mata kuliah <span style="color:#DC2626">*</span></label>
                    <input type="text"
                           id="input-edit-kode"
                           name="kode"
                           class="figma-input"
                           value=""
                           required maxlength="20">
                </div>
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="input-edit-nama">Nama mata kuliah <span style="color:#DC2626">*</span></label>
                    <input type="text"
                           id="input-edit-nama"
                           name="nama_matkul"
                           class="figma-input"
                           value=""
                           required maxlength="100">
                </div>
            </div>

            {{-- Row 2: SKS & Semester --}}
            <div class="figma-form-row">
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="input-edit-sks">Jumlah SKS <span style="color:#DC2626">*</span></label>
                    <input type="number"
                           id="input-edit-sks"
                           name="sks"
                           class="figma-input"
                           value=""
                           required min="1" max="6">
                </div>
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="select-edit-semester">Semester <span style="color:#DC2626">*</span></label>
                    <select id="select-edit-semester" name="semester" class="figma-input-select" required>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Row 3: Status --}}
            <div class="figma-form-group">
                <label class="figma-form-label" for="select-edit-status">Status <span style="color:#DC2626">*</span></label>
                <select id="select-edit-status" name="is_aktif" class="figma-input-select">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            {{-- Tombol Batal & Simpan --}}
            <div class="figma-form-actions">
                <button type="button" class="btn-figma-outline" onclick="resetFormMk()">
                    Batal
                </button>

                <button type="submit" class="btn-figma-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Simpan mata kuliah</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════ Modal Tambah Mata Kuliah ═══════════ --}}
<div id="modal-tambah-mk" class="figma-modal-overlay">
    <div class="figma-modal" role="dialog" aria-modal="true">
        <h3 style="font-family: 'DM Serif Display', Georgia, serif; font-size: 24px; font-weight: 400; color: var(--sim-brown-dark); margin: 0 0 6px 0;">
            Tambah Mata Kuliah Baru
        </h3>
        <p style="font-size: 13px; color: var(--sim-text-muted); margin: 0 0 20px 0;">
            Masukkan detail mata kuliah baru untuk program studi yang terkait.
        </p>

        <form method="POST" action="{{ route('koordinator.master-data.mata-kuliah.store') }}">
            @csrf

            <div class="figma-form-row">
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="t-kode">Kode mata kuliah <span style="color:#DC2626">*</span></label>
                    <input type="text" id="t-kode" name="kode" class="figma-input" placeholder="cth: SIB301" required maxlength="20">
                </div>
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="t-nama">Nama mata kuliah <span style="color:#DC2626">*</span></label>
                    <input type="text" id="t-nama" name="nama_matkul" class="figma-input" placeholder="cth: Pemrograman Web Lanjut" required maxlength="100">
                </div>
            </div>

            <div class="figma-form-row">
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="t-sks">Jumlah SKS <span style="color:#DC2626">*</span></label>
                    <input type="number" id="t-sks" name="sks" class="figma-input" placeholder="3" required min="1" max="6">
                </div>
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label" for="t-semester">Semester <span style="color:#DC2626">*</span></label>
                    <select id="t-semester" name="semester" class="figma-input-select" required>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label" for="t-status">Status <span style="color:#DC2626">*</span></label>
                <select id="t-status" name="is_aktif" class="figma-input-select">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label" for="t-kelas">Kelas yang terhubung</label>
                <select id="t-kelas" name="kelas_id[]" class="figma-input-select" multiple size="4">
                    @foreach($kelasList as $kls)
                        <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                    @endforeach
                </select>
                <p style="font-size: 11px; color: var(--sim-text-muted); margin-top: 6px;">Tekan Ctrl (Windows) / Cmd (Mac) untuk memilih lebih dari satu kelas.</p>
            </div>

            <div class="figma-form-actions" style="margin-top: 24px;">
                <button type="button" class="btn-figma-outline" onclick="closeModalTambah()">
                    Batal
                </button>
                <button type="submit" class="btn-figma-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Simpan Mata Kuliah</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    {{-- Fungsi Interaktif Edit Mata Kuliah dari Baris Tabel --}}
    function pilihMkEdit(id, kode, nama, sks, semester, isAktif, kelasList) {
        // Update header form edit
        document.getElementById('edit-mk-title').textContent = 'Edit mata kuliah ' + kode;
        document.getElementById('form-edit-mk').action = '/koordinator/master-data/mata-kuliah/' + id;
        document.getElementById('edit-mk-subtitle').textContent = 'Dipilih: ' + kode + ' · Perubahan tidak menghapus riwayat proyek.';

        // Update value form
        document.getElementById('input-edit-kode').value = kode;
        document.getElementById('input-edit-nama').value = nama;
        document.getElementById('input-edit-sks').value = sks;
        document.getElementById('select-edit-semester').value = semester;
        document.getElementById('select-edit-status').value = isAktif ? '1' : '0';

        // Highlight baris yang dipilih
        document.querySelectorAll('.figma-table tbody tr').forEach(tr => tr.classList.remove('row--selected'));
        const row = document.getElementById('row-mk-' + id);
        if (row) row.classList.add('row--selected');

        // Scroll halus ke form edit
        document.getElementById('section-edit-mk').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetFormMk() {
        document.getElementById('form-edit-mk').reset();
        document.getElementById('form-edit-mk').action = '#';
        document.getElementById('edit-mk-title').textContent = 'Edit mata kuliah';
        document.getElementById('edit-mk-subtitle').textContent = 'Pilih mata kuliah dari tabel untuk mengedit.';
        document.querySelectorAll('.figma-table tbody tr').forEach(tr => tr.classList.remove('row--selected'));
    }

    {{-- Modal Tambah --}}
    function openModalTambah() {
        document.getElementById('modal-tambah-mk').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModalTambah() {
        document.getElementById('modal-tambah-mk').classList.remove('open');
        document.body.style.overflow = '';
    }

    {{-- Tutup modal jika klik luar modal atau tekan Esc --}}
    document.getElementById('modal-tambah-mk').addEventListener('click', function(e) {
        if (e.target === this) closeModalTambah();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModalTambah();
    });

    {{-- Submit otomatis search bila tekan Enter --}}
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
