@extends('layouts.dosen')

@section('title', 'Kelompok Bimbingan')
@section('meta_description', 'Daftar kelompok bimbingan berdasarkan kelas aktif dosen.')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <span class="text-xs font-semibold tracking-wider text-gray-400 uppercase">RUANG DOSEN • KELOMPOK</span>
        <div class="flex items-center justify-between mt-1 gap-4 flex-wrap">
            <h1 class="text-3xl font-serif font-bold text-gray-900">{{ $kelas['nama'] }}</h1>
            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-full border border-gray-200">
                • {{ count($kelompok) }} Kelompok Terdaftar
            </span>
        </div>
        <p class="text-sm text-gray-500 mt-1">Pantau perkembangan proyek dari kelompok yang kamu dampingi pada {{ $kelas['semester'] }}.</p>
    </div>

    <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div class="text-sm text-gray-600">
            <span class="font-medium text-gray-700">Kelas aktif:</span> {{ $kelas['nama'] }} • {{ $kelas['prodi'] }}
        </div>
        <a href="{{ route('dosen.pilih-kelas') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-200 bg-white text-sm font-medium text-gray-700 rounded-lg hover:border-emerald-500 hover:text-emerald-700 transition-colors">
            Ganti Kelas
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @forelse ($kelompok as $item)
            @php
                $statusClass = match($item['status'] ?? 'Aktif') {
                    'Review' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'Terlambat' => 'bg-rose-50 text-rose-600 border-rose-200',
                    default => 'bg-emerald-50 text-emerald-600 border-emerald-200'
                };
            @endphp

            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            👥
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base leading-snug">{{ $item['nama'] }}</h3>
                            <p class="text-xs text-gray-500">{{ $item['proyek'] ?? 'Proyek PBL' }}</p>
                        </div>
                    </div>

                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full border {{ $statusClass }}">
                        {{ $item['status'] ?? 'Aktif' }}
                    </span>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 grid grid-cols-3 gap-2 text-xs mb-4">
                    <div>
                        <span class="text-gray-400 block mb-0.5">Anggota</span>
                        <span class="font-bold text-gray-800">{{ $item['jumlah_anggota'] ?? 5 }} mahasiswa</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Ketua</span>
                        <span class="font-bold text-gray-800">{{ $item['ketua'] ?? 'Belum ada' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-0.5">Aktivitas</span>
                        <span class="font-bold text-gray-800">{{ $item['status'] ?? 'Aktif' }}</span>
                    </div>
                </div>

                <div class="mb-5">
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-400">Progress kelompok</span>
                        <span class="font-bold text-gray-700">{{ $item['progress'] ?? 60 }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-emerald-700 h-1.5 rounded-full" style="width: {{ $item['progress'] ?? 60 }}%"></div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" class="inline-flex items-center justify-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-medium rounded-lg transition-colors">
                        Lihat detail →
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-500 text-sm">Tidak ada kelompok bimbingan yang ditemukan pada kelas {{ $kelas['nama'] }}.</p>
            </div>
        @endforelse
    </div>

    <div class="bg-neutral-800 text-white p-4 rounded-xl flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-neutral-700 flex items-center justify-center text-amber-400 font-bold">
                ⚠️
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold block">CATATAN BIMBINGAN AKTIF</span>
                <p class="text-xs text-gray-200 mt-0.5">Sinyal perhatian: kelompok dengan status review perlu penyesuaian catatan mingguan.</p>
            </div>
        </div>
        <button type="button" class="bg-amber-100 hover:bg-amber-200 text-neutral-900 text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
            Kirim pengingat →
        </button>
    </div>
</div>
@endsection
