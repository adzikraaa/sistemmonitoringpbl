@extends('layouts.dosen')

@section('title', 'Dashboard Dosen Pengampu')
@section('breadcrumb', 'Dashboard')

@section('content')

{{-- ── Semester aktif badge ─────────────────────────────────────────────── --}}
<div class="sim-page-header" style="padding-top: 28px; padding-bottom: 12px;">
    <div class="sim-page-header__left" style="flex:1; min-width:0;">
        <p class="sim-page-header__eyebrow">RUANG DOSEN · DASHBOARD</p>
        <h1 class="sim-page-header__title">Dashboard Dosen</h1>
        <p class="sim-page-header__desc">
            Pantau kegiatan kelas aktif, kelompok bimbingan, dan status review pada semester berjalan.
        </p>
    </div>

    {{-- Badge semester aktif (dari fitur Pilih Semester) --}}
    @if (isset($semester))
        <div class="sim-page-header__right">
            <div style="display:inline-flex;align-items:center;gap:8px;background:#D6ECDE;color:#3B7454;border-radius:20px;padding:6px 16px;font-size:13px;font-weight:600;border:1px solid #b2d8c0;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ $semester['nama'] }} &bull; {{ $semester['tahun'] }}
            </div>
        </div>
    @endif
</div>

{{-- ── Banner kelas aktif (dari fitur tambah_kelas_dosen) ─────────────────── --}}
<div class="sim-proposal-card" style="margin: 0 32px 24px;">
    @if ($kelas)
        <div class="sim-dosen-banner" style="margin: 0; padding: 20px 18px;">
            <div>
                <div class="sim-dosen-banner__title">Kelas aktif</div>
                <div class="sim-dosen-banner__meta">
                    <strong>{{ $kelas['nama'] }}</strong> · {{ $kelas['semester'] }} · {{ $kelas['prodi'] }}
                </div>
            </div>
            {{-- Arahkan ke KelompokController (dosen.kelompok.index) bukan DashboardController::kelompok() --}}
            <a href="{{ route('dosen.kelompok.index') }}" class="sim-dosen-banner__button">Ke Kelompok</a>
        </div>
    @else
        <div class="sim-catatan" style="background: #FFF9E6; border-color: #F0E0A0; color: #7C5D00; margin: 0;">
            Belum ada kelas yang dipilih. Silakan pilih kelas untuk melanjutkan.
        </div>
    @endif

    {{-- Tombol aksi cepat: Pilih Kelas, Lihat Kelompok, Ganti Semester --}}
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:20px;">
        <a href="{{ route('dosen.pilih-kelas') }}" class="sim-btn-primary">Pilih Kelas</a>
        <a href="{{ route('dosen.kelompok.index') }}" class="sim-btn-secondary">Lihat Kelompok</a>
        <a href="{{ route('dosen.pilih-semester') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:#fff;border:1.5px solid #CFC7BC;border-radius:10px;color:#2D2621;font-size:13.5px;font-weight:600;text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Ganti Semester
        </a>
    </div>
</div>

@endsection
