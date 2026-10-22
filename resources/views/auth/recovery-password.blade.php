<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemulihan Password — SIM-PBL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex">
    <div class="hidden lg:block lg:w-[35%] bg-blue-900"></div>

    <div class="w-full lg:w-[65%] bg-[#FDFBF0] flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            <h1 class="text-3xl font-bold text-blue-900 mb-1">Pemulihan Password</h1>
            <p class="text-blue-900 text-sm mb-8">SIM-PBL — Sistem Monitoring PBL</p>

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.request.submit') }}">
                @csrf
                <div class="mb-4">
                    <label for="nim" class="block text-sm font-medium text-blue-900 mb-1">NIM</label>
                    <input type="text" id="nim" name="nim" value="{{ old('nim') }}" placeholder="Masukkan NIM Anda" autocomplete="off" required class="w-full px-5 py-3 rounded-full bg-[#F5F0E0] border-0 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
                <div class="mb-4">
                    <label for="tanggal_lahir" class="block text-sm font-medium text-blue-900 mb-1">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required class="w-full px-5 py-3 rounded-full bg-[#F5F0E0] border-0 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
                <div class="mb-8">
                    <label for="email" class="block text-sm font-medium text-blue-900 mb-1">Email Pemulihan</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="email@student.polinema.ac.id" autocomplete="off" required class="w-full px-5 py-3 rounded-full bg-[#F5F0E0] border-0 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
                <button type="submit" class="w-full bg-blue-900 text-white py-3 px-6 rounded-full text-sm font-medium hover:bg-blue-800 transition mb-4">Proses</button>
            </form>

            <p class="text-center text-sm text-blue-900">
                klik <a href="{{ route('login') }}" class="text-blue-600 hover:underline">disini</a> untuk kembali ke Halaman Login
            </p>
        </div>
    </div>
</body>
</html>
