@extends('layouts.mahasiswa')

@section('title', 'Kelompok')
@section('meta_description', 'Ruang kolaborasi kelompok mahasiswa SIM-PBL')

@section('content')

@php
    $anggota = [
        ['inisial' => 'AP', 'nama' => 'Adzikra', 'peran' => 'Ketua'],
        ['inisial' => 'BS', 'nama' => 'Tanggaq', 'peran' => 'Anggota'],
        ['inisial' => 'RM', 'nama' => 'Dina',    'peran' => 'Anggota'],
        ['inisial' => 'AS', 'nama' => 'Niky',    'peran' => 'Anggota'],
        ['inisial' => 'AS', 'nama' => 'Ayu',     'peran' => 'Anggota'],
    ];
@endphp

<div style="font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;">

{{-- Page Header Section --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">KELOMPOK SAYA</p>
        <h1 class="sim-page-header__title">Kelompok 01</h1>
        <p class="sim-page-header__desc">Ruang kolaborasi untuk menjaga proyek tetap bergerak.</p>
    </div>

    <div class="sim-page-header__right">
        <button class="sim-btn-secondary"
            style="border: 1px solid var(--sim-border); border-radius: 999px; padding: 10px 20px; background: #fff; gap: 8px; font-size: 13px; font-weight: 600; font-family: inherit;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            Pengaturan kelompok
        </button>
    </div>
</div>

{{-- Main Container --}}
<div style="padding: 0 32px 40px;">

    {{-- Banner Milestone Selanjutnya (lebar dibatasi, ada bayangan) --}}
    <div style="background: linear-gradient(135deg, #3A291F 0%, #34241A 100%); border-radius: 24px; padding: 40px; color: #FAF7F3; margin-bottom: 32px; max-width: 712px; box-shadow: 0 18px 40px rgba(58,38,26,0.20);">

        <p style="font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: #A08C7D; margin: 0 0 8px;">
            JADWAL MILESTONE SELANJUTNYA
        </p>

        <h2 style="font-family: 'DM Serif Display', serif; font-size: 32px; font-weight: 400; margin: 0 0 20px; color: #FAF7F3; line-height: 1.2;">
            Milestone 2 - Implementasi Project
        </h2>

        <div style="display: flex; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 24px;">
            <div style="min-width: 266px;">
                <p style="font-size: 10px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: #A08C7D; margin: 0 0 6px;">
                    JADWAL MILESTONE
                </p>
                <p style="font-size: 14px; font-weight: 500; margin: 0; color: #FAF7F3;">
                    Selasa, 29 September 2026
                </p>
            </div>

            <div>
                <p style="font-size: 10px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: #A08C7D; margin: 0 0 6px;">
                    PROGRES PROJECT
                </p>
                <p style="font-size: 14px; font-weight: 500; margin: 0; color: #FAF7F3;">
                    65%
                </p>
            </div>
        </div>
    </div>

    {{-- Grid 2 Kolom --}}
    <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 32px; align-items: start;">

        {{-- CARD KIRI: Anggota Kelompok --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 28px; padding: 28px; box-shadow: 0 8px 24px rgba(58,38,26,0.05);">

            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 20px; border-bottom: 1px solid var(--sim-border-light);">
                <div>
                    <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sim-text-muted); margin: 0 0 4px;">
                        ORANG-ORANG DI DALAMNYA
                    </p>
                    <h2 style="font-family: 'DM Serif Display', serif; font-size: 24px; font-weight: 400; color: var(--sim-text-dark); margin: 0;">
                        Anggota kelompok
                    </h2>
                </div>

                <div style="color: var(--sim-text-light);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.75"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>

            {{-- List Anggota --}}
            <div style="display: flex; flex-direction: column; padding-top: 0;">
                @foreach ($anggota as $a)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 0; {{ $loop->last ? '' : 'border-bottom: 1px solid var(--sim-border-light);' }}">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: #EBE1CC; color: #6E5B3E; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                                {{ $a['inisial'] }}
                            </div>
                            <div>
                                <p style="font-size: 14px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 1px;">
                                    {{ $a['nama'] }}
                                </p>
                                <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0;">
                                    {{ $a['peran'] }}
                                </p>
                            </div>
                        </div>
                        <button type="button" aria-label="Pesan {{ $a['nama'] }}"
                            style="border: none; background: transparent; color: var(--sim-text-light); cursor: pointer; padding: 6px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.75"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                <line x1="8.5" y1="10.5" x2="15.5" y2="10.5"></line>
                                <line x1="8.5" y1="13.5" x2="13.5" y2="13.5"></line>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>

            {{-- Tombol Tambah anggota (pojok kanan bawah card) --}}
            <div style="display: flex; justify-content: flex-end; padding-top: 16px;">
                <button type="button"
                    style="display: inline-flex; align-items: center; gap: 8px; border: none; border-radius: 999px; padding: 11px 22px; background: #3A261A; color: #FFFFFF; font-family: inherit; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 6px 16px rgba(58,38,26,0.18);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.25" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah anggota
                </button>
            </div>
        </div>

        {{-- CARD KANAN: Titik Fokus (tinggi mengikuti isi) --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 28px; padding: 28px; box-shadow: 0 8px 24px rgba(58,38,26,0.05);">

            <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.16em; text-transform: uppercase; color: var(--sim-text-muted); margin: 0 0 4px;">
                TITIK FOKUS
            </p>

            <h2 style="font-family: 'DM Serif Display', serif; font-size: 24px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 18px;">
                Implementasi
            </h2>

            <p style="font-size: 13px; color: var(--sim-text-body); line-height: 1.45; margin: 0 0 24px;">
                Pekerjaan kelompok saat ini adalah menerjemahkan temuan lapangan menjadi prototipe yang bisa dipelajari bersama.
            </p>

            {{-- Progress Bar --}}
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--sim-text-muted); margin-bottom: 8px;">
                    <span>Progress</span>
                    <span style="font-weight: 500;">65%</span>
                </div>

                <div style="width: 100%; height: 8px; background: #E8E2D9; border-radius: 999px; overflow: hidden;">
                    <div style="width: 65%; height: 100%; background: #3F6B4E; border-radius: 999px;"></div>
                </div>
            </div>

            {{-- Aktivitas Tercatat --}}
            <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--sim-text-body); margin-bottom: 34px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="#3F6B4E" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
                <span>4 aktivitas tercatat</span>
            </div>

            {{-- Link Buka Catatan Proses --}}
            <a href="{{ route('mahasiswa.logbook') }}"
                style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: #5C3317; text-decoration: none;">
                Buka catatan proses &rarr;
            </a>
        </div>

    </div>
</div>

</div>

@endsection