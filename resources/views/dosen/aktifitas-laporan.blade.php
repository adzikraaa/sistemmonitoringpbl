@extends('layouts.dosen')

@section('title', 'Laporan Aktivitas Kelompok')
@section('breadcrumb', 'Laporan aktivitas')
@section('meta_description', 'Pantau pembaruan pekerjaan dan perkembangan proyek mahasiswa.')

@section('content')

{{-- Page Header Section --}}
<div class="sim-page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div style="flex: 1;">
        <p class="sim-page-header__eyebrow" style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); margin-bottom: 4px;">RUANG DOSEN • LAPORAN AKTIVITAS</p>
        <h1 class="sim-page-header__title" style="font-family: 'DM Serif Display', serif; font-size: 28px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 6px;">
            Laporan aktivitas kelompok
        </h1>
        <p class="sim-page-header__desc" style="font-size: 13.5px; color: var(--sim-text-body); margin: 0;">
            Pantau pembaruan pekerjaan dan perkembangan proyek mahasiswa.
        </p>
    </div>

    <div style="display: flex; gap: 10px;">
        <button type="button" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 20px; font-size: 13px; font-weight: 500; color: var(--sim-text-dark); cursor: pointer;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Ekspor rekap
        </button>
        <button type="button" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 20px; font-size: 13px; font-weight: 500; color: var(--sim-text-dark); cursor: pointer;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
            Perbarui data
        </button>
    </div>
</div>

{{-- Main Container --}}
<div style="padding: 0 32px 40px;">

    {{-- Filter Bar --}}
    <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; padding: 18px 24px; margin-bottom: 24px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; align-items: center;">
        <div>
            <label style="display: block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin-bottom: 6px;">Kelas</label>
            <select style="width: 100%; padding: 8px 12px; border: 1px solid var(--sim-border); border-radius: 8px; font-size: 13px; background: #FFF; color: var(--sim-text-dark); outline: none;">
                <option>Semua Kelas</option>
                <option>SIB 3A</option>
                <option>SIB 3B</option>
                <option>SIB 3C</option>
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin-bottom: 6px;">Jenis Laporan</label>
            <select style="width: 100%; padding: 8px 12px; border: 1px solid var(--sim-border); border-radius: 8px; font-size: 13px; background: #FFF; color: var(--sim-text-dark); outline: none;">
                <option>Semua jenis</option>
                <option>Logbook</option>
                <option>Proposal</option>
                <option>Milestone</option>
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin-bottom: 6px;">Status Evaluasi</label>
            <select style="width: 100%; padding: 8px 12px; border: 1px solid var(--sim-border); border-radius: 8px; font-size: 13px; background: #FFF; color: var(--sim-text-dark); outline: none;">
                <option>Semua status</option>
                <option>Menunggu review</option>
                <option>Perlu revisi</option>
                <option>Disetujui</option>
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin-bottom: 6px;">Pencarian</label>
            <div style="position: relative;">
                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--sim-text-light);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" placeholder="Cari topik atau lainnya..." style="width: 100%; padding: 8px 12px 8px 34px; border: 1px solid var(--sim-border); border-radius: 8px; font-size: 13px; outline: none; box-sizing: border-box;">
            </div>
        </div>
    </div>

    {{-- List Section Wrapper --}}
    <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; margin-bottom: 24px; overflow: hidden;">
        
        {{-- List Header Info --}}
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--sim-border-light); display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--sim-text-muted);">Daftar Pembaruan & Dokumen</span>
            <span style="font-size: 12px; color: var(--sim-text-muted);">Menampilkan 4 dari 12 aktivitas</span>
        </div>

        {{-- Items List --}}
        <div style="display: flex; flex-direction: column;">

            {{-- Item 1: Menunggu Review --}}
            <div style="padding: 18px 24px; border-bottom: 1px solid var(--sim-border-light); display: flex; align-items: center; justify-content: space-between; transition: background 0.15s;" onmouseover="this.style.background='#FAF7F3'" onmouseout="this.style.background='#FFFFFF'">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #FDF6EE; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="13" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <span style="font-size: 11px; font-weight: 600; color: var(--sim-text-muted);">Kelompok 01 - SIB 3A</span>
                            <span style="color: var(--sim-text-light);">•</span>
                            <span style="font-size: 11px; color: var(--sim-text-muted);">Pengembangan Sistem Magang</span>
                        </div>
                        <h3 style="font-size: 14.5px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 6px;">
                            Logbook: Implementasi autentikasi & perancangan token JWT
                        </h3>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: var(--sim-text-muted);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>Diunggah oleh Andi Pratama</span>
                            <span>•</span>
                            <span>28 September 2026, 14:15</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 16px;">
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #FEF3C7; color: #92400E; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                        Menunggu review
                    </span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--sim-text-light);"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            {{-- Item 2: Perlu Revisi --}}
            <div style="padding: 18px 24px; border-bottom: 1px solid var(--sim-border-light); display: flex; align-items: center; justify-content: space-between; transition: background 0.15s;" onmouseover="this.style.background='#FAF7F3'" onmouseout="this.style.background='#FFFFFF'">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.2"/></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <span style="font-size: 11px; font-weight: 600; color: var(--sim-text-muted);">Kelompok 01 - SIB 3B</span>
                            <span style="color: var(--sim-text-light);">•</span>
                            <span style="font-size: 11px; color: var(--sim-text-muted);">Platform Kolaborasi Laboratorium</span>
                        </div>
                        <h3 style="font-size: 14.5px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 6px;">
                            Proposal: Revisi latar belakang & batasan implementasi sensor IoT
                        </h3>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: var(--sim-text-muted);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>Diunggah oleh Dimas Arya</span>
                            <span>•</span>
                            <span>28 September 2026, 14:15</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 16px;">
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #FEE2E2; color: #991B1B; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #DC2626;"></span>
                        Perlu revisi
                    </span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--sim-text-light);"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            {{-- Item 3: Disetujui --}}
            <div style="padding: 18px 24px; border-bottom: 1px solid var(--sim-border-light); display: flex; align-items: center; justify-content: space-between; transition: background 0.15s;" onmouseover="this.style.background='#FAF7F3'" onmouseout="this.style.background='#FFFFFF'">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <span style="font-size: 11px; font-weight: 600; color: var(--sim-text-muted);">Kelompok 01 - SIB 3C</span>
                            <span style="color: var(--sim-text-light);">•</span>
                            <span style="font-size: 11px; color: var(--sim-text-muted);">Sistem Informasi Inventaris Kampus</span>
                        </div>
                        <h3 style="font-size: 14.5px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 6px;">
                            Logbook: Pengujian unit testing modul inventaris barang
                        </h3>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: var(--sim-text-muted);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>Diunggah oleh Siti Rahma</span>
                            <span>•</span>
                            <span>Disetujui kemarin pada 20:10</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 16px;">
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #ECFDF5; color: #065F46; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                        Disetujui
                    </span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--sim-text-light);"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            {{-- Item 4: Disetujui --}}
            <div style="padding: 18px 24px; border-bottom: 1px solid var(--sim-border-light); display: flex; align-items: center; justify-content: space-between; transition: background 0.15s;" onmouseover="this.style.background='#FAF7F3'" onmouseout="this.style.background='#FFFFFF'">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <span style="font-size: 11px; font-weight: 600; color: var(--sim-text-muted);">Kelompok 01 - SIB 3D</span>
                            <span style="color: var(--sim-text-light);">•</span>
                            <span style="font-size: 11px; color: var(--sim-text-muted);">Pengembangan Situs Web Perpustakaan Kampus</span>
                        </div>
                        <h3 style="font-size: 14.5px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 6px;">
                            Logbook: Penyusunan skema database ERD & relasi tabel
                        </h3>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: var(--sim-text-muted);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>Diunggah oleh Budi Santoso</span>
                            <span>•</span>
                            <span style="color: #059669; font-weight: 500;">Disetujui pada 25 Sep 2026</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 16px;">
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #ECFDF5; color: #065F46; border-radius: 20px; font-size: 12px; font-weight: 600; white-space: nowrap;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                        Disetujui
                    </span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--sim-text-light);"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            {{-- Item 5: Disetujui --}}
            <div style="padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; transition: background 0.15s;" onmouseover="this.style.background='#FAF7F3'" onmouseout="this.style.background='#FFFFFF'">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                            <span style="font-size: 11px; font-weight: 600; color: var(--sim-text-muted);">Kelompok 01 - SIB 3E</span>
                            <span style="color: var(--sim-text-light);">•</span>
                            <span style="font-size: 11px; color: var(--sim-text-muted);">Pengembangan Sistem Alarm Pintar</span>
                        </div>
                        <h3 style="font-size: 14.5px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 6px;">
                            Milestone 1 : Laporan Progress Minggu 1–4
                        </h3>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: var(--sim-text-muted);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>Diunggah oleh Andika Sulaiman</span>
                            <span>•</span>
                            <span style="color: #059669; font-weight: 500;">Disetujui pada 8 Okt 2026</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 16px;">
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #ECFDF5; color: #065F46; border-radius: 20px; font-size: 12px; font-weight: 600; white-space: nowrap;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                        Disetujui
                    </span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--sim-text-light);"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

        </div>

    </div>

    {{-- Pagination Bar --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; font-size: 13px; color: var(--sim-text-muted);">
        <span>Menampilkan halaman 1 dari 3</span>
        <div style="display: flex; align-items: center; gap: 6px;">
            <button type="button" style="padding: 6px 12px; border: 1px solid var(--sim-border); background: #FFF; border-radius: 6px; color: var(--sim-text-light); cursor: pointer;" disabled>Sebelumnya</button>
            <button type="button" style="padding: 6px 12px; border: 1px solid var(--sim-border); background: #2C1A0E; color: #FAF7F3; border-radius: 6px; font-weight: 600; cursor: pointer;">1</button>
            <button type="button" style="padding: 6px 12px; border: 1px solid var(--sim-border); background: #FFF; color: var(--sim-text-dark); border-radius: 6px; cursor: pointer;">2</button>
            <button type="button" style="padding: 6px 12px; border: 1px solid var(--sim-border); background: #FFF; color: var(--sim-text-dark); border-radius: 6px; cursor: pointer;">3</button>
            <button type="button" style="padding: 6px 12px; border: 1px solid var(--sim-border); background: #FFF; border-radius: 6px; color: var(--sim-text-dark); cursor: pointer;">Selanjutnya</button>
        </div>
    </div>

    {{-- Bottom Grid (2 Kolom: Prioritas & Statistik) --}}
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; align-items: start;">

        {{-- Kartu Kiri: Prioritas Bimbingan --}}
        <div style="background: linear-gradient(135deg, #321F14 0%, #20130C 100%); border-radius: 20px; padding: 28px 32px; color: #FAF7F3; border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #A08C7D; margin: 0;">
                    PRIORITAS BIMBINGAN PEKAN INI
                </p>
                <span style="color: #A08C7D;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </span>
            </div>

            <h2 style="font-family: 'DM Serif Display', serif; font-size: 24px; font-weight: 400; color: #FAF7F3; margin: 0 0 12px; line-height: 1.3;">
                Total 4 dokumen menunggu evaluasi dosen minggu ini.
            </h2>

            <p style="font-size: 13px; color: #D1C4B8; line-height: 1.5; margin: 0 0 24px;">
                Pemberian catatan berkala membantu mahasiswa mempertahankan momentum kerja kelompok serta menghindari deviasi dari target capaian semester.
            </p>

            <div style="display: flex; align-items: center; gap: 20px;">
                <a href="#" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #FAF7F3; color: #2C1A0E; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;">
                    Mulai review antrean pertama &rarr;
                </a>
                <span style="font-size: 12px; color: #A08C7D;">Estimasi waktu: ~15 menit</span>
            </div>
        </div>

        {{-- Kartu Kanan: Statistik Respon Dosen --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 20px; padding: 28px 32px; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
            <div>
                <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--sim-text-muted); margin: 0 0 20px;">
                    STATISTIK RESPON DOSEN
                </p>

                {{-- Rata-rata waktu tanggap --}}
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 13px; color: var(--sim-text-body);">Rata-rata waktu tanggap</span>
                        <span style="font-size: 14px; font-weight: 600; color: var(--sim-text-dark);">1,4 hari</span>
                    </div>
                    <div style="width: 100%; height: 6px; background: #E8E2D9; border-radius: 3px; overflow: hidden;">
                        <div style="width: 28%; height: 100%; background: #D97706; border-radius: 3px;"></div>
                    </div>
                </div>

                {{-- Ketepatan logbook kelompok --}}
                <div style="margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 13px; color: var(--sim-text-body);">Ketepatan logbook kelompok</span>
                        <span style="font-size: 14px; font-weight: 600; color: var(--sim-text-dark);">91%</span>
                    </div>
                    <div style="width: 100%; height: 6px; background: #E8E2D9; border-radius: 3px; overflow: hidden;">
                        <div style="width: 91%; height: 100%; background: #4A7C59; border-radius: 3px;"></div>
                    </div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: var(--sim-text-muted);">
                <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                <span>Sinkronisasi otomatis aktif</span>
            </div>
        </div>

    </div>

</div>

@endsection