@extends('layouts.koordinator')

@section('title', 'Master Data — Pengguna')
@section('breadcrumb_title', 'Pengguna')

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
        padding: 9px 18px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s;
        white-space: nowrap;
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
        margin: 28px 0 20px;
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

    /* ── Role Filter Pills ── */
    .figma-role-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .figma-role-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 500;
        color: var(--sim-text-muted);
        background: #FFFFFF;
        border: 1px solid var(--sim-border-input);
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .figma-role-pill:hover {
        background: #F5EFE6;
        color: var(--sim-brown-dark);
    }
    .figma-role-pill--active {
        background: var(--sim-brown-dark) !important;
        color: #FAF8F5 !important;
        border-color: var(--sim-brown-dark) !important;
        font-weight: 600;
    }
    .figma-role-count {
        font-size: 11px;
        padding: 1px 7px;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.08);
    }
    .figma-role-pill--active .figma-role-count {
        background: rgba(255, 255, 255, 0.2);
        color: #FFFFFF;
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
        width: 340px;
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
        margin-bottom: 24px;
        width: 100%;
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
    .figma-table td {
        padding: 16px 20px;
        vertical-align: middle;
        color: var(--sim-brown-dark);
    }
    .figma-table td.col-aksi {
        text-align: right;
        padding-right: 28px;
    }

    /* Role Badges */
    .badge-role {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
    }
    .badge-role-mahasiswa {
        background: #E8F4FA;
        color: #0A6082;
    }
    .badge-role-dosen {
        background: var(--sim-green-badge-bg);
        color: var(--sim-green-badge-text);
    }
    .badge-role-admin {
        background: #F5EEE6;
        color: #6C431B;
    }
    .badge-role-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .badge-role-mahasiswa .badge-role-dot { background: #0D8ABC; }
    .badge-role-dosen .badge-role-dot { background: var(--sim-green-badge-dot); }
    .badge-role-admin .badge-role-dot { background: #8E531D; }

    /* Status Badges */
    .figma-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 11px;
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

    /* Action Buttons */
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
    .figma-action-btn.btn-delete:hover {
        color: #DC2626;
        background: #FDF2F2;
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

    /* ── Bottom Reset Password Button (Sesuai Desain Figma) ── */
    .btn-figma-reset-bottom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #FFFFFF;
        border: 1px solid var(--sim-border-input);
        border-radius: 8px;
        padding: 8px 16px;
        color: var(--sim-brown-dark);
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: all 0.15s ease;
    }
    .btn-figma-reset-bottom:hover {
        background: #FAF8F5;
        border-color: var(--sim-border-strong);
        color: var(--sim-brown-deep);
    }
    .btn-figma-reset-bottom svg {
        color: var(--sim-brown-dark);
        flex-shrink: 0;
    }

    /* Modals */
    .figma-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(33, 21, 14, 0.48);
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
        box-sizing: border-box;
    }
    .figma-modal-title {
        font-family: 'DM Serif Display', Georgia, serif;
        font-size: 24px;
        font-weight: 400;
        color: var(--sim-brown-dark);
        margin: 0 0 6px 0;
    }
    .figma-modal-desc {
        font-size: 13px;
        color: var(--sim-text-muted);
        margin: 0 0 22px 0;
    }
    .figma-form-group {
        margin-bottom: 16px;
    }
    .figma-form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--sim-brown-dark);
        margin-bottom: 6px;
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

    @media (max-width: 900px) {
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

{{-- ═══════════ 1. Bagian Judul Sesuai Desain Figma ═══════════ --}}
<div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
    <div>
        <p class="figma-title-eyebrow">SIM-PBL / ADMINISTRASI PROGRAM</p>
        <h1 class="figma-title-h1">Master Data</h1>
        <p class="figma-title-desc">Kelola akun pengguna, hak akses peran, serta relasi akademik di sistem SIM-PBL.</p>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
        <button type="button" class="btn-figma-outline" onclick="openModalUndang()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
            <span>Undang Pengguna</span>
        </button>

        <button type="button" class="btn-figma-primary" onclick="openModalTambah()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Tambah Pengguna</span>
        </button>
    </div>
</div>

{{-- ═══════════ 2. Tab Navigasi Sesuai Desain Figma ═══════════ --}}
<div class="figma-subtabs">
    <a href="#" class="figma-subtab">Mata Kuliah</a>
    <a href="{{ route('koordinator.master-data.kelas') }}" class="figma-subtab">Kelas</a>
    <a href="{{ route('koordinator.master-data.pengguna') }}" class="figma-subtab figma-subtab--active">Pengguna</a>
</div>

{{-- ═══════════ 3. Filter Peran Pengguna (Semua, Mahasiswa, Dosen, Admin) ═══════════ --}}
<div class="figma-role-pills">
    <a href="{{ route('koordinator.master-data.pengguna', array_merge(request()->query(), ['role' => 'semua', 'page' => 1])) }}"
       class="figma-role-pill {{ $roleFilter === 'semua' ? 'figma-role-pill--active' : '' }}">
        <span>Semua</span>
        <span class="figma-role-count">{{ $countSemua }}</span>
    </a>

    <a href="{{ route('koordinator.master-data.pengguna', array_merge(request()->query(), ['role' => 'mahasiswa', 'page' => 1])) }}"
       class="figma-role-pill {{ $roleFilter === 'mahasiswa' ? 'figma-role-pill--active' : '' }}">
        <span>Mahasiswa</span>
        <span class="figma-role-count">{{ $countMahasiswa }}</span>
    </a>

    <a href="{{ route('koordinator.master-data.pengguna', array_merge(request()->query(), ['role' => 'dosen', 'page' => 1])) }}"
       class="figma-role-pill {{ $roleFilter === 'dosen' ? 'figma-role-pill--active' : '' }}">
        <span>Dosen</span>
        <span class="figma-role-count">{{ $countDosen }}</span>
    </a>

    <a href="{{ route('koordinator.master-data.pengguna', array_merge(request()->query(), ['role' => 'admin', 'page' => 1])) }}"
       class="figma-role-pill {{ $roleFilter === 'admin' ? 'figma-role-pill--active' : '' }}">
        <span>Admin</span>
        <span class="figma-role-count">{{ $countAdmin }}</span>
    </a>
</div>

{{-- ═══════════ 4. Pencarian dan Filter Status ═══════════ --}}
<form method="GET" action="{{ route('koordinator.master-data.pengguna') }}" id="filter-user-form">
    <input type="hidden" name="role" value="{{ $roleFilter }}">

    <div class="figma-filter-bar">
        <div class="figma-filter-left">
            {{-- Search Bar --}}
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
                       placeholder="Cari nama, ID/NIM, atau email kampus..."
                       autocomplete="off">
            </div>

            {{-- Filter Status Akun --}}
            <select name="status" class="figma-select" onchange="document.getElementById('filter-user-form').submit()">
                <option value="semua" {{ $statusFilter === 'semua' ? 'selected' : '' }}>Status Akun: Semua</option>
                <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Status: Aktif</option>
                <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Status: Nonaktif</option>
            </select>

            @if(request()->filled('search') || (request()->filled('status') && request('status') !== 'semua'))
                <a href="{{ route('koordinator.master-data.pengguna', ['role' => $roleFilter]) }}"
                   style="font-size: 12.5px; color: var(--sim-text-muted); text-decoration: none; padding: 6px;"
                   title="Reset filter">
                    ✕ Reset
                </a>
            @endif
        </div>

        <div class="figma-filter-count">
            {{ $users->total() }} pengguna terdaftar
        </div>
    </div>
</form>

{{-- ═══════════ 5. Tabel Data Pengguna ═══════════ --}}
<div class="figma-table-card">
    <table class="figma-table">
        <thead>
            <tr>
                <th style="width: 14%;">ID / NIM</th>
                <th style="width: 24%;">NAMA PENGGUNA</th>
                <th style="width: 22%;">EMAIL KAMPUS</th>
                <th style="width: 12%;">PERAN</th>
                <th style="width: 16%;">KONTEKS / RELASI</th>
                <th style="width: 12%;">STATUS AKUN</th>
                <th class="col-aksi" style="width: 10%;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                @php
                    $initials = strtoupper(substr($user->nama, 0, 2));
                @endphp
                <tr>
                    {{-- 1. ID / NIM --}}
                    <td>
                        <span style="font-family: monospace; font-size: 12.5px; font-weight: 600; color: var(--sim-brown-dark);">
                            {{ $user->identifier }}
                        </span>
                    </td>

                    {{-- 2. Nama Pengguna --}}
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #F3EDE4; color: var(--sim-brown-dark); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                {{ $initials }}
                            </div>
                            <span style="font-weight: 600; font-size: 13.5px; color: var(--sim-brown-dark);">
                                {{ $user->nama }}
                            </span>
                        </div>
                    </td>

                    {{-- 3. Email Kampus --}}
                    <td>
                        <span style="font-size: 13px; color: var(--sim-text-body);">
                            {{ $user->email }}
                        </span>
                    </td>

                    {{-- 4. Peran --}}
                    <td>
                        <span class="badge-role badge-role-{{ $user->role_key }}">
                            <span class="badge-role-dot"></span>
                            <span>{{ $user->role_label }}</span>
                        </span>
                    </td>

                    {{-- 5. Konteks / Relasi --}}
                    <td>
                        <span style="font-size: 12.5px; color: var(--sim-text-muted);">
                            {{ $user->konteks }}
                        </span>
                    </td>

                    {{-- 6. Status Akun --}}
                    <td>
                        <span class="figma-status-badge {{ $user->is_aktif ? 'figma-status-badge--active' : 'figma-status-badge--inactive' }}">
                            <span class="figma-status-dot"></span>
                            <span>{{ $user->is_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                        </span>
                    </td>

                    {{-- 7. Aksi (Edit, Toggle Status, Reset Password) --}}
                    <td class="col-aksi">
                        <div style="display: inline-flex; align-items: center; gap: 6px;">
                            {{-- Edit --}}
                            <button type="button"
                                    class="figma-action-btn"
                                    title="Edit data pengguna"
                                    onclick="openModalEdit('{{ $user->role_key }}', {{ $user->id }}, '{{ addslashes($user->nama) }}', '{{ $user->identifier }}', '{{ $user->email }}', {{ $user->kelas_id ?? 'null' }})">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            {{-- Reset Password --}}
                            <button type="button"
                                    class="figma-action-btn"
                                    title="Reset kata sandi"
                                    onclick="openModalReset('{{ $user->role_key }}', {{ $user->id }}, '{{ addslashes($user->nama) }}')">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 2l-2 2m-1.5 1.5L14 9l-3-3 2.5-2.5a2.121 2.121 0 0 1 3 3L15 8"/>
                                    <circle cx="7.5" cy="15.5" r="5.5"/>
                                    <path d="m3.5 19.5 2-2"/>
                                </svg>
                            </button>

                            {{-- Toggle Status --}}
                            @if($user->role_key !== 'admin')
                            <form method="POST"
                                  action="{{ route('koordinator.master-data.pengguna.toggle', ['role' => $user->role_key, 'id' => $user->id]) }}"
                                  style="display: inline; margin: 0;">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="figma-action-btn"
                                        title="{{ $user->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }} akun">
                                    @if($user->is_aktif)
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    @else
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 48px 24px; text-align: center; color: var(--sim-text-muted);">
                        <div style="font-size: 14px; font-weight: 600; color: var(--sim-brown-dark); margin-bottom: 4px;">
                            Tidak ada pengguna ditemukan.
                        </div>
                        <p style="font-size: 13px; margin: 0;">Ubah kata kunci pencarian atau tambahkan pengguna baru.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination Bar --}}
    <div class="figma-pagination">
        <div>
            @if($users->total() > 0)
                Menampilkan {{ $users->firstItem() ?? 1 }}–{{ $users->lastItem() ?? $users->total() }} dari {{ $users->total() }} pengguna
            @else
                Menampilkan 0 pengguna
            @endif
        </div>

        <div class="figma-pager-controls">
            {{-- Prev --}}
            @if($users->onFirstPage())
                <button type="button" class="figma-pager-btn" disabled>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="figma-pager-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif

            <span class="figma-page-number">{{ $users->currentPage() }}</span>

            <span style="font-size: 12.5px; color: var(--sim-text-muted);">
                dari {{ max(1, $users->lastPage()) }}
            </span>

            {{-- Next --}}
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="figma-pager-btn">
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

{{-- ═══════════ 6. Bagian Bawah: Tombol Reset Kata Sandi Sesuai Desain Figma ═══════════ --}}
<div style="margin-top: 18px; margin-bottom: 28px;">
    <button type="button" class="btn-figma-reset-bottom" onclick="openModalResetGlobal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="7.5" cy="15.5" r="5.5"/>
            <path d="m21 2-9.6 9.6"/>
            <path d="m15.5 7.5 2.5 2.5"/>
            <path d="m18.5 4.5 2.5 2.5"/>
        </svg>
        <span>Reset kata sandi</span>
    </button>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL 1: TAMBAH PENGGUNA
     ══════════════════════════════════════════════════════ --}}
<div id="modal-tambah-user" class="figma-modal-overlay">
    <div class="figma-modal" role="dialog" aria-modal="true">
        <h3 class="figma-modal-title">Tambah Pengguna Baru</h3>
        <p class="figma-modal-desc">Buat akun pengguna baru dengan menetapkan peran serta identitas akademik.</p>

        <form method="POST" action="{{ route('koordinator.master-data.pengguna.store') }}">
            @csrf

            <div class="figma-form-group">
                <label class="figma-form-label" for="add-role">Peran Pengguna <span style="color:#DC2626">*</span></label>
                <select id="add-role" name="role" class="figma-input-select" onchange="toggleKelasField(this.value, 'group-kelas-add')" required>
                    <option value="mahasiswa" selected>Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="admin">Admin / Koordinator</option>
                </select>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label" for="add-nama">Nama Lengkap <span style="color:#DC2626">*</span></label>
                <input type="text" id="add-nama" name="nama" class="figma-input" placeholder="cth: Muhammad Rizky" required maxlength="100">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="figma-form-group">
                    <label class="figma-form-label" for="add-identifier">ID / NIM / NIDN <span style="color:#DC2626">*</span></label>
                    <input type="text" id="add-identifier" name="identifier" class="figma-input" placeholder="cth: 2241720050" required maxlength="30">
                </div>
                <div class="figma-form-group">
                    <label class="figma-form-label" for="add-email">Email Kampus <span style="color:#DC2626">*</span></label>
                    <input type="email" id="add-email" name="email" class="figma-input" placeholder="user@polinema.ac.id" required maxlength="100">
                </div>
            </div>

            <div id="group-kelas-add" class="figma-form-group">
                <label class="figma-form-label" for="add-kelas">Penempatan Kelas (Mahasiswa)</label>
                <select id="add-kelas" name="kelas_id" class="figma-input-select">
                    <option value="">— Belum Ditetapkan —</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }})</option>
                    @endforeach
                </select>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label" for="add-password">Kata Sandi Awal <span style="color:#DC2626">*</span></label>
                <input type="password" id="add-password" name="password" class="figma-input" placeholder="Minimal 6 karakter" required minlength="6">
            </div>

            <div class="figma-form-actions">
                <button type="button" class="btn-figma-outline" onclick="closeModalTambah()">Batal</button>
                <button type="submit" class="btn-figma-primary">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL 2: EDIT PENGGUNA
     ══════════════════════════════════════════════════════ --}}
<div id="modal-edit-user" class="figma-modal-overlay">
    <div class="figma-modal" role="dialog" aria-modal="true">
        <h3 class="figma-modal-title" id="edit-user-title">Edit Pengguna</h3>
        <p class="figma-modal-desc">Perbarui data profil dan identitas akun pengguna.</p>

        <form method="POST" id="form-edit-user" action="">
            @csrf
            @method('PUT')

            <div class="figma-form-group">
                <label class="figma-form-label" for="edit-nama">Nama Lengkap <span style="color:#DC2626">*</span></label>
                <input type="text" id="edit-nama" name="nama" class="figma-input" required maxlength="100">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="figma-form-group">
                    <label class="figma-form-label" for="edit-identifier">ID / NIM / NIDN <span style="color:#DC2626">*</span></label>
                    <input type="text" id="edit-identifier" name="identifier" class="figma-input" required maxlength="30">
                </div>
                <div class="figma-form-group">
                    <label class="figma-form-label" for="edit-email">Email Kampus <span style="color:#DC2626">*</span></label>
                    <input type="email" id="edit-email" name="email" class="figma-input" required maxlength="100">
                </div>
            </div>

            <div id="group-kelas-edit" class="figma-form-group">
                <label class="figma-form-label" for="edit-kelas">Penempatan Kelas</label>
                <select id="edit-kelas" name="kelas_id" class="figma-input-select">
                    <option value="">— Belum Ditetapkan —</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->prodi }})</option>
                    @endforeach
                </select>
            </div>

            <div class="figma-form-actions">
                <button type="button" class="btn-figma-outline" onclick="closeModalEdit()">Batal</button>
                <button type="submit" class="btn-figma-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL 3: RESET KATA SANDI
     ══════════════════════════════════════════════════════ --}}
<div id="modal-reset-user" class="figma-modal-overlay">
    <div class="figma-modal" role="dialog" aria-modal="true" style="max-width: 460px;">
        <h3 class="figma-modal-title">Reset Kata Sandi</h3>
        <p class="figma-modal-desc" id="reset-user-desc-fixed">
            Atur ulang kata sandi untuk akun <strong id="reset-user-name"></strong>.
        </p>
        <p class="figma-modal-desc" id="reset-user-desc-global" style="display: none;">
            Pilih akun pengguna yang ingin diatur ulang kata sandinya ke kata sandi baru.
        </p>

        <form method="POST" id="form-reset-user" action="">
            @csrf

            <div class="figma-form-group" id="reset-user-select-group" style="display: none;">
                <label class="figma-form-label" for="reset-target-user">Pilih Akun Pengguna <span style="color:#DC2626">*</span></label>
                <select id="reset-target-user" name="target_user" class="figma-input-select">
                    <option value="">-- Pilih Akun --</option>
                    @if(isset($allUsersForSelect))
                        <optgroup label="Dosen">
                            @foreach($allUsersForSelect->where('role_key', 'dosen') as $u)
                                <option value="dosen:{{ $u->id }}">{{ $u->identifier }} — {{ $u->nama }} ({{ $u->email }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Mahasiswa">
                            @foreach($allUsersForSelect->where('role_key', 'mahasiswa') as $u)
                                <option value="mahasiswa:{{ $u->id }}">{{ $u->identifier }} — {{ $u->nama }} ({{ $u->email }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Admin / Koordinator">
                            @foreach($allUsersForSelect->where('role_key', 'admin') as $u)
                                <option value="admin:{{ $u->id }}">{{ $u->identifier }} — {{ $u->nama }} ({{ $u->email }})</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label" for="reset-pass">Kata Sandi Baru <span style="color:#DC2626">*</span></label>
                <input type="text" id="reset-pass" name="password" class="figma-input" value="password123" required minlength="6">
                <span style="font-size: 11px; color: var(--sim-text-muted); display: block; margin-top: 4px;">
                    Kata sandi default: <code>password123</code>
                </span>
            </div>

            <div class="figma-form-actions">
                <button type="button" class="btn-figma-outline" onclick="closeModalReset()">Batal</button>
                <button type="submit" class="btn-figma-primary">Reset Kata Sandi</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL 4: UNDANG PENGGUNA
     ══════════════════════════════════════════════════════ --}}
<div id="modal-undang-user" class="figma-modal-overlay">
    <div class="figma-modal" role="dialog" aria-modal="true" style="max-width: 460px;">
        <h3 class="figma-modal-title">Undang Pengguna</h3>
        <p class="figma-modal-desc">Kirim tautan undangan aktivasi akun ke alamat email institusi kampus.</p>

        <form method="POST" action="{{ route('koordinator.master-data.pengguna.invite') }}">
            @csrf

            <div class="figma-form-group">
                <label class="figma-form-label" for="invite-email">Email Kampus <span style="color:#DC2626">*</span></label>
                <input type="email" id="invite-email" name="email" class="figma-input" placeholder="nama@polinema.ac.id" required>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label" for="invite-role">Peran <span style="color:#DC2626">*</span></label>
                <select id="invite-role" name="role" class="figma-input-select" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="admin">Admin / Koordinator</option>
                </select>
            </div>

            <div class="figma-form-actions">
                <button type="button" class="btn-figma-outline" onclick="closeModalUndang()">Batal</button>
                <button type="submit" class="btn-figma-primary">Kirim Undangan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Toggle Input Kelas pada Modal Tambah ──
    function toggleKelasField(role, containerId) {
        const el = document.getElementById(containerId);
        if (el) {
            el.style.display = (role === 'mahasiswa') ? 'block' : 'none';
        }
    }

    // Modal Tambah
    function openModalTambah() {
        document.getElementById('modal-tambah-user').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModalTambah() {
        document.getElementById('modal-tambah-user').classList.remove('open');
        document.body.style.overflow = '';
    }

    // Modal Edit
    function openModalEdit(role, id, nama, identifier, email, kelasId) {
        document.getElementById('edit-user-title').textContent = 'Edit Pengguna — ' + nama;
        document.getElementById('form-edit-user').action = '/koordinator/master-data/pengguna/' + role + '/' + id;
        document.getElementById('edit-nama').value = nama;
        document.getElementById('edit-identifier').value = identifier;
        document.getElementById('edit-email').value = email;

        const groupKelas = document.getElementById('group-kelas-edit');
        if (role === 'mahasiswa') {
            groupKelas.style.display = 'block';
            const selKelas = document.getElementById('edit-kelas');
            for (let i = 0; i < selKelas.options.length; i++) {
                selKelas.options[i].selected = (parseInt(selKelas.options[i].value) === kelasId);
            }
        } else {
            groupKelas.style.display = 'none';
        }

        document.getElementById('modal-edit-user').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModalEdit() {
        document.getElementById('modal-edit-user').classList.remove('open');
        document.body.style.overflow = '';
    }

    // Modal Reset Password (spesifik dari tombol aksi baris tabel)
    function openModalReset(role, id, nama) {
        document.getElementById('reset-user-desc-fixed').style.display = 'block';
        document.getElementById('reset-user-desc-global').style.display = 'none';
        document.getElementById('reset-user-select-group').style.display = 'none';
        document.getElementById('reset-target-user').removeAttribute('required');
        document.getElementById('reset-user-name').textContent = nama;
        document.getElementById('form-reset-user').action = '/koordinator/master-data/pengguna/' + role + '/' + id + '/reset-password';
        document.getElementById('modal-reset-user').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    // Modal Reset Password Global (dari tombol bawah tabel sesuai desain Figma)
    function openModalResetGlobal() {
        document.getElementById('reset-user-desc-fixed').style.display = 'none';
        document.getElementById('reset-user-desc-global').style.display = 'block';
        const group = document.getElementById('reset-user-select-group');
        group.style.display = 'block';
        const sel = document.getElementById('reset-target-user');
        sel.setAttribute('required', 'required');
        sel.value = '';
        document.getElementById('form-reset-user').action = '{{ route('koordinator.master-data.pengguna.reset-password-general') }}';
        document.getElementById('modal-reset-user').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModalReset() {
        document.getElementById('modal-reset-user').classList.remove('open');
        document.body.style.overflow = '';
    }

    // Modal Undang Pengguna
    function openModalUndang() {
        document.getElementById('modal-undang-user').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModalUndang() {
        document.getElementById('modal-undang-user').classList.remove('open');
        document.body.style.overflow = '';
    }

    // Tutup backdrop saat klik luar
    document.querySelectorAll('.figma-modal-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
    });

    // Tutup dengan Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.figma-modal-overlay.open').forEach(function(modal) {
                modal.classList.remove('open');
            });
            document.body.style.overflow = '';
        }
    });

    // Enter submit on search
    const userSearchInput = document.querySelector('.figma-search-input');
    if (userSearchInput) {
        userSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filter-user-form').submit();
            }
        });
    }
</script>
@endpush

