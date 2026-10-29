<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIM-PBL') -- SIM-PBL</title>
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

                        <svg width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>

                        Dashboard
                    </a>
                </li>

                {{-- Kelas --}}
                <li>
                    <a href="#"
                       class="sim-sidebar__menu-item {{ request()->routeIs('dosen.kelas*') ? 'sim-sidebar__menu-item--active' : '' }}">

                        <svg width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <line x1="3" y1="9" x2="21" y2="9"/>
                            <line x1="3" y1="15" x2="21" y2="15"/>
                            <line x1="9" y1="9" x2="9" y2="21"/>
                        </svg>

                        Kelas
                    </a>
                </li>

                {{-- Kelompok --}}
                <li>
                    <a href="#"
                       class="sim-sidebar__menu-item {{ request()->routeIs('dosen.kelompok*') ? 'sim-sidebar__menu-item--active' : '' }}">

                        <svg width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>

                        Kelompok
                    </a>
                </li>

                {{-- Laporan Aktivitas --}}
                <li>
                    <a href="{{ route('dosen.laporan-aktivitas') }}"
                       class="sim-sidebar__menu-item {{ request()->routeIs('dosen.laporan-aktivitas') ? 'sim-sidebar__menu-item--active' : '' }}">

                        <svg width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round"
                             stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>

                        Laporan Aktivitas
                    </a>
                </li>

                {{-- Notifikasi --}}
                <li>
                    <a href="#"
                       class="sim-sidebar__menu-item {{ request()->routeIs('dosen.notifikasi*') ? 'sim-sidebar__menu-item--active' : '' }}"
                       style="display: flex; justify-content: space-between; align-items: center;">

                        <span style="display: flex; align-items: center; gap: 10px;">
                            <svg width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor"
                                 stroke-width="1.75" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                            Notifikasi
                        </span>

                        <span style="background: #DC2626; color: #fff; font-size: 10px; font-weight: 700; border-radius: 10px; padding: 1px 7px; min-width: 20px; text-align: center;">3</span>
                    </a>
                </li>

            </ul>
        </nav>

        {{-- Sidebar Footer --}}
        <div class="sim-sidebar__footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sim-sidebar__logout" style="background: none; border: none; width: 100%; cursor: pointer; display: flex; align-items: center; gap: 10px; padding: 0; font-size: inherit; font-family: inherit;">

                    <svg width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.75" stroke-linecap="round"
                         stroke-linejoin="round">
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

                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            {{-- Breadcrumb --}}
            <div class="sim-header__breadcrumb">

                <span class="sim-header__breadcrumb-root">
                    SIM-PBL
                </span>

                <svg width="14" height="14" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round"
                     class="sim-header__breadcrumb-sep">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>

                <span class="sim-header__breadcrumb-link">
                    Ruang Dosen
                </span>

                @hasSection('breadcrumb')
                    <svg width="14" height="14" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round"
                         class="sim-header__breadcrumb-sep">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>

                    <span class="sim-header__breadcrumb-current">
                        @yield('breadcrumb')
                    </span>
                @endif

            </div>

            {{-- User Information --}}
            <div class="sim-header__user">

                <div class="sim-header__notif">
                    <svg width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.75" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>

                <div class="sim-header__user-avatar">
                    {{ strtoupper(substr(Auth::guard('dosen')->user()->nama ?? 'D', 0, 2)) }}
                </div>

                <div class="sim-header__user-info">
                    <span class="sim-header__user-name">
                        {{ Auth::guard('dosen')->user()->nama ?? 'Dosen' }}
                    </span>
                    <span class="sim-header__user-role">
                        Dosen Pembimbing
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

    {{-- Sidebar Script --}}
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
