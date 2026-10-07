<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIM-PBL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex">
    <div class="hidden lg:block lg:w-[35%] bg-blue-900"></div>

    <div class="w-full lg:w-[65%] bg-[#FDFBF0] flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md" x-data="{
            role: '{{ old('role', 'mahasiswa') }}',
            showPassword: false,
            buttonText() {
                const labels = { mahasiswa: 'Mahasiswa', dosen: 'Dosen', koordinator: 'Koordinator' };
                return 'Masuk Sebagai ' + labels[this.role] + ' \u2192';
            }
        }">
            <h1 class="text-3xl font-bold text-blue-900 mb-1">Masuk Ke SIM-PBL</h1>
            <p class="text-blue-900 text-sm mb-8">Gunakan Akun Kampus Untuk Melanjutkan</p>

            <div class="bg-slate-500/40 rounded-full p-1 flex mb-8">
                <button type="button" @click="role = 'mahasiswa'"
                    :class="role === 'mahasiswa' ? 'bg-white text-blue-900 shadow' : 'text-white/70'"
                    class="flex-1 py-2 text-sm font-medium rounded-full transition">Mahasiswa</button>
                <button type="button" @click="role = 'dosen'"
                    :class="role === 'dosen' ? 'bg-white text-blue-900 shadow' : 'text-white/70'"
                    class="flex-1 py-2 text-sm font-medium rounded-full transition">Dosen</button>
                <button type="button" @click="role = 'koordinator'"
                    :class="role === 'koordinator' ? 'bg-white text-blue-900 shadow' : 'text-white/70'"
                    class="flex-1 py-2 text-sm font-medium rounded-full transition">Koordinator</button>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="role" :value="role">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-blue-900 mb-1" x-text="role === 'mahasiswa' ? 'NIM' : 'NIDN'"></label>
                        <input
                        type="text"
                        name="identifier"
                        value="{{ old('identifier') }}"
                        :placeholder="role === 'mahasiswa' ? 'ketikkan NIM di sini' : 'ketikkan NIDN di sini'"
                        class="w-full px-5 py-3 rounded-full bg-[#F5F0E0] border-0 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-blue-900 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                            placeholder="ketikkan sandi di sini"
                            class="w-full px-5 py-3 rounded-full bg-[#F5F0E0] border-0 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900 pr-16">
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-sm text-blue-900/60 hover:text-blue-900"
                            x-text="showPassword ? 'Tutup' : 'Lihat'"></button>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-8 text-sm">
                    <label class="flex items-center gap-2 text-blue-900">
                        <input type="checkbox" class="rounded border-gray-300">
                        Ingat Perangkat Ini
                    </label>
                    <a href="#" class="text-blue-900 hover:underline">Lupa kata sandi?</a>
                </div>

                <button type="submit"
                    class="w-full bg-blue-900 text-white py-3 px-6 rounded-full text-sm font-medium hover:bg-blue-800 transition"
                    x-text="buttonText()"></button>
            </form>
        </div>
    </div>
</body>
</html>
