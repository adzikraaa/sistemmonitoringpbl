@extends('layouts.dosen')

@section('title', 'Dashboard Dosen Pengampu')
@section('breadcrumb_page', 'Dashboard')

@section('content')
<div style="padding: 2rem;">

    {{-- Semester aktif badge --}}
    @if(isset($semester))
    <div style="display:inline-flex;align-items:center;gap:8px;background:#D6ECDE;color:#3B7454;border-radius:20px;padding:6px 16px;font-size:13px;font-weight:600;margin-bottom:1.5rem;border:1px solid #b2d8c0;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Semester Aktif: {{ $semester['nama'] }} &bull; {{ $semester['tahun'] }}
    </div>
    @endif

    {{-- Judul --}}
    <h1 style="font-family:'DM Serif Display',serif;font-size:2rem;color:#2D2621;margin:0 0 0.4rem;">
        Dashboard Dosen Pengampu
    </h1>
    <p style="color:#A3978A;margin:0 0 2rem;font-size:14px;">
        Halaman ini sedang dalam pengembangan dan akan diisi fitur oleh tim.
    </p>

    {{-- Aksi cepat --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('dosen.pilih-semester') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;background:#fff;border:1.5px solid #CFC7BC;border-radius:10px;color:#2D2621;font-size:13.5px;font-weight:600;text-decoration:none;transition:border-color 0.15s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Ganti Semester
        </a>
    </div>

</div>
@endsection
