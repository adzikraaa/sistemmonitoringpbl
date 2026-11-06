@extends('layouts.koordinator')

@section('title', 'Dashboard Koordinator - SIM-PBL')
@section('breadcrumb', 'Dashboard')

@section('content')
<div style="padding: 32px;">
    {{-- Header Page --}}
    <div style="margin-bottom: 24px;">
        <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #A3978A; text-transform: uppercase;">
            RUANG KOORDINATOR · DASHBOARD
        </span>
        <h1 style="font-family: 'DM Serif Display', Georgia, serif; font-size: 28px; color: #2D2621; margin: 4px 0 6px;">
            Selamat Datang, {{ Auth::guard('koordinator')->user()->nama ?? 'Koordinator' }}
        </h1>
        <p style="font-size: 14px; color: #786F65; margin: 0;">
            Kelola administrasi akademik, periode semester, dan distribusi penugasan dosen pembimbing PBL.
        </p>
    </div>

    {{-- Akses Cepat --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-bottom: 28px;">
        <a href="{{ route('koordinator.penugasan-dosen.index') }}" style="text-decoration: none; display: block; background: #FFFFFF; border: 1.5px solid #CFC7BC; border-radius: 14px; padding: 22px; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #D6ECDE; color: #3B7454; display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <polyline points="16 11 18 13 22 9"/>
                    </svg>
                </div>
                <span style="font-size: 12px; font-weight: 600; color: #3B7454; background: #EAF4EE; padding: 4px 10px; border-radius: 20px;">Fitur Utama</span>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: #2D2621; margin: 0 0 4px;">Penugasan Dosen</h3>
            <p style="font-size: 13px; color: #786F65; margin: 0 0 14px; line-height: 1.5;">
                Distribusikan pembimbing dari Master Data ke kelas dan kelompok dengan kapasitas yang seimbang.
            </p>
            <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #B87C2B;">
                <span>Buka Penugasan Dosen</span>
                <span>→</span>
            </div>
        </a>
    </div>
</div>
@endsection
