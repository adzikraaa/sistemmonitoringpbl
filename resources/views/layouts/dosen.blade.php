<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ruang Dosen - SIM-PBL')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-gray-800 antialiased flex min-h-screen">

    <!-- Sidebar Ruang Dosen -->
    <aside class="w-64 bg-neutral-900 text-white min-h-screen p-4 flex flex-col justify-between">
        <div>
            <!-- Logo Brand -->
            <div class="flex items-center gap-3 px-2 py-3 mb-6">
                <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center font-serif font-bold text-white text-sm">S</div>
                <div>
                    <h2 class="font-serif font-bold text-sm tracking-wide">SIM-PBL</h2>
                    <span class="text-[10px] text-gray-400 block tracking-widest uppercase">RUANG DOSEN</span>
                </div>
            </div>

            <!-- Navigasi Menu -->
            <nav class="space-y-1 text-xs">
                <a href="{{ route('dosen.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-400 hover:text-white hover:bg-neutral-800 transition-colors">
                    📊 <span>Dashboard</span>
                </a>
                <a href="{{ route('dosen.kelompok.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-neutral-800 text-white font-medium">
                    👥 <span>Kelompok</span>
                </a>
            </nav>
        </div>

        <div class="px-2 py-3 border-t border-neutral-800 text-xs text-gray-500">
            © 2026 SIM-PBL
        </div>
    </aside>

    <!-- Content Area -->
    <main class="flex-1 overflow-y-auto">
        @yield('content')
    </main>

</body>
</html>