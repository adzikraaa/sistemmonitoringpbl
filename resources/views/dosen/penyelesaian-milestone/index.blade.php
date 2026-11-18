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
                            $row = $statusMap->get($key);
                            $status = $row?->status ?? 'belum_dimulai';
                        @endphp

                        <form method="POST"
                              action="{{ route('dosen.penyelesaian-milestone.update', [$kelompok, $milestone, $mataKuliah]) }}"
                              class="grid grid-cols-1 md:grid-cols-[1fr_150px_1fr_110px] gap-3 items-center py-3 border-t border-[#F0EAE2] first:border-t-0">
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
                                        class="w-full bg-white border border-[#CFC7BC] rounded-lg px-2.5 py-2 text-xs">
                                    @foreach ($statusLabels as $value => $label)
                                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="sr-only" for="catatan-{{ $key }}">Catatan</label>
                                <input id="catatan-{{ $key }}" type="text" name="catatan"
                                       value="{{ $row?->catatan }}"
                                       placeholder="Catatan (opsional)"
                                       class="w-full bg-white border border-[#CFC7BC] rounded-lg px-2.5 py-2 text-xs">
                            </div>

                            <button type="submit"
                                    class="px-3 py-2 bg-[#2F5C4A] hover:bg-[#234739] text-white text-xs font-semibold rounded-lg">
                                Simpan
                            </button>
                        </form>
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