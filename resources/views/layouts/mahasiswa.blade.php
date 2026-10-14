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
</head>
<body class="sim-body">
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>
    <aside id="sidebar" class="sim-sidebar">
        <div class="sim-sidebar__logo">
            <div class="sim-sidebar__logo-icon">S</div>
            <span class="sim-sidebar__logo-text">SIM-PBL</span>
        </div>
        <nav class="sim-sidebar__nav">
            <p class="sim-sidebar__nav-label">RUANG MAHASISWA</p>
            <ul class="sim-sidebar__menu">
                <li>
                    <a href="{{ route('mahasiswa.dashboard') }}" class="sim-sidebar__menu-item {{ request()->routeIs('mahasiswa.dashboard') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.kelompok') }}" class="sim-sidebar__menu-item {{ request()->routeIs('mahasiswa.kelompok') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Kelompok saya
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.proposal') }}" class="sim-sidebar__menu-item {{ request()->routeIs('mahasiswa.proposal') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        Proposal
                        @if(request()->routeIs('mahasiswa.proposal'))
                            <span class="sim-sidebar__menu-dot"></span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.logbook') }}" class="sim-sidebar__menu-item {{ request()->routeIs('mahasiswa.logbook') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        Logbook
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.milestone') }}" class="sim-sidebar__menu-item {{ request()->routeIs('mahasiswa.milestone') ? 'sim-sidebar__menu-item--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Milestone
                    </a>
                </li>
            </ul>
        </nav>
        <div class="sim-sidebar__footer">
            <a href="#" class="sim-sidebar__logout">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Keluar
            </a>
        </div>
    </aside>
    <div class="sim-main">
        <header class="sim-header">
            <button class="sim-header__hamburger" onclick="toggleSidebar()" aria-label="Toggle menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="sim-header__breadcrumb">
                <span class="sim-header__breadcrumb-root">SIM-PBL</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sim-header__breadcrumb-sep"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="sim-header__breadcrumb-current">Ruang mahasiswa</span>
            </div>
            <div class="sim-header__user">
                <div class="sim-header__notif">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </div>
                <div class="sim-header__user-avatar">{{ strtoupper(substr($userName ?? 'MT', 0, 2)) }}</div>
                <div class="sim-header__user-info">
                    <span class="sim-header__user-name">{{ $userName ?? 'Mahasiswa' }}</span>
                    <span class="sim-header__user-role">{{ $userProdi ?? 'Teknologi Informasi' }}</span>
                </div>
            </div>
        </header>
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