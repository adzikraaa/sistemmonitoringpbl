@extends('layouts.dosen')

@section('breadcrumb')
    Detail Kelompok
@endsection

@section('title', 'Detail Kelompok')

@section('content')
<div style="padding: 24px 32px 40px;">
    <!-- Breadcrumb & Back -->
    <div class="mb-5">
        <a href="{{ route('dosen.kelompok.index') }}" class="text-xs font-semibold text-[#7A5C4A] hover:text-[#2B221D] inline-flex items-center gap-1.5 transition-colors">
            &larr; Kembali ke kelompok
        </a>
    </div>

    @php
        $statusBadge = match($kelompok->status ?? 'Berjalan') {
            'Review'    => 'bg-[#FFF7ED] text-[#C2410C] border-[#FED7AA]',
            'Terlambat' => 'bg-[#FDF2F2] text-[#B91C1C] border-[#FECACA]',
            default     => 'bg-[#EAF3EC] text-[#2F5C4A] border-[#C6DFC8]',
        };
    @endphp

    <!-- Card Header Kelompok -->
    <div class="bg-white border border-[#E4DED5] rounded-2xl p-6 mb-6 shadow-2xs">
        <span class="text-[10px] font-bold text-[#8C7A6B] uppercase tracking-widest block">
            DETAIL KELOMPOK &bull; {{ $kelompok->kelas->nama_kelas ?? $kelompok->kelas ?? 'SIB 3B' }}
        </span>
        <h1 class="text-2xl sm:text-3xl font-serif font-bold text-[#1F1F1F] mt-1 mb-1">
            {{ $kelompok->nama_kelompok ?? 'Kelompok 2' }} - {{ $kelompok->judul_proyek ?? 'Pengembangan Sistem Monitoring PBL' }}
        </h1>
        <p class="text-xs text-[#7D746D] mb-4">Semester Ganjil 2026/2027</p>

        <span class="inline-block border text-[11px] font-semibold px-3 py-1 rounded-md mb-6 {{ $statusBadge }}">
            {{ $kelompok->status ?? 'Berjalan' }}
        </span>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-5 border-t border-[#EFEAE2] text-xs">
            <div>
                <span class="text-[#8C7A6B] block mb-1">Dosen Pengampu</span>
                <span class="font-bold text-[#1F1F1F]">{{ Auth::guard('dosen')->user()?->nama ?? 'Dian Subhi, S.Kom., M.Kom.' }}</span>
            </div>
            <div>
                <span class="text-[#8C7A6B] block mb-1">Anggota kelompok</span>
                <span class="font-bold text-[#1F1F1F]">{{ $kelompok->anggotaKelompok->count() }} mahasiswa</span>
            </div>
            <div>
                <span class="text-[#8C7A6B] block mb-1">Logbook tercatat</span>
                <span class="font-bold text-[#1F1F1F]">{{ $kelompok->logbookMilestone->count() }} entri</span>
            </div>
            <div>
                <span class="text-[#8C7A6B] block mb-1">Aktivitas terakhir</span>
                <span class="font-bold text-[#1F1F1F]">{{ $kelompok->updated_at ? $kelompok->updated_at->format('d M Y') : '24 Okt 2026' }}</span>
            </div>
        </div>
    </div>

    <!-- Grid Konten Detail -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <!-- Card Anggota Kelompok -->
        <div class="bg-white border border-[#E4DED5] rounded-2xl p-6 shadow-2xs">
            <h2 class="font-bold text-[#1F1F1F] text-base mb-0.5">Anggota kelompok</h2>
            <p class="text-xs text-[#7D746D] mb-5">Daftar anggota yang terdaftar di sistem.</p>

            <div class="divide-y divide-[#EFEAE2]">
                @forelse($kelompok->anggotaKelompok as $anggota)
                    @php
                        $namaMhs = $anggota->mahasiswa->nama ?? 'Nama Mahasiswa';
                        $parts = explode(' ', trim($namaMhs));
                        $inisial = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : substr($parts[0], 1, 1)));
                    @endphp
                    <div class="flex items-center gap-3.5 py-3.5 first:pt-0 last:pb-0">
                        <div class="w-9 h-9 rounded-full bg-[#EAF3EC] text-[#2F5C4A] flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ $inisial }}
                        </div>
                        <div>
                            <h4 class="font-bold text-[#1F1F1F] text-xs">{{ $namaMhs }}</h4>
                            <span class="text-[11px] text-[#7D746D]">Mahasiswa &bull; {{ $anggota->mahasiswa->nim ?? 'SIB 3B' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-[#7D746D]">Belum ada anggota terdaftar.</p>
                @endforelse
            </div>
        </div>

        <!-- Card Ringkasan Administrasi & Aktivitas -->
        <div class="bg-white border border-[#E4DED5] rounded-2xl p-6 shadow-2xs">
            <h2 class="font-bold text-[#1F1F1F] text-base mb-0.5">Ringkasan administrasi &amp; aktivitas</h2>
            <p class="text-xs text-[#7D746D] mb-5">Status berdasarkan data yang tercatat.</p>

            <div class="space-y-4 text-xs mb-6">
                <div class="flex items-start justify-between gap-3 pb-4 border-b border-[#EFEAE2]">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF3EC] text-[#2F5C4A] flex items-center justify-center font-bold flex-shrink-0">✓</div>
                        <div>
                            <h4 class="font-bold text-[#1F1F1F]">Proposal proyek</h4>
                            <p class="text-[#7D746D] text-[11px] mt-0.5">Proposal telah dikirim dan tercatat pada sistem.</p>
                        </div>
                    </div>
                    <span class="bg-[#EAF3EC] text-[#2F5C4A] px-2.5 py-1 rounded-md text-[11px] font-semibold flex-shrink-0">Terkirim</span>
                </div>

                <div class="flex items-start justify-between gap-3 pb-4 border-b border-[#EFEAE2]">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF3EC] text-[#2F5C4A] flex items-center justify-center font-bold flex-shrink-0">&#9711;</div>
                        <div>
                            <h4 class="font-bold text-[#1F1F1F]">Logbook aktivitas</h4>
                            <p class="text-[#7D746D] text-[11px] mt-0.5">{{ $kelompok->logbookMilestone->count() }} catatan aktivitas tersedia.</p>
                        </div>
                    </div>
                    <span class="bg-[#EAF3EC] text-[#2F5C4A] px-2.5 py-1 rounded-md text-[11px] font-semibold flex-shrink-0">Tersedia</span>
                </div>

                <div class="flex items-start justify-between gap-3">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#FFF7ED] text-[#C2410C] flex items-center justify-center font-bold flex-shrink-0">&#8599;</div>
                        <div>
                            <h4 class="font-bold text-[#1F1F1F]">Bukti pekerjaan</h4>
                            <p class="text-[#7D746D] text-[11px] mt-0.5">Tautan dan berkas pendukung dapat diperiksa pada detail aktivitas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ route('dosen.laporan-aktivitas') }}"
            class="block w-full text-center py-2.5 bg-white border border-[#CFC7BC] hover:bg-[#FBF9F5] text-[#1F1F1F] text-xs font-semibold rounded-xl transition-colors">
                Buka Laporan Aktivitas
            </a>
        </div>
    </div>
</div>
@endsection