<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pilih Semester') -- SIM-PBL</title>
    <meta name="description" content="@yield('meta_description', 'Pilih Semester & Proyek PBL - SIM-PBL')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="sim-body">
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

    {{-- Sidebar: minimal, hanya label Pilih Semester --}}
    <aside id="sidebar" class="sim-sidebar">
        <div class="sim-sidebar__logo">
            <div class="sim-sidebar__logo-icon">S</div>
            <span class="sim-sidebar__logo-text">SIM-PBL</span>
        </div>
        <nav class="sim-sidebar__nav">
            <p class="sim-sidebar__nav-label">RUANG MAHASISWA</p>
            <ul class="sim-sidebar__menu">
                <li>
                    <a href="{{ route('mahasiswa.pilih-semester') }}" class="sim-sidebar__menu-item sim-sidebar__menu-item--active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Pilih Semester
                        <span class="sim-sidebar__menu-dot"></span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="sim-sidebar__footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sim-sidebar__logout" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="sim-main">
        {{-- Header --}}
        <header class="sim-header">
            <button class="sim-header__hamburger" onclick="toggleSidebar()" aria-label="Toggle menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="sim-header__breadcrumb">
                <span class="sim-header__breadcrumb-root">SIM-PBL</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sim-header__breadcrumb-sep"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="sim-header__breadcrumb-current">Ruang Mahasiswa</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sim-header__breadcrumb-sep"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="sim-header__breadcrumb-current" style="color: var(--sim-brown-warm);">Pilih Semester</span>
            </div>
            <div class="sim-header__user">
                <a href="{{ route('mahasiswa.notifikasi') }}" class="sim-header__notif" title="Notifikasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <span class="sim-header__notif-dot"></span>
                </a>
                <div class="sim-header__user-avatar">{{ strtoupper(substr($userName ?? 'MT', 0, 2)) }}</div>
                <div class="sim-header__user-info">
                    <span class="sim-header__user-name">{{ $userName ?? 'Mahasiswa' }}</span>
                    <span class="sim-header__user-role">{{ $userNim ?? '244107060105' }} · {{ $userKelas ?? 'SIB 3B' }}</span>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="sim-content sim-ps-main-content">
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
