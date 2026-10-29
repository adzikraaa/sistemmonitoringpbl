<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Ruang Dosen - SIM-PBL')</title>
    <meta name="description" content="@yield('meta_description', 'Sistem Informasi Monitoring Project Based Learning')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        .sim-header__breadcrumb {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            min-width: 0;
        }

        .sim-header__breadcrumb-root,
        .sim-header__breadcrumb-link {
            color: #786F65;
            font-weight: 400;
            white-space: nowrap;
        }

        .sim-header__breadcrumb-current {
            color: #B97817;
            font-weight: 400;
            white-space: nowrap;
        }

        .sim-header__breadcrumb-sep {
            color: #B5AA9C;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .sim-header__breadcrumb {
                gap: 6px;
                font-size: 12px;
            }
        }
    </style>
</head>

<body class="sim-body">

    {{-- Sidebar Overlay --}}
    <div id="sidebar-overlay"
         class="sidebar-overlay"
         onclick="closeSidebar()">
    </div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="sim-sidebar">

        <div class="sim-sidebar__logo">
            <div class="sim-sidebar__logo-icon">S</div>
            <span class="sim-sidebar__logo-text">SIM-PBL</span>
        </div>

        <nav class="sim-sidebar__nav">
            <p class="sim-sidebar__nav-label">RUANG DOSEN</p>

            <ul class="sim-sidebar__menu">

                {{-- Dashboard --}}
                <li>
                    <a href="{{ route('dosen.dashboard') }}"
                       class="sim-sidebar__menu-item {{ request()->routeIs('dosen.dashboard') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        Dashboard
                    </a>
                </li>

                {{-- Pilih Kelas (dari fitur tambah_kelas_dosen) --}}
                <li>
                    <a href="{{ route('dosen.pilih-kelas') }}"
                       class="sim-sidebar__menu-item {{ request()->routeIs('dosen.pilih-kelas') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v15H6.5A2.5 2.5 0 0 0 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            <path d="M8 7h8"/>
                            <path d="M8 11h8"/>
                        </svg>
                        Pilih Kelas
                    </a>
                </li>

                {{-- Kelompok — route dosen.kelompok.index (KelompokController@index) --}}
                <li>
                    <a href="{{ route('dosen.kelompok.index') }}"
                       class="sim-sidebar__menu-item {{ request()->routeIs('dosen.kelompok*') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Kelompok
                    </a>
                </li>

                {{-- Pilih Semester (dari fitur pilih-semester) --}}
                <li>
                    <a href="{{ route('dosen.pilih-semester') }}"
                       class="sim-sidebar__menu-item {{ request()->routeIs('dosen.pilih-semester') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Pilih Semester
                    </a>
                </li>

                {{-- Laporan Aktivitas --}}
                <li>
                    <a href="{{ route('dosen.laporan-aktivitas') }}"
                       class="sim-sidebar__menu-item {{ request()->routeIs('dosen.laporan-aktivitas') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        Laporan Aktivitas
                    </a>
                </li>

                {{-- Proposal (placeholder dari teman, href="#") --}}
                <li>
                    <a href="#" class="sim-sidebar__menu-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                        Proposal
                    </a>
                </li>

                {{-- Review (placeholder dari teman, href="#") --}}
                <li>
                    <a href="#" class="sim-sidebar__menu-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Review
                    </a>
                </li>

            </ul>
        </nav>

        {{-- Sidebar Footer --}}
        <div class="sim-sidebar__footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sim-sidebar__logout"
                    style="background: none; border: none; width: 100%; cursor: pointer; display: flex; align-items: center; gap: 10px; padding: 0; font-size: inherit; font-family: inherit;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>

    </aside>

    {{-- Main Content --}}
    <div class="sim-main">

        {{-- Header --}}
        <header class="sim-header">

            <button class="sim-header__hamburger"
                    onclick="toggleSidebar()"
                    aria-label="Toggle menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            {{-- Breadcrumb --}}
            <div class="sim-header__breadcrumb">

                <span class="sim-header__breadcrumb-root">SIM-PBL</span>

                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sim-header__breadcrumb-sep">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>

                <span class="sim-header__breadcrumb-link">Ruang Dosen</span>

                @hasSection('breadcrumb')
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sim-header__breadcrumb-sep">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                    <span class="sim-header__breadcrumb-current">
                        @yield('breadcrumb')
                    </span>
                @endif

            </div>

            {{-- User Info --}}
            <div class="sim-header__user">

                <div class="sim-header__notif">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>

                <div class="sim-header__user-avatar">
                    {{ strtoupper(substr($userName ?? auth('dosen')->user()?->nama ?? 'DS', 0, 2)) }}
                </div>

                <div class="sim-header__user-info">
                    <span class="sim-header__user-name">
                        {{ $userName ?? auth('dosen')->user()?->nama ?? 'Dosen' }}
                    </span>
                    <span class="sim-header__user-role">
                        {{ $userRole ?? 'Dosen Pengampu' }}
                    </span>
                </div>

            </div>
        </header>

        {{-- Page Content --}}
        <main class="sim-content">
            @yield('content')
        </main>

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
