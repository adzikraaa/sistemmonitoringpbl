<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ruang Koordinator') — SIM-PBL</title>
    <meta name="description" content="@yield('meta_description', 'Sistem Informasi Monitoring Project Based Learning - Ruang Koordinator')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        :root {
            --krd-sidebar-w: 220px;
            --krd-sidebar-bg: #21150E;
            --krd-sidebar-hover: rgba(255, 255, 255, 0.05);
            --krd-sidebar-active: #4F3526;
            --krd-sidebar-text: #BCAE9E;
            --krd-sidebar-text-muted: #7A695B;
            --krd-bg: #FAF8F5;
            --krd-card-bg: #FFFFFF;
            --krd-brown-dark: #2B221D;
            --krd-brown-btn: #5C4436;
            --krd-brown-btn-hover: #483428;
            --krd-cream-head: #F3EDE4;
            --krd-border-warm: #E6DFD5;
            --krd-border-input: #DDD6CE;
            --krd-text-dark: #2B221D;
            --krd-text-body: #574B40;
            --krd-text-muted: #7A6E63;
            --krd-text-light: #9B8E82;
            --krd-green-badge-bg: #EDF4EA;
            --krd-green-badge-text: #2A5A3B;
            --krd-green-badge-dot: #2E7D32;

            /* Aliases untuk komponen konten */
            --sim-bg: #FAF8F5;
            --sim-card-bg: #FFFFFF;
            --sim-brown-dark: #2B221D;
            --sim-brown-btn: #5C4436;
            --sim-brown-btn-hover: #483428;
            --sim-cream-head: #F3EDE4;
            --sim-border-warm: #E6DFD5;
            --sim-border-input: #DDD6CE;
            --sim-text-dark: #2B221D;
            --sim-text-body: #574B40;
            --sim-text-muted: #7A6E63;
            --sim-text-light: #9B8E82;
            --sim-green-badge-bg: #EDF4EA;
            --sim-green-badge-text: #2A5A3B;
            --sim-green-badge-dot: #2E7D32;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            background-color: var(--krd-bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--krd-text-dark);
            -webkit-font-smoothing: antialiased;
        }

        body.sim-koordinator-body {
            display: flex;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ══════════════════════════════════════════════════════
           1. SIDEBAR KIRI (LEBAR TETAP 220px, PERMANEN)
           ══════════════════════════════════════════════════════ */
        .krd-sidebar {
            width: var(--krd-sidebar-w);
            min-width: var(--krd-sidebar-w);
            max-width: var(--krd-sidebar-w);
            height: 100vh;
            background-color: var(--krd-sidebar-bg);
            color: #FAF8F5;
            position: sticky;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-y: auto;
            z-index: 150;
            padding: 20px 14px 18px 14px;
            flex-shrink: 0;
        }

        /* Logo Brand */
        .krd-logo-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 6px 18px 6px;
        }
        .krd-logo-circle {
            width: 32px;
            height: 32px;
            background: #F4EDE4;
            color: #21150E;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 16px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .krd-logo-brand {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 17px;
            font-weight: 400;
            letter-spacing: 0.02em;
            color: #FAF8F5;
            line-height: 1.1;
        }
        .krd-logo-subtitle {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.09em;
            color: var(--krd-sidebar-text-muted);
            text-transform: uppercase;
            display: block;
            margin-top: 3px;
        }

        /* Section Heading */
        .krd-nav-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: var(--krd-sidebar-text-muted);
            text-transform: uppercase;
            margin: 12px 8px 8px 8px;
        }

        /* Navigation Menu List */
        .krd-nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .krd-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 7px;
            color: var(--krd-sidebar-text);
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
        }
        .krd-nav-link:hover {
            background: var(--krd-sidebar-hover);
            color: #FFFFFF;
        }
        .krd-nav-link--active {
            background: var(--krd-sidebar-active) !important;
            color: #FFFFFF !important;
            font-weight: 600;
        }
        .krd-nav-icon {
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: inherit;
        }

        /* Submenu Master Data */
        .krd-submenu-list {
            list-style: none;
            margin: 2px 0 4px 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .krd-submenu-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px 7px 36px;
            border-radius: 6px;
            color: var(--krd-sidebar-text);
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
        }
        .krd-submenu-link:hover {
            background: var(--krd-sidebar-hover);
            color: #FFFFFF;
        }
        .krd-submenu-link--active {
            background: var(--krd-sidebar-active) !important;
            color: #FFFFFF !important;
            font-weight: 600;
        }
        .krd-submenu-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #FFFFFF;
            display: inline-block;
        }

        /* Sidebar Info Widgets */
        .krd-sidebar-info-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
            padding: 9px 12px;
            margin-top: 10px;
        }
        .krd-info-title {
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--krd-sidebar-text-muted);
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .krd-info-val {
            font-size: 11.5px;
            font-weight: 600;
            color: #FAF8F5;
            margin: 0;
            line-height: 1.3;
        }
        .krd-info-sub {
            font-size: 9.5px;
            color: var(--krd-sidebar-text-muted);
            margin: 2px 0 0 0;
        }

        /* Sidebar User Profile Bottom */
        .krd-user-profile-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 14px;
            margin-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        .krd-user-left {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }
        .krd-user-avatar {
            width: 30px;
            height: 30px;
            background: #F4EDE4;
            color: #21150E;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .krd-user-details {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .krd-user-name {
            font-size: 12px;
            font-weight: 600;
            color: #FAF8F5;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        .krd-user-role {
            font-size: 10px;
            color: var(--krd-sidebar-text-muted);
            line-height: 1.2;
        }
        .krd-logout-btn {
            background: none;
            border: none;
            color: var(--krd-sidebar-text-muted);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s;
        }
        .krd-logout-btn:hover {
            color: #FFFFFF;
        }

        /* ══════════════════════════════════════════════════════
           2. AREA KONTEN UTAMA (MEMENUHI SELURUH SISA LEBAR)
           ══════════════════════════════════════════════════════ */
        .krd-content-wrapper {
            flex: 1;
            min-width: 0;
            width: calc(100% - var(--krd-sidebar-w));
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Top Header Membentang Penuh */
        .krd-top-header {
            height: 58px;
            width: 100%;
            background: #FAF8F5;
            border-bottom: 1px solid var(--krd-border-warm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-sizing: border-box;
        }
        .krd-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .krd-hamburger {
            display: none;
            background: none;
            border: none;
            padding: 6px;
            border-radius: 6px;
            cursor: pointer;
            color: var(--krd-brown-dark);
        }
        .krd-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        .krd-breadcrumb-root {
            color: var(--krd-text-muted);
            font-weight: 400;
        }
        .krd-breadcrumb-sep {
            color: #C8BFB4;
            font-size: 12px;
        }
        .krd-breadcrumb-current {
            color: var(--krd-brown-dark);
            font-weight: 600;
        }

        .krd-header-meta {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .krd-semester-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--krd-green-badge-bg);
            color: var(--krd-green-badge-text);
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .krd-semester-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--krd-green-badge-dot);
        }
        .krd-header-date {
            font-size: 12.5px;
            color: var(--krd-text-muted);
            font-weight: 400;
        }
        .krd-header-bell {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: var(--krd-brown-dark);
            text-decoration: none;
            transition: background 0.15s;
        }
        .krd-header-bell:hover {
            background: #F3EDE4;
        }

        /* Konten Utama: 100% Lebar Desktop, Padding Proporsional 30px */
        .krd-main-body {
            flex: 1;
            width: 100%;
            max-width: none; /* Hilangkan pembatas max-width */
            padding: 28px 32px 48px;
            box-sizing: border-box;
        }

        /* Footer Membentang Penuh */
        .krd-page-footer {
            width: 100%;
            padding: 20px 32px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--krd-border-warm);
            font-size: 12px;
            color: var(--krd-text-muted);
            box-sizing: border-box;
        }

        /* Backdrop untuk Mobile Drawer */
        .krd-sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(33, 21, 14, 0.45);
            z-index: 140;
        }

        /* ══════════════════════════════════════════════════════
           RESPONSIVITAS DESKTOP, LAPTOP, TABLET & MOBILE
           ══════════════════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .krd-sidebar {
                width: 200px;
                min-width: 200px;
                max-width: 200px;
            }
            .krd-content-wrapper {
                width: calc(100% - 200px);
            }
            .krd-top-header {
                padding: 0 24px;
            }
            .krd-main-body {
                padding: 24px;
            }
            .krd-page-footer {
                padding: 16px 24px;
            }
        }

        @media (max-width: 768px) {
            .krd-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                transition: transform 0.25s ease-in-out;
                z-index: 200;
                box-shadow: 4px 0 24px rgba(0,0,0,0.25);
            }
            .krd-sidebar.open {
                transform: translateX(0);
            }
            .krd-sidebar-backdrop.open {
                display: block;
            }
            .krd-content-wrapper {
                width: 100%;
            }
            .krd-hamburger {
                display: inline-flex;
            }
            .krd-top-header {
                padding: 0 16px;
            }
            .krd-main-body {
                padding: 16px;
            }
            .krd-header-date {
                display: none;
            }
            .krd-page-footer {
                padding: 16px;
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }
        }
    </style>
</head>
<body class="sim-koordinator-body">

    {{-- Backdrop Overlay untuk Mobile --}}
    <div id="krd-backdrop" class="krd-sidebar-backdrop" onclick="toggleKrdSidebar()"></div>

    {{-- ══════════════════════════════════════════════════════
         1. SIDEBAR KIRI (PERMANEN LEBAR 220px)
         ══════════════════════════════════════════════════════ --}}
    <aside id="krd-sidebar" class="krd-sidebar">
        <div>
            {{-- Logo Brand --}}
            <div class="krd-logo-wrap">
                <div class="krd-logo-circle">S</div>
                <div>
                    <div class="krd-logo-brand">SIM-PBL</div>
                    <span class="krd-logo-subtitle">RUANG ADMIN / KOORDINATOR</span>
                </div>
            </div>

            {{-- Navigasi Utama --}}
            <nav>
                <div class="krd-nav-label">ADMINISTRASI AKADEMIK</div>
                <ul class="krd-nav-list">
                    {{-- Dashboard --}}
                    <li>
                        <a href="{{ route('koordinator.dashboard') }}"
                           class="krd-nav-link {{ request()->routeIs('koordinator.dashboard') ? 'krd-nav-link--active' : '' }}">
                            <span class="krd-nav-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                                </svg>
                            </span>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Semester & Periode --}}
                    <li>
                        <a href="{{ route('koordinator.periode.index') }}" 
                        class="krd-nav-link {{ request()->routeIs('koordinator.periode.*') ? 'krd-nav-link--active' : '' }}">
                            <span class="krd-nav-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                            </span>
                            <span>Semester & Periode</span>
                        </a>
                    </li>

                    {{-- Master Data --}}
                    <li>
                        <a href="{{ route('koordinator.master-data.kelas') }}"
                           class="krd-nav-link {{ request()->routeIs('koordinator.master-data.*') || request()->routeIs('koordinator.kelas') ? 'krd-nav-link--active' : '' }}">
                            <span class="krd-nav-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                                </svg>
                            </span>
                            <span>Master Data</span>
                        </a>
                        {{-- Submenu Master Data Sesuai Figma --}}
                        <ul class="krd-submenu-list">
                            <li>
                                <a href="{{ route('koordinator.master-data.mata-kuliah') }}"
                                   class="krd-submenu-link {{ request()->routeIs('koordinator.master-data.mata-kuliah*') ? 'krd-submenu-link--active' : '' }}">
                                    <span>Mata Kuliah</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('koordinator.master-data.kelas') }}"
                                   class="krd-submenu-link {{ request()->routeIs('koordinator.master-data.kelas') || request()->routeIs('koordinator.kelas') ? 'krd-submenu-link--active' : '' }}">
                                    <span>Kelas</span>
                                    <span class="krd-submenu-dot"></span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('koordinator.master-data.pengguna') }}"
                                   class="krd-submenu-link {{ request()->routeIs('koordinator.master-data.pengguna*') || request()->routeIs('koordinator.pengguna*') ? 'krd-submenu-link--active' : '' }}">
                                    <span>Pengguna</span>
                                    <span class="krd-submenu-dot"></span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Milestone Global --}}
                    <li>
                        <a href="#" class="krd-nav-link">
                            <span class="krd-nav-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                                    <line x1="4" y1="22" x2="4" y2="15"/>
                                </svg>
                            </span>
                            <span>Milestone Global</span>
                        </a>
                    </li>

                    {{-- Penugasan Dosen --}}
                    <li>
                        <a href="{{ route('koordinator.penugasan-dosen.index') }}"
                           class="krd-nav-link {{ request()->routeIs('koordinator.penugasan-dosen.*') ? 'krd-nav-link--active' : '' }}">
                            <span class="krd-nav-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="8.5" cy="7" r="4"/>
                                    <line x1="20" y1="8" x2="20" y2="14"/>
                                    <line x1="23" y1="11" x2="17" y2="11"/>
                                </svg>
                            </span>
                            <span>Penugasan Dosen</span>
                        </a>
                    </li>

                    {{-- Kelompok PBL --}}
                    <li>
                        <a href="#" class="krd-nav-link">
                            <span class="krd-nav-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </span>
                            <span>Kelompok PBL</span>
                        </a>
                    </li>
                </ul>

                {{-- Widget 1: Informasi Program Studi --}}
                <div class="krd-sidebar-info-card">
                    <div class="krd-info-title">PROGRAM STUDI</div>
                    <p class="krd-info-val">D4 Sistem Informasi Bisnis</p>
                </div>

                {{-- Widget 2: Informasi Semester Aktif --}}
                @php
                    $periodeAktif = \App\Models\Periode::where('is_aktif', true)->first();
                @endphp
                <div class="krd-sidebar-info-card">
                    <div class="krd-info-title">SEMESTER AKTIF</div>
                    <p class="krd-info-val">{{ $periodeAktif ? $periodeAktif->nama_periode : 'Ganjil 2026/2027' }}</p>
                    <p class="krd-info-sub">
                        {{ $periodeAktif && $periodeAktif->tanggal_mulai ? $periodeAktif->tanggal_mulai->format('d M Y') . ' — ' . $periodeAktif->tanggal_selesai->format('d M Y') : '31 Agu 2026 — 15 Jan 2027' }}
                    </p>
                </div>
            </nav>
        </div>

        {{-- Profil Pengguna Koordinator & Tombol Keluar (Bagian Bawah Sidebar) --}}
        @php
            $koordinatorUser = Auth::guard('koordinator')->user();
            $namaKoordinator = $koordinatorUser?->nama ?? 'Nadia Kusuma';
            $initialKoordinator = strtoupper(substr($namaKoordinator, 0, 2));
        @endphp
        <div class="krd-user-profile-bar">
            <div class="krd-user-left">
                <div class="krd-user-avatar">{{ $initialKoordinator }}</div>
                <div class="krd-user-details">
                    <span class="krd-user-name" title="{{ $namaKoordinator }}">{{ $namaKoordinator }}</span>
                    <span class="krd-user-role">Admin / Koordinator</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="krd-logout-btn" title="Keluar dari sistem" aria-label="Keluar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- ══════════════════════════════════════════════════════
         2. AREA KONTEN UTAMA & HEADER (LEBAR PENUH SISA LAYAR)
         ══════════════════════════════════════════════════════ --}}
    <div class="krd-content-wrapper">
        {{-- Top Header Membentang Penuh --}}
        <header class="krd-top-header">
            <div class="krd-header-left">
                {{-- Hamburger Menu untuk Mobile --}}
                <button type="button" class="krd-hamburger" onclick="toggleKrdSidebar()" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>

                {{-- Breadcrumb --}}
                <div class="krd-breadcrumb">
                    <span class="krd-breadcrumb-root">Ruang Koordinator</span>
                    <span class="krd-breadcrumb-sep">/</span>
                    <span class="krd-breadcrumb-current">@yield('breadcrumb_title', 'Kelas')</span>
                </div>
            </div>

            {{-- Informasi Semester Aktif, Tanggal, dan Ikon Notifikasi --}}
            <div class="krd-header-meta">
                <div class="krd-semester-pill">
                    <span class="krd-semester-dot"></span>
                    <span>{{ $periodeAktif ? ($periodeAktif->nama_periode . ' · Aktif') : 'Ganjil 2026/2027 · Aktif' }}</span>
                </div>

                <div class="krd-header-date">
                    {{ now()->translatedFormat('d F Y') }}
                </div>

                <a href="#" class="krd-header-bell" title="Notifikasi" aria-label="Notifikasi">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </a>
            </div>
        </header>

        {{-- Konten Utama (Memenuhi Seluruh Lebar Desktop) --}}
        <main class="krd-main-body">
            @yield('content')
        </main>

        {{-- Footer Membentang Penuh --}}
        <footer class="krd-page-footer">
            <div>
                SIM-PBL · D4 Sistem Informasi Bisnis
            </div>
            <div>
                Data ilustratif · Semester Ganjil 2026/2027
            </div>
        </footer>
    </div>

    @stack('scripts')
    <script>
        function toggleKrdSidebar() {
            const sidebar = document.getElementById('krd-sidebar');
            const backdrop = document.getElementById('krd-backdrop');
            if (sidebar) sidebar.classList.toggle('open');
            if (backdrop) backdrop.classList.toggle('open');
        }
    </script>
</body>
</html>
