@extends('layouts.dosen')

@section('title', 'Dashboard Dosen')
@section('meta_description', 'Dashboard dosen dan kelas aktif SIM-PBL.')

@section('content')
<div class="sim-page-header" style="padding-top: 28px; padding-bottom: 18px;">
    <div class="sim-page-header__left" style="flex:1; min-width:0;">
        <p class="sim-page-header__eyebrow">RUANG DOSEN · DASHBOARD</p>
        <h1 class="sim-page-header__title">Dashboard Dosen</h1>
        <p class="sim-page-header__desc">
            Pantau kegiatan kelas aktif, kelompok bimbingan, dan status review pada semester berjalan.
        </p>
    </div>
</div>

<div class="sim-proposal-card" style="margin: 0 32px 24px;">
    @if ($kelas)
        <div class="sim-dosen-banner" style="margin: 0; padding: 20px 18px;">
            <div>
                <div class="sim-dosen-banner__title">Kelas aktif</div>
                <div class="sim-dosen-banner__meta">
                    <strong>{{ $kelas['nama'] }}</strong> · {{ $kelas['semester'] }} · {{ $kelas['prodi'] }}
                </div>
            </div>
            <a href="{{ route('dosen.kelompok') }}?class_id={{ $kelas['id'] }}" class="sim-dosen-banner__button">Ke Kelompok</a>
        </div>
    @else
        <div class="sim-catatan" style="background: #FFF9E6; border-color: #F0E0A0; color: #7C5D00; margin: 0;">
            Belum ada kelas yang dipilih. Silakan pilih kelas untuk melanjutkan.
        </div>
    @endif

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:20px;">
        <a href="{{ route('dosen.pilih-kelas') }}" class="sim-btn-primary">Pilih Kelas</a>
        <a href="{{ route('dosen.kelompok') }}" class="sim-btn-secondary">Lihat Kelompok</a>
    </div>
</div>
@endsection
