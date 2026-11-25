@extends('layouts.dosen')

@section('title', 'Penyelesaian Milestone')
@section('breadcrumb', 'Penyelesaian Milestone')

@section('content')
@php
    $statusLabels = [
        'belum_dimulai' => 'Belum dimulai',
        'berjalan' => 'Berjalan',
        'selesai' => 'Selesai',
    ];
@endphp

<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">RUANG DOSEN &bull; PENYELESAIAN MILESTONE</p>
        <h1 class="sim-page-header__title">Penyelesaian milestone</h1>
        <p class="sim-page-header__desc">
            Tetapkan status penyelesaian milestone untuk setiap mata kuliah yang Anda ampu.
        </p>
    </div>
</div>

<div style="padding: 0 32px 40px;">

    @if (session('success'))
        <div class="mb-5 bg-[#EAF3EC] border border-[#C6DFC8] text-[#2F5C4A] px-4 py-3 rounded-xl text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 bg-[#FDF2F2] border border-[#FECACA] text-[#B91C1C] px-4 py-3 rounded-xl text-xs">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @forelse ($pengampuList as $pengampu)
        @php
            $kelas = $pengampu->kelas;
            $mataKuliah = $pengampu->mataKuliah;
            $daftarKelompok = $kelompokByKelas->get($kelas->id, collect());
        @endphp

        <section class="bg-white border border-[#E4DED5] rounded-2xl p-6 mb-6 shadow-2xs">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <p class="text-[10px] font-bold text-[#8C7A6B] uppercase tracking-widest mb-1">
                        {{ $kelas->nama_kelas }}
                    </p>
                    <h2 class="text-xl font-serif font-bold text-[#1F1F1F]">
                        {{ $mataKuliah->nama_matkul }}
                    </h2>
                </div>
                <span class="text-[11px] font-semibold px-3 py-1 rounded-full bg-[#F5F1EB] text-[#6E5B3E]">
                    {{ $daftarKelompok->count() }} kelompok
                </span>
            </div>

            @forelse ($daftarKelompok as $kelompok)
                @php
                    $milestones = $milestoneByPeriode
                        ->get($kelompok->periode_id, collect())
                        ->filter(fn ($milestone) => $milestone->mataKuliah->contains('id', $mataKuliah->id));
                @endphp

                <div class="border border-[#EFEAE2] rounded-xl p-4 mb-4">
                    <h3 class="font-bold text-[#1F1F1F] text-sm mb-3">
                        {{ $kelompok->nama_kelompok }}
                        <span class="font-normal text-[#8C7A6B]">&bull; {{ $kelompok->periode?->nama_periode }}</span>
                    </h3>

                    @forelse ($milestones as $milestone)
                        @php
                            $key = $kelompok->id.':'.$milestone->id.':'.$mataKuliah->id;
                            $pengumpulanKey = $kelompok->id.':'.$milestone->id.':'.$mataKuliah->slug;
                            $pengumpulan = $pengumpulanMap->get($pengumpulanKey);
                            $row = $pengumpulan ? $statusMap->get($key) : null;
                            $status = $row?->status ?? 'belum_dimulai';
                        @endphp

                        <div class="py-3.5 border-t border-[#F0EAE2] first:border-t-0 space-y-2.5">
                            <form method="POST"
                                  action="{{ route('dosen.penyelesaian-milestone.update', [$kelompok, $milestone, $mataKuliah]) }}"
                                  class="grid grid-cols-1 md:grid-cols-[1fr_150px_1fr_110px] gap-3 items-center">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <p class="text-xs font-semibold text-[#1F1F1F]">{{ $milestone->judul }}</p>
                                    <p class="text-[11px] text-[#8C7A6B]">
                                        {{ $milestone->kode }} &bull;
                                        {{ $milestone->deadline ? $milestone->deadline->format('d M Y') : 'Tanpa tenggat' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="sr-only" for="status-{{ $key }}">Status</label>
                                    <select id="status-{{ $key }}" name="status"
                                            @disabled(! $pengumpulan)
                                            class="w-full bg-white border border-[#CFC7BC] rounded-lg px-2.5 py-2 text-xs disabled:bg-[#F5F1EB] disabled:text-[#9C9188] disabled:cursor-not-allowed">
                                        @foreach ($statusLabels as $value => $label)
                                            <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="sr-only" for="catatan-{{ $key }}">Catatan Dosen</label>
                                    <input id="catatan-{{ $key }}" type="text" name="catatan"
                                           value="{{ $row?->catatan }}"
                                           @disabled(! $pengumpulan)
                                           placeholder="Catatan dosen (opsional)"
                                           class="w-full bg-white border border-[#CFC7BC] rounded-lg px-2.5 py-2 text-xs disabled:bg-[#F5F1EB] disabled:text-[#9C9188] disabled:cursor-not-allowed">
                                </div>

                                <button type="submit"
                                        @disabled(! $pengumpulan)
                                        class="px-3 py-2 bg-[#2F5C4A] hover:bg-[#234739] text-white text-xs font-semibold rounded-lg disabled:bg-[#CFC7BC] disabled:cursor-not-allowed">
                                    Simpan
                                </button>
                            </form>

                            {{-- Student Submission Info (Pengumpulan Milestone) --}}
                            <div class="bg-[#FAF8F5] border border-[#EFEAE2] rounded-lg p-3 text-xs">
                                @if ($pengumpulan)
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                                        <div class="space-y-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-[#E8F0EC] text-[#2F5C4A]">
                                                    Laporan Terkumpul
                                                </span>
                                                <span class="font-medium text-[#1F1F1F]">{{ $pengumpulan->file_name ?? 'Berkas Laporan' }}</span>
                                                @if ($pengumpulan->created_at)
                                                    <span class="text-[#8C7A6B] text-[11px]">&bull; {{ $pengumpulan->created_at->format('d M Y H:i') }}</span>
                                                @endif
                                            </div>
                                            @if ($pengumpulan->catatan)
                                                <p class="text-[#594E42] text-[11px]">
                                                    <span class="font-medium text-[#1F1F1F]">Catatan Mahasiswa:</span> {{ $pengumpulan->catatan }}
                                                </p>
                                            @endif
                                            @if ($pengumpulan->link_bukti)
                                                <p class="text-[11px]">
                                                    <span class="font-medium text-[#1F1F1F]">Tautan Bukti:</span>
                                                    <a href="{{ $pengumpulan->link_bukti }}" target="_blank" rel="noopener noreferrer" class="text-[#2F5C4A] underline hover:text-[#234739]">
                                                        {{ $pengumpulan->link_bukti }}
                                                    </a>
                                                </p>
                                            @endif
                                        </div>

                                        @if ($pengumpulan->file_path)
                                            <div class="shrink-0 flex items-center gap-2">
                                                <a href="{{ route('dosen.laporan-aktivitas.milestone', [$kelompok, $milestone]) }}"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#FAF7F3] hover:bg-[#F0EBE1] border border-[#CFC7BC] text-[#2E6B42] rounded-lg font-semibold text-xs transition-colors shadow-2xs"
                                                   title="Buka halaman review milestone terpadu">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    Tinjau Terpadu
                                                </a>
                                                <a href="{{ route('dosen.penyelesaian-milestone.download', [$kelompok, $milestone, $mataKuliah]) }}"
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-[#F5F1EB] border border-[#CFC7BC] text-[#1F1F1F] rounded-lg font-medium text-xs transition-colors shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-[#594E42]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    Unduh Berkas
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 text-[#8C7A6B]">
                                        <svg class="w-4 h-4 text-[#CFC7BC] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Laporan belum dikumpulkan oleh kelompok mahasiswa untuk mata kuliah ini.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-[#8C7A6B]">Belum ada milestone yang dipetakan ke mata kuliah ini.</p>
                    @endforelse
                </div>
            @empty
                <p class="text-xs text-[#8C7A6B]">Belum ada kelompok pada kelas ini.</p>
            @endforelse
        </section>
    @empty
        <div class="bg-white border border-dashed border-[#CFC7BC] rounded-2xl p-10 text-center">
            <h2 class="font-serif text-lg font-bold text-[#1F1F1F] mb-1">Belum ada mata kuliah yang diampu</h2>
            <p class="text-xs text-[#8C7A6B]">Hubungi koordinator jika penugasan mata kuliah belum tersedia.</p>
        </div>
    @endforelse

</div>
@endsection
