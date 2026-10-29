<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pilih Semester') -- SIM-PBL</title>
    <meta name="description" content="@yield('meta_description', 'Pilih Semester Dosen Pengampu - SIM-PBL')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="margin:0;padding:0;font-family:'Inter',ui-sans-serif,system-ui,sans-serif;background:#FAF8F4;color:#2D2621;min-height:100vh;display:flex;flex-direction:column;">

    {{-- ═══════════════════════════════════════════
         HEADER DOSEN
    ═══════════════════════════════════════════ --}}
    <header style="background:#fff;border-bottom:1px solid #CFC7BC;padding:0 32px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;gap:16px;">

        {{-- Kiri: Logo + Identitas Institusi --}}
        <div style="display:flex;align-items:center;gap:14px;flex-shrink:0;">
            <div style="width:36px;height:36px;background:#3B7454;border-radius:8px;display:flex;align-items:center;justify-content:center;font-family:'DM Serif Display',serif;font-size:18px;font-weight:400;color:#fff;letter-spacing:-0.5px;">S</div>
            <div>
                <div style="font-size:14px;font-weight:700;color:#2D2621;letter-spacing:0.01em;line-height:1.1;">SIM-PBL</div>
                <div style="font-size:10.5px;color:#A3978A;font-weight:500;letter-spacing:0.02em;line-height:1.1;">POLITEKNIK NEGERI MALANG</div>
            </div>
        </div>

        {{-- Kanan: Notif + Identitas Dosen --}}
        <div style="display:flex;align-items:center;gap:14px;flex-shrink:0;">
            {{-- Ikon Notifikasi --}}
            <div style="position:relative;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;cursor:pointer;color:#A3978A;transition:background 0.15s;" title="Notifikasi">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                {{-- Dot notifikasi (sembunyikan jika tidak ada notif) --}}
                <span style="position:absolute;top:6px;right:6px;width:7px;height:7px;background:#3B7454;border-radius:50%;border:1.5px solid #fff;"></span>
            </div>

            {{-- Avatar Inisial --}}
            <div style="width:36px;height:36px;background:#3B7454;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;letter-spacing:0.5px;">
                {{ strtoupper(substr($userName ?? 'DS', 0, 2)) }}
            </div>

            {{-- Nama & Role --}}
            <div style="line-height:1.25;">
                <div style="font-size:13.5px;font-weight:600;color:#2D2621;white-space:nowrap;max-width:200px;overflow:hidden;text-overflow:ellipsis;">{{ $userName ?? 'Dosen Pengampu' }}</div>
                <div style="font-size:11.5px;color:#A3978A;font-weight:500;">{{ $userRole ?? 'Dosen Pengampu' }}</div>
            </div>

            {{-- Tombol Keluar --}}
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" title="Keluar" style="width:34px;height:34px;border:1px solid #CFC7BC;border-radius:8px;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#A3978A;transition:all 0.15s;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </header>

    {{-- Content --}}
    <main style="flex:1;">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
