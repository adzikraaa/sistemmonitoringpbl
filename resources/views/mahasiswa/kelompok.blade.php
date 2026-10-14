@extends('layouts.mahasiswa')

@section('title', 'Kelompok')
@section('meta_description', 'Ruang kolaborasi kelompok mahasiswa SIM-PBL')

@section('content')
{{-- Group Info Bar Top --}}
<div class="sim-group-bar">
    <div class="sim-group-bar__left">
        <span class="sim-group-bar__name">Kelompok 01 – SIB 3B</span>
        <span class="sim-badge" style="background: #EAE6DF; color: #5C4A3E; font-weight: 500;">4 anggota</span>
        <span class="sim-group-bar__sep">|</span>
        <span class="sim-group-bar__dosen">Dosen Pembimbing: <strong>Dr. Ahmad Fikri, S.Kom., M.T.</strong></span>
        <span class="sim-group-bar__sep">|</span>
        <span class="sim-group-bar__jadwal">Selasa, 13.00–15.00</span>
    </div>
    <div class="sim-group-bar__members-list">
        Andi Pratama — SIB 3B · Budi Santoso — SIB 3B · Rina Maulani — SIB 3B · Agus Setiawan — SIB 3B
    </div>
</div>

{{-- Page Header Section --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">KELOMPOK SAYA</p>
        <h1 class="sim-page-header__title">Kelompok 01</h1>
        <p class="sim-page-header__desc">Ruang kolaborasi untuk menjaga proyek tetap bergerak.</p>
    </div>
    <div class="sim-page-header__right">
        <button class="sim-btn-secondary" style="border: 1px solid var(--sim-border); border-radius: 20px; padding: 8px 16px; background: #fff; gap: 8px; font-size: 13px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            Pengaturan kelompok
        </button>
    </div>
</div>

{{-- Main Container --}}
<div style="padding: 0 32px 40px;">

    {{-- Banner Milestone selanjutnya --}}
    <div style="background: linear-gradient(135deg, #3A261A 0%, #291A11 100%); border-radius: 16px; padding: 28px 32px; color: #FAF7F3; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.05);">
        <p style="font-size: 11px; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #A08C7D; margin: 0 0 8px;">
            JADWAL MILESTONE SELANJUTNYA
        </p>
        <h2 style="font-family: 'DM Serif Display', serif; font-size: 32px; font-weight: 400; margin: 0 0 24px; color: #FAF7F3; line-height: 1.2;">
            Milestone 2 - Implementasi Project
        </h2>
        
        <div style="display: flex; gap: 48px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
            <div>
                <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #A08C7D; margin: 0 0 4px;">
                    JADWAL MILESTONE
                </p>
                <p style="font-size: 14px; font-weight: 600; margin: 0; color: #FAF7F3;">
                    Selasa, 29 September 2026
                </p>
            </div>
            <div>
                <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #A08C7D; margin: 0 0 4px;">
                    PROGRES PROJECT
                </p>
                <p style="font-size: 14px; font-weight: 600; margin: 0; color: #FAF7F3;">
                    65%
                </p>
            </div>
        </div>
    </div>

    {{-- Grid 2 Kolom (Anggota & Titik Fokus) --}}
    <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 24px; align-items: start;">
        
        {{-- CARD KIRI: Anggota Kelompok --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; padding: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div>
                    <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--sim-text-muted); margin: 0 0 4px;">
                        ORANG-ORANG DI DALAMNYA
                    </p>
                    <h2 style="font-family: 'DM Serif Display', serif; font-size: 24px; font-weight: 400; color: var(--sim-text-dark); margin: 0;">
                        Anggota kelompok
                    </h2>
                </div>
                <div style="color: var(--sim-text-light);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>

            {{-- List Anggota --}}
            <div style="display: flex; flex-direction: column;">
                
                {{-- Item 1: Adzikra --}}
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--sim-border-light);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #E6DFD5; color: #5C4A3E; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                            AP
                        </div>
                        <div>
                            <p style="font-size: 14px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 2px;">Adzikra</p>
                            <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0;">Ketua</p>
                        </div>
                    </div>
                    <button style="border: none; background: transparent; color: var(--sim-text-light); cursor: pointer; padding: 6px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </button>
                </div>

                {{-- Item 2: Tanggaq --}}
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--sim-border-light);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #E6DFD5; color: #5C4A3E; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                            BS
                        </div>
                        <div>
                            <p style="font-size: 14px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 2px;">Tanggaq</p>
                            <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0;">Anggota</p>
                        </div>
                    </div>
                    <button style="border: none; background: transparent; color: var(--sim-text-light); cursor: pointer; padding: 6px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </button>
                </div>

                {{-- Item 3: Dina --}}
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--sim-border-light);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #E6DFD5; color: #5C4A3E; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                            RM
                        </div>
                        <div>
                            <p style="font-size: 14px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 2px;">Dina</p>
                            <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0;">Anggota</p>
                        </div>
                    </div>
                    <button style="border: none; background: transparent; color: var(--sim-text-light); cursor: pointer; padding: 6px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </button>
                </div>

                {{-- Item 4: Niky --}}
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--sim-border-light);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #E6DFD5; color: #5C4A3E; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                            AS
                        </div>
                        <div>
                            <p style="font-size: 14px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 2px;">Niky</p>
                            <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0;">Anggota</p>
                        </div>
                    </div>
                    <button style="border: none; background: transparent; color: var(--sim-text-light); cursor: pointer; padding: 6px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </button>
                </div>

                {{-- Item 5: Ayu --}}
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: #E6DFD5; color: #5C4A3E; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                            AS
                        </div>
                        <div>
                            <p style="font-size: 14px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 2px;">Ayu</p>
                            <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0;">Anggota</p>
                        </div>
                    </div>
                    <button style="border: none; background: transparent; color: var(--sim-text-light); cursor: pointer; padding: 6px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- CARD KANAN: Titik Fokus --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; min-height: 100%;">
            <div>
                <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--sim-text-muted); margin: 0 0 4px;">
                    TITIK FOKUS
                </p>
                <h2 style="font-family: 'DM Serif Display', serif; font-size: 24px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 16px;">
                    Implementasi
                </h2>
                <p style="font-size: 13px; color: var(--sim-text-body); line-height: 1.6; margin: 0 0 24px;">
                    Pekerjaan kelompok saat ini adalah menerjemahkan temuan lapangan menjadi prototipe yang bisa dipelajari bersama.
                </p>

                {{-- Progress Bar --}}
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--sim-text-muted); margin-bottom: 6px;">
                        <span>Progress</span>
                        <span style="font-weight: 600; color: var(--sim-text-dark);">65%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: #E8E2D9; border-radius: 4px; overflow: hidden;">
                        <div style="width: 65%; height: 100%; background: #4A7C59; border-radius: 4px;"></div>
                    </div>
                </div>

                {{-- Aktivitas Tercatat --}}
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: var(--sim-text-body); margin-bottom: 32px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4A7C59" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                    <span>4 aktivitas tercatat</span>
                </div>
            </div>

            {{-- Link Buka Catatan Proses --}}
            <a href="{{ route('mahasiswa.logbook') }}" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: #5C3317; text-decoration: none; transition: opacity 0.15s;">
                Buka catatan proses &rarr;
            </a>
        </div>

    </div>

</div>
@endsection