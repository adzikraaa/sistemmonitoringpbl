<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Penugasan Dosen - Ruang Koordinator SIM-PBL')</title>
    <meta name="description" content="@yield('meta_description', 'Sistem Informasi Monitoring Project Based Learning - Ruang Koordinator')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        :root {
            --sim-sidebar-w: 248px;
            --sim-sidebar-bg: #2D2621;
        }

        body.sim-body {
            background-color: #FAF8F4;
            color: #2D2621;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }

        .sim-header__breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            min-width: 0;
            font-size: 13.5px;
        }

        .sim-header__breadcrumb-root {
            color: #786F65;
            font-weight: 500;
        }

        .sim-header__breadcrumb-sep {
            color: #B5AA9C;
            flex-shrink: 0;
        }

        .sim-header__breadcrumb-current {
            color: #2D2621;
            font-weight: 600;
        }

        /* Koordinator Sidebar Customizations */
        .koor-sidebar {
            width: var(--sim-sidebar-w);
            background: #2D2621;
        }

        .koor-sidebar__logo-sub {
            display: block;
            font-size: 9px;
            letter-spacing: 0.08em;
            color: #A3978A;
            text-transform: uppercase;
            margin-top: 1px;
            font-weight: 600;
        }

        .koor-sidebar__nav-subgroup {
            margin-left: 28px;
            padding-left: 10px;
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            margin-top: 2px;
            margin-bottom: 4px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .koor-sidebar__sub-item {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.45);
            padding: 5px 8px;
            border-radius: 6px;
            text-decoration: none;
            display: block;
            transition: all 0.15s;
        }

        .koor-sidebar__sub-item:hover {
            color: #FAF8F4;
            background: rgba(255, 255, 255, 0.05);
        }

        .koor-sidebar__prodi-box {
            padding: 0 16px 12px;
        }

        .koor-sidebar__prodi-label {
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 0.08em;
            color: #A3978A;
            text-transform: uppercase;
            margin: 0 0 2px;
        }

        .koor-sidebar__prodi-value {
            font-size: 12px;
            font-weight: 500;
            color: #E8E2D8;
            margin: 0;
        }

        .koor-sidebar__semester-card {
            background: #231D19;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 10px 12px;
            margin: 0 12px 14px;
        }

        .koor-sidebar__semester-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #B87C2B;
            text-transform: uppercase;
            margin: 0 0 3px;
        }

        .koor-sidebar__semester-title {
            font-size: 13px;
            font-weight: 600;
            color: #FAF8F4;
            margin: 0 0 2px;
        }

        .koor-sidebar__semester-range {
            font-size: 11px;
            color: #A3978A;
            margin: 0;
        }

        .koor-sidebar__user-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            background: rgba(0, 0, 0, 0.15);
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .koor-sidebar__user-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .koor-sidebar__avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #5C3317;
            color: #F5EFE6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .koor-sidebar__user-details {
            min-width: 0;
        }

        .koor-sidebar__user-name {
            font-size: 13px;
            font-weight: 600;
            color: #FAF8F4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }

        .koor-sidebar__user-role {
            font-size: 11px;
            color: #A3978A;
            margin: 0;
        }

        .koor-sidebar__logout-btn {
            background: none;
            border: none;
            color: #A3978A;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .koor-sidebar__logout-btn:hover {
            color: #FAF8F4;
            background: rgba(255, 255, 255, 0.08);
        }

        /* Top Header Badges */
        .koor-header-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #D6ECDE;
            color: #2E5A3D;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1px solid #B8DBC4;
        }

        .koor-header-pill__dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #3B7454;
        }

        .koor-header-date {
            font-size: 13px;
            color: #786F65;
            font-weight: 500;
        }

        .koor-header-notif {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: #5C4F44;
            background: #FAF8F4;
            border: 1px solid #E8E2D8;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }

        .koor-header-notif:hover {
            background: #F4EFE6;
            color: #2D2621;
        }

        .koor-header-notif__badge {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #DC2626;
            border: 1.5px solid #FAF8F4;
        }
    </style>
</head>

<body class="sim-body">

    {{-- Sidebar Mobile Overlay --}}
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

    {{-- Sidebar Koordinator --}}
    <aside id="sidebar" class="sim-sidebar koor-sidebar">

        {{-- Logo Header --}}
        <div class="sim-sidebar__logo" style="padding: 18px 20px 16px;">
            <div class="sim-sidebar__logo-icon" style="background: #3B7454;">S</div>
            <div>
                <span class="sim-sidebar__logo-text" style="font-size: 15.5px; letter-spacing: 0.02em;">SIM-PBL</span>
                <span class="koor-sidebar__logo-sub">RUANG ADMIN/KOORDINATOR</span>
            </div>
        </div>

        {{-- Navigasi Menu --}}
        <nav class="sim-sidebar__nav" style="padding: 16px 12px 10px;">
            <p class="sim-sidebar__nav-label" style="color: #A3978A; font-size: 9.5px; margin-bottom: 8px;">ADMINISTRASI AKADEMIK</p>

            <ul class="sim-sidebar__menu">

                {{-- Dashboard --}}
                <li>
                    <a href="{{ route('koordinator.dashboard') }}"
                       class="sim-sidebar__menu-item {{ request()->routeIs('koordinator.dashboard') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        Dashboard
                    </a>
                </li>

                {{-- Semester & Periode --}}
                <li>
                    <a href="#" class="sim-sidebar__menu-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Semester & Periode
                    </a>
                </li>

                {{-- Master Data Group --}}
                <li>
                    <div class="sim-sidebar__menu-item" style="cursor: default; padding-bottom: 4px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="5" rx="9" ry="3"/>
                            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                        </svg>
                        <span>Master Data</span>
                    </div>
                    <div class="koor-sidebar__nav-subgroup">
                        <a href="#" class="koor-sidebar__sub-item">Mata Kuliah</a>
                        <a href="#" class="koor-sidebar__sub-item">Kelas</a>
                        <a href="#" class="koor-sidebar__sub-item">Pengguna</a>
                    </div>
                </li>

                {{-- Milestone Global --}}
                <li>
                    <a href="#" class="sim-sidebar__menu-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                            <line x1="4" y1="22" x2="4" y2="15"/>
                        </svg>
                        Milestone Global
                    </a>
                </li>

                {{-- Penugasan Dosen (Fitur Aktif) --}}
                <li>
                    <a href="{{ route('koordinator.penugasan-dosen.index') }}"
                       class="sim-sidebar__menu-item {{ request()->routeIs('koordinator.penugasan-dosen*') ? 'sim-sidebar__menu-item--active' : '' }}"
                       style="{{ request()->routeIs('koordinator.penugasan-dosen*') ? 'background: #3D2E24; border: 1px solid rgba(203, 161, 83, 0.25);' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <polyline points="16 11 18 13 22 9"/>
                        </svg>
                        <span>Penugasan Dosen</span>
                        @if(request()->routeIs('koordinator.penugasan-dosen*'))
                            <span class="sim-sidebar__menu-dot" style="background: #CBA153;"></span>
                        @endif
                    </a>
                </li>

                {{-- Kelompok PBL --}}
                <li>
                    <a href="#" class="sim-sidebar__menu-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Kelompok PBL
                    </a>
                </li>

            </ul>
        </nav>

        {{-- Bottom Section: Prodi & Semester Aktif --}}
        <div style="margin-top: auto;">
            <div class="koor-sidebar__prodi-box">
                <p class="koor-sidebar__prodi-label">PROGRAM STUDI</p>
                <p class="koor-sidebar__prodi-value">D4 Sistem Informasi Bisnis</p>
            </div>

            @php
                $sidebarPeriode = \App\Models\Periode::where('is_aktif', true)->first()
                    ?? \App\Models\Periode::latest()->first();
            @endphp
            <div class="koor-sidebar__semester-card">
                <p class="koor-sidebar__semester-label">SEMESTER AKTIF</p>
                <p class="koor-sidebar__semester-title">{{ $sidebarPeriode?->nama_periode ?? 'Ganjil 2026/2027' }}</p>
                <p class="koor-sidebar__semester-range">
                    @if($sidebarPeriode && $sidebarPeriode->tanggal_mulai && $sidebarPeriode->tanggal_selesai)
                        {{ $sidebarPeriode->tanggal_mulai->format('d M Y') }} — {{ $sidebarPeriode->tanggal_selesai->format('d M Y') }}
                    @else
                        31 Agu 2026 — 15 Jan 2027
                    @endif
                </p>
            </div>

            {{-- User Footer Profil & Logout --}}
            @php
                $userKoordinator = Auth::guard('koordinator')->user();
                $userName = $userKoordinator?->nama ?? 'Nadia Kusuma';
                $words = explode(' ', trim($userName));
                $userInitials = count($words) >= 2
                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                    : strtoupper(substr($userName, 0, 2));
            @endphp
            <div class="koor-sidebar__user-card">
                <div class="koor-sidebar__user-left">
                    <div class="koor-sidebar__avatar">{{ $userInitials }}</div>
                    <div class="koor-sidebar__user-details">
                        <p class="koor-sidebar__user-name" title="{{ $userName }}">{{ $userName }}</p>
                        <p class="koor-sidebar__user-role">Admin / Koordinator</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="koor-sidebar__logout-btn" title="Keluar dari sistem" aria-label="Keluar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- Main Container --}}
    <div class="sim-main" style="min-height: 100vh; display: flex; flex-direction: column;">

        {{-- Top Navigation Header --}}
        <header class="sim-header" style="background: #FFFFFF; border-bottom: 1px solid #E8E2D8; padding: 0 32px; height: 60px; display: flex; align-items: center; justify-content: space-between;">

            <div style="display: flex; align-items: center; gap: 14px;">
                <button class="sim-header__hamburger" onclick="toggleSidebar()" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>

                {{-- Breadcrumb --}}
                <div class="sim-header__breadcrumb">
                    <span class="sim-header__breadcrumb-root">Ruang Koordinator</span>
                    <span class="sim-header__breadcrumb-sep">/</span>
                    <span class="sim-header__breadcrumb-current">@yield('breadcrumb', 'Penugasan Dosen')</span>
                </div>
            </div>

            {{-- Header Right Items --}}
            <div style="display: flex; align-items: center; gap: 18px;">
                {{-- Active Period Pill --}}
                <div class="koor-header-pill">
                    <span class="koor-header-pill__dot"></span>
                    <span>{{ $sidebarPeriode?->nama_periode ?? 'Ganjil 2026/2027' }} · Aktif</span>
                </div>

                {{-- Tanggal --}}
                <div class="koor-header-date">
                    {{ now()->translatedFormat('d F Y') }}
                </div>

                {{-- Ikon Notifikasi --}}
                <a href="#" class="koor-header-notif" aria-label="Notifikasi" title="Notifikasi">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span class="koor-header-notif__badge"></span>
                </a>
            </div>

        </header>

        {{-- Page Main Content --}}
        <main class="sim-content" style="flex: 1; padding: 0;">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer style="padding: 16px 32px; border-top: 1px solid #E8E2D8; display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: #A3978A;">
            <div>SIM-PBL · D4 Sistem Informasi Bisnis</div>
            <div>Data Terintegrasi · Semester {{ $sidebarPeriode?->nama_periode ?? 'Ganjil 2026/2027' }}</div>
        </footer>

    </div>

    @stack('scripts')

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('sim-sidebar--open');
            overlay.classList.toggle('sidebar-overlay--visible');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.remove('sim-sidebar--open');
            overlay.classList.remove('sidebar-overlay--visible');
        }
    </script>

</body>
</html>

