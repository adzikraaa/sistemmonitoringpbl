@extends('layouts.mahasiswa')

@section('title', 'Notifikasi')
@section('breadcrumb', 'Notifikasi')
@section('meta_description', 'Pusat aktivitas dan pemberitahuan SIM-PBL')

@section('content')
{{-- Page Header --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">PUSAT AKTIVITAS</p>
        <h1 class="sim-page-header__title">Notifikasi</h1>
        <p class="sim-page-header__desc">Pantau pembaruan proposal, bimbingan, logbook, dan perkembangan proyek kelompokmu.</p>
    </div>
    <div class="sim-page-header__right">
        <button class="sim-btn-secondary" style="border: 1px solid var(--sim-border); border-radius: 20px; padding: 8px 16px; background: #fff; gap: 8px; font-size: 13px; display: inline-flex; align-items: center; cursor: pointer;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Tandai semua dibaca
        </button>
    </div>
</div>

{{-- Main Container --}}
<div style="padding: 0 32px 40px;">

    {{-- Summary Cards (3 Kolom Atas) --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px;">
        
        {{-- Card 1: Belum Dibaca --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 14px; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 12.5px; color: var(--sim-text-muted); margin: 0 0 6px; font-weight: 500;">Belum dibaca</p>
                <h3 style="font-family: 'DM Serif Display', serif; font-size: 32px; font-weight: 400; color: var(--sim-text-dark); margin: 0; line-height: 1;">3</h3>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #F7F3EE; display: flex; align-items: center; justify-content: center; color: var(--sim-text-body);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            </div>
        </div>

        {{-- Card 2: Notifikasi Hari Ini --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 14px; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 12.5px; color: var(--sim-text-muted); margin: 0 0 6px; font-weight: 500;">Notifikasi hari ini</p>
                <h3 style="font-family: 'DM Serif Display', serif; font-size: 32px; font-weight: 400; color: var(--sim-text-dark); margin: 0; line-height: 1;">2</h3>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #F7F3EE; display: flex; align-items: center; justify-content: center; color: var(--sim-text-body);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
        </div>

        {{-- Card 3: Total Tersimpan (Ikon Layers) --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 14px; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 12.5px; color: var(--sim-text-muted); margin: 0 0 6px; font-weight: 500;">Total tersimpan</p>
                <h3 style="font-family: 'DM Serif Display', serif; font-size: 32px; font-weight: 400; color: var(--sim-text-dark); margin: 0; line-height: 1;">6</h3>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #F7F3EE; display: flex; align-items: center; justify-content: center; color: var(--sim-text-body);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
        </div>

    </div>

    {{-- Filter Bar & Search --}}
    <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; padding: 18px 24px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 16px;">
        
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            {{-- Tabs Filter Utama --}}
            <div style="display: flex; background: #F2ECE4; padding: 4px; border-radius: 20px; gap: 4px;">
                <button style="padding: 6px 16px; border-radius: 16px; border: none; background: #FFFFFF; color: var(--sim-text-dark); font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">Semua</button>
                <button style="padding: 6px 16px; border-radius: 16px; border: none; background: transparent; color: var(--sim-text-muted); font-size: 13px; font-weight: 500; cursor: pointer;">Belum dibaca <span style="background: #5C3317; color: #fff; padding: 1px 6px; border-radius: 10px; font-size: 11px; margin-left: 4px;">3</span></button>
                <button style="padding: 6px 16px; border-radius: 16px; border: none; background: transparent; color: var(--sim-text-muted); font-size: 13px; font-weight: 500; cursor: pointer;">Dibaca</button>
            </div>

            {{-- Search Box --}}
            <div style="position: relative; min-width: 280px;">
                <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--sim-text-light);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" placeholder="Cari notifikasi atau kata kunci..." style="width: 100%; padding: 9px 16px 9px 40px; border: 1px solid var(--sim-border); border-radius: 20px; font-size: 13px; background: #FAF7F3; outline: none; color: var(--sim-text-dark);">
            </div>
        </div>

        {{-- Category Pills --}}
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; padding-top: 4px; border-top: 1px solid var(--sim-border-light);">
            <span style="font-size: 11px; font-weight: 600; color: var(--sim-text-muted); text-transform: uppercase; margin-right: 4px;">Kategori:</span>
            <button style="padding: 5px 14px; border-radius: 14px; border: none; background: #2C1A0E; color: #FAF7F3; font-size: 12.5px; font-weight: 500; cursor: pointer;">Semua kategori</button>
            <button style="padding: 5px 14px; border-radius: 14px; border: 1px solid var(--sim-border); background: #FAF7F3; color: var(--sim-text-body); font-size: 12.5px; font-weight: 500; cursor: pointer;">Proposal</button>
            <button style="padding: 5px 14px; border-radius: 14px; border: 1px solid var(--sim-border); background: #FAF7F3; color: var(--sim-text-body); font-size: 12.5px; font-weight: 500; cursor: pointer;">Logbook</button>
            <button style="padding: 5px 14px; border-radius: 14px; border: 1px solid var(--sim-border); background: #FAF7F3; color: var(--sim-text-body); font-size: 12.5px; font-weight: 500; cursor: pointer;">Milestone</button>
            <button style="padding: 5px 14px; border-radius: 14px; border: 1px solid var(--sim-border); background: #FAF7F3; color: var(--sim-text-body); font-size: 12.5px; font-weight: 500; cursor: pointer;">Bimbingan</button>
            <button style="padding: 5px 14px; border-radius: 14px; border: 1px solid var(--sim-border); background: #FAF7F3; color: var(--sim-text-body); font-size: 12.5px; font-weight: 500; cursor: pointer;">Sistem</button>
        </div>

    </div>

    {{-- Daftar Notifikasi (List Cards) --}}
    <div style="display: flex; flex-direction: column; gap: 12px;">

        {{-- Item 1: Proposal disetujui (Belum dibaca) --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EBF4EC; color: #4A7C59; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 10px; font-weight: 700; background: #EBF4EC; color: #4A7C59; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Proposal</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                                Proposal disetujui <span style="width: 6px; height: 6px; background: #C0392B; border-radius: 50%; display: inline-block;"></span>
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Proposal kelompokmu telah disetujui oleh dosen pembimbing.
                        </p>
                    </div>
                </div>
                <span style="font-size: 12px; color: var(--sim-text-muted); white-space: nowrap;">10 menit yang lalu</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.proposal') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Buka halaman proposal &rarr;
                </a>
                <button style="border: none; background: transparent; color: var(--sim-text-muted); cursor: pointer; font-weight: 500;">Tandai dibaca</button>
            </div>
        </div>

        {{-- Item 2: Jadwal bimbingan diperbarui (Belum dibaca) --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #FFF9E6; color: #8A6A00; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 10px; font-weight: 700; background: #FFF9E6; color: #8A6A00; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Bimbingan</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                                Jadwal bimbingan diperbarui <span style="width: 6px; height: 6px; background: #C0392B; border-radius: 50%; display: inline-block;"></span>
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Jadwal bimbingan kelompok telah diperbarui oleh Dr. Ahmad Fikri untuk sesi tinjauan sprint pertama.
                        </p>
                    </div>
                </div>
                <span style="font-size: 12px; color: var(--sim-text-muted); white-space: nowrap;">Hari ini, 09.30</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="#" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Lihat detail jadwal bimbingan &rarr;
                </a>
                <button style="border: none; background: transparent; color: var(--sim-text-muted); cursor: pointer; font-weight: 500;">Tandai dibaca</button>
            </div>
        </div>

        {{-- Item 3: Logbook perlu diperbaiki (Belum dibaca) --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #FDF2F2; color: #C0392B; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 10px; font-weight: 700; background: #FDF2F2; color: #C0392B; padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Logbook</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0; display: inline-flex; align-items: center; gap: 6px;">
                                Logbook perlu diperbaiki <span style="width: 6px; height: 6px; background: #C0392B; border-radius: 50%; display: inline-block;"></span>
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Logbook minggu ini perlu dilengkapi sesuai catatan dosen pembimbing: tambahkan diagram ERD dan relasi modul otentikasi.
                        </p>
                    </div>
                </div>
                <span style="font-size: 12px; color: var(--sim-text-muted); white-space: nowrap;">Kemarin, 16.45</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.logbook') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Buka halaman logbook &rarr;
                </a>
                <button style="border: none; background: transparent; color: var(--sim-text-muted); cursor: pointer; font-weight: 500;">Tandai dibaca</button>
            </div>
        </div>

        {{-- Item 4: Milestone diperbarui (Sudah dibaca) --}}
        <div style="background: #F9F7F5; border: 1px solid var(--sim-border); border-radius: 16px; padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EBE5DC; color: var(--sim-text-muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 10px; font-weight: 700; background: #EBE5DC; color: var(--sim-text-muted); padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Milestone</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0;">
                                Milestone diperbarui
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Status milestone Perancangan Sistem telah diperbarui menjadi selesai 100%.
                        </p>
                    </div>
                </div>
                <span style="font-size: 12px; color: var(--sim-text-muted); white-space: nowrap;">3 hari lalu</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.milestone') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Buka halaman milestone &rarr;
                </a>
                <span style="font-size: 12px; color: var(--sim-text-light);">Tandai belum dibaca</span>
            </div>
        </div>

        {{-- Item 5: Revisi proposal diperlukan --}}
        <div style="background: #F9F7F5; border: 1px solid var(--sim-border); border-radius: 16px; padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EBE5DC; color: var(--sim-text-muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 10px; font-weight: 700; background: #EBE5DC; color: var(--sim-text-muted); padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Proposal</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0;">
                                Revisi proposal diperlukan
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Dosen pembimbing memberikan catatan revisi pada proposal kelompokmu terkait batasan ruang lingkup implementasi.
                        </p>
                    </div>
                </div>
                <span style="font-size: 12px; color: var(--sim-text-muted); white-space: nowrap;">5 hari lalu</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.proposal') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Lihat catatan revisi proposal &rarr;
                </a>
                <span style="font-size: 12px; color: var(--sim-text-light);">Tandai belum dibaca</span>
            </div>
        </div>

        {{-- Item 6: Pengingat aktivitas sistem --}}
        <div style="background: #F9F7F5; border: 1px solid var(--sim-border); border-radius: 16px; padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #EBE5DC; color: var(--sim-text-muted); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-size: 10px; font-weight: 700; background: #EBE5DC; color: var(--sim-text-muted); padding: 2px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Sistem</span>
                            <h4 style="font-size: 15px; font-weight: 600; color: var(--sim-text-dark); margin: 0;">
                                Pengingat aktivitas
                            </h4>
                        </div>
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.5;">
                            Jangan lupa memperbarui logbook aktivitas proyekmu sebelum batas rekapitulasi mingguan hari Jumat pukul 23.59 WIB.
                        </p>
                    </div>
                </div>
                <span style="font-size: 12px; color: var(--sim-text-muted); white-space: nowrap;">1 minggu lalu</span>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--sim-border-light); padding-top: 12px; font-size: 13px;">
                <a href="{{ route('mahasiswa.logbook') }}" style="color: #5C3317; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    Pelajari panduan pengisian logbook &rarr;
                </a>
                <span style="font-size: 12px; color: var(--sim-text-light);">Tandai belum dibaca</span>
            </div>
        </div>

    </div>

</div>
@endsection