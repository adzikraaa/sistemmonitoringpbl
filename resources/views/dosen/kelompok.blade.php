@extends('layouts.dosen')

@section('title', 'Kelompok Dosen')
@section('meta_description', 'Daftar kelompok bimbingan berdasarkan kelas aktif dosen.')

@section('content')
<div class="sim-page-header" style="padding-top: 28px; padding-bottom: 18px;">
    <div class="sim-page-header__left" style="flex:1; min-width:0;">
        <p class="sim-page-header__eyebrow">RUANG DOSEN · KELOMPOK</p>
        <h1 class="sim-page-header__title">{{ $kelas['nama'] }}</h1>
        <p class="sim-page-header__desc">
            Menampilkan daftar kelompok bimbingan untuk {{ $kelas['prodi'] }} pada {{ $kelas['semester'] }}.
        </p>
    </div>
    <div class="sim-page-header__right">
        <a href="{{ route('dosen.pilih-kelas') }}" class="sim-btn-secondary">Ganti Kelas</a>
    </div>
</div>

<div class="sim-group-bar" style="margin-top: 0;">
    <div class="sim-group-bar__left">
        <span class="sim-group-bar__name">{{ $kelas['nama'] }}</span>
        <span class="sim-group-bar__sep">•</span>
        <span class="sim-group-bar__members">{{ count($kelompok) }} kelompok terdaftar</span>
        <span class="sim-group-bar__sep">•</span>
        <span class="sim-group-bar__dosen">Semester aktif: <strong>{{ $kelas['semester'] }}</strong></span>
    </div>
</div>

<section class="sim-dosen-grid" style="padding-top: 20px; padding-bottom: 40px;">
    @forelse ($kelompok as $item)
        <article class="sim-class-card sim-class-card--selected" style="border-color: rgba(61, 35, 20, 0.8);">
            <span class="sim-class-card__tag sim-class-card__tag--active">{{ $item['status'] }}</span>

            <div class="sim-class-card__body">
                <p class="sim-class-card__label">Kelompok</p>
                <h2 class="sim-class-card__name" style="font-size: 26px;">{{ $item['nama'] }}</h2>

                <div class="sim-class-card__stats" style="margin-top:16px;">
                    <div class="sim-class-card__stats-head">
                        <span>Ketua</span>
                        <strong>{{ $item['ketua'] }}</strong>
                    </div>
                    <div class="sim-class-card__stats-head" style="margin-top: 8px;">
                        <span>Anggota</span>
                        <strong>{{ $item['jumlah_anggota'] }} Mahasiswa</strong>
                    </div>
                </div>
            </div>

            <button type="button" class="sim-class-card__button sim-class-card__button--selected">
                Lihat Detail
            </button>
        </article>
    @empty
        <div class="sim-proposal-card" style="grid-column: 1 / -1;">
            <p class="sim-proposal-card__title">Belum ada kelompok</p>
            <p class="sim-proposal-card__desc">Tidak ada kelompok yang terdaftar pada kelas {{ $kelas['nama'] }}.</p>
        </div>
    @endforelse
</section>
@endsection
