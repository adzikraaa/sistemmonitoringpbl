@extends('layouts.koordinator')

@section('title', 'Detail Kelompok PBL')
@section('breadcrumb_title', 'Kelompok PBL')

@section('content')
@php
    $group = $kelompok;
    $projectName = $group->nama_kelompok ?? 'Sistem Inventaris Laboratorium';
    $projectTitle = $group->judul_proyek ?? 'Pengembangan sistem pencatatan inventaris laboratorium, perizinan, dan monitoring alat praktikum.';
    $statusKelompok = $group->status ?? 'Aktif';
    $kelas = $group->kelas?->nama_kelas ?? 'SIB 3C';
    $periode = $group->periode?->nama_periode ?? 'Ganjil 2026/2027';
    $dosenName = $group->dosen?->nama ?? 'Ir. Maya Safitri, M.Sc.';
    $dosenNip = $group->dosen?->nip ?? 'DSN-006';
    $milestone = 40;
    $nextDate = now()->addDays(10)->translatedFormat('d F Y');
    $members = $group->anggotaKelompok ?? collect();
@endphp

<style>
    .sim-detail-page {
        background: #f4f0ea;
        min-height: 100%;
        padding: 32px 28px 24px;
        color: #2f2a28;
    }

    .sim-detail-panel {
        max-width: 1180px;
        margin: 0 auto;
    }

    .sim-detail-breadcrumb {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
        color: #5d564f;
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .sim-detail-breadcrumb .left {
        font-weight: 700;
        color: #665e59;
    }

    .sim-detail-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .sim-detail-header h1 {
        margin: 0;
        font-size: 44px;
        line-height: 1.08;
        letter-spacing: -0.04em;
        font-weight: 800;
        color: #2d2927;
        font-family: Georgia, 'Times New Roman', serif;
    }

    .sim-detail-code {
        margin-top: 8px;
        font-size: 14px;
        color: #675f5b;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sim-detail-code strong {
        font-weight: 700;
        color: #2c2725;
    }

    .sim-detail-status-btn {
        border: 1px solid #d7cab8;
        background: rgba(255,255,255,0.06);
        color: #403930;
        border-radius: 9px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .sim-detail-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
        background: #f3efe9;
        border: 1px solid #d9d0c5;
        border-radius: 16px;
        padding: 18px 20px;
    }

    .sim-detail-metric {
        padding: 4px 0;
    }

    .sim-detail-label {
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #7e756d;
        margin-bottom: 8px;
        font-weight: 700;
    }

    .sim-detail-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 999px;
        background: #ebf8ee;
        color: #2d7a4f;
        font-weight: 700;
        font-size: 12px;
        border: 1px solid #cbe7d0;
    }

    .sim-detail-value {
        font-size: 16px;
        font-weight: 700;
        color: #2d2927;
    }

    .sim-detail-progress {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 2px;
    }

    .sim-detail-progress-bar {
        flex: 1;
        height: 10px;
        background: #e3dace;
        border-radius: 999px;
        overflow: hidden;
    }

    .sim-detail-progress-bar span {
        display: block;
        height: 100%;
        width: {{ $milestone }}%;
        background: #3f3936;
        border-radius: 999px;
    }

    .sim-detail-tabs {
        display: flex;
        align-items: center;
        gap: 20px;
        border-bottom: 1px solid #d3c8b7;
        margin-bottom: 24px;
    }

    .sim-detail-tab {
        padding: 14px 8px 12px;
        border: none;
        background: transparent;
        color: #6d6662;
        font-weight: 600;
        cursor: pointer;
        position: relative;
        font-size: 14px;
    }

    .sim-detail-tab.active {
        color: #2d2927;
        font-weight: 700;
    }

    .sim-detail-tab.active::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: -1px;
        height: 2px;
        background: #2d2927;
        border-radius: 2px;
    }

    .sim-detail-content {
        display: grid;
        grid-template-columns: minmax(0, 2.1fr) minmax(260px, 0.9fr);
        gap: 20px;
    }

    .sim-detail-main,
    .sim-detail-side {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sim-detail-card {
        background: #f7f3ee;
        border: 1px solid #d9d0c3;
        border-radius: 16px;
        padding: 20px 22px 18px;
    }

    .sim-detail-card-title {
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #7a706a;
        margin-bottom: 16px;
        font-weight: 700;
    }

    .sim-detail-project-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .sim-detail-project-name {
        font-size: 31px;
        line-height: 1.2;
        font-weight: 800;
        color: #2d2927;
        margin-bottom: 14px;
    }

    .sim-detail-copy {
        font-size: 14px;
        line-height: 1.7;
        color: #4f4945;
    }

    .sim-detail-subitem {
        margin-bottom: 18px;
    }

    .sim-detail-subitem:last-child {
        margin-bottom: 0;
    }

    .sim-detail-side-box {
        background: #f7f3ee;
        border: 1px solid #d9d0c3;
        border-radius: 16px;
        padding: 20px 20px 18px;
    }

    .sim-detail-next-date {
        font-size: 34px;
        line-height: 1.2;
        font-weight: 800;
        color: #2a2827;
        margin-bottom: 10px;
    }

    .sim-detail-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #2b2827;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
        flex-shrink: 0;
    }

    .sim-detail-dosen-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .sim-detail-dosen-name {
        font-size: 18px;
        font-weight: 800;
        color: #2d2827;
    }

    .sim-detail-dosen-nip {
        font-size: 12px;
        color: #5c5652;
    }

    .sim-detail-tab-panel {
        display: none;
    }

    .sim-detail-tab-panel.active {
        display: block;
    }

    .sim-detail-member-box {
        background: #f7f3ee;
        border: 1px solid #d9d0c3;
        border-radius: 16px;
        padding: 18px 20px 16px;
    }

    .sim-detail-member-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sim-detail-member-title {
        font-size: 18px;
        font-weight: 800;
        color: #2f2a28;
        margin: 0;
        font-family: Georgia, 'Times New Roman', serif;
    }

    .sim-detail-add-btn {
        border: 1px solid #d7cab8;
        background: rgba(255,255,255,0.2);
        color: #403930;
        border-radius: 9px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .sim-detail-table-wrap {
        overflow: hidden;
        border: 1px solid #d9d0c3;
        border-radius: 14px;
    }

    .sim-detail-table {
        width: 100%;
        border-collapse: collapse;
        background: #f7f3ee;
    }

    .sim-detail-table thead th {
        background: #e5dfd6;
        color: #615a55;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 11px;
        text-align: left;
        padding: 13px 14px;
        border-bottom: 1px solid #d7cbb9;
    }

    .sim-detail-table tbody td {
        padding: 14px;
        border-bottom: 1px solid #e3d8ca;
        color: #2d2827;
        font-size: 14px;
        vertical-align: middle;
    }

    .sim-detail-table tbody tr:last-child td {
        border-bottom: none;
    }

    .sim-detail-milestone-box {
        background: #f7f3ee;
        border: 1px solid #d9d0c3;
        border-radius: 16px;
        padding: 18px 20px 16px;
    }

    .sim-detail-milestone-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
    }

    .sim-detail-milestone-title {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #2d2927;
        font-family: Georgia, 'Times New Roman', serif;
    }

    .sim-detail-milestone-summary {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 12px;
        color: #625d59;
        white-space: nowrap;
    }

    .sim-detail-milestone-progress-wrap {
        margin-top: 14px;
        margin-bottom: 18px;
    }

    .sim-detail-milestone-progress-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
        font-weight: 800;
        color: #2d2927;
        font-size: 16px;
    }

    .sim-detail-milestone-progress-bar {
        width: 100%;
        height: 10px;
        background: #e4dccc;
        border-radius: 999px;
        overflow: hidden;
        position: relative;
    }

    .sim-detail-milestone-progress-bar span {
        display: block;
        height: 100%;
        width: 60%;
        background: #7a695d;
        border-radius: 999px;
    }

    .sim-detail-milestone-table-wrap {
        overflow: hidden;
        border: 1px solid #d9d0c3;
        border-radius: 14px;
        margin-top: 14px;
    }

    .sim-detail-milestone-table {
        width: 100%;
        border-collapse: collapse;
        background: #f7f3ee;
    }

    .sim-detail-milestone-table thead th {
        background: #e5dfd6;
        color: #615a55;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 11px;
        text-align: left;
        padding: 13px 14px;
        border-bottom: 1px solid #d7cbb9;
    }

    .sim-detail-milestone-table tbody td {
        padding: 14px;
        border-bottom: 1px solid #e3d8ca;
        color: #2d2827;
        font-size: 14px;
        vertical-align: middle;
    }

    .sim-detail-milestone-table tbody tr:last-child td {
        border-bottom: none;
    }

    .sim-detail-status-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid #d7cab8;
        background: #eef3ec;
        color: #317a4d;
        border-color: #cfe3d1;
    }

    .sim-detail-status-chip--pending {
        background: #f8efe4;
        color: #ad7231;
        border-color: #e8d0a4;
    }

    .sim-detail-role {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid #d7cab8;
        background: rgba(255,255,255,0.3);
        color: #564f4a;
    }

    .sim-detail-role.ketua {
        background: #f2e6d3;
        color: #6e5328;
        border-color: #e2c998;
    }

    .sim-detail-role.anggota {
        background: #f0f0eb;
        color: #5d574f;
        border-color: #d7d0c8;
    }

    .sim-detail-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        white-space: nowrap;
    }

    .sim-detail-action-link {
        color: #483f3b;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
    }

    .sim-status-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(33, 25, 21, 0.28);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
        padding: 20px;
    }

    .sim-status-modal-backdrop.open {
        display: flex;
    }

    .sim-status-modal {
        width: min(620px, calc(100vw - 40px));
        background: #f4efe9;
        border: 1px solid #d8cbb9;
        border-radius: 14px;
        box-shadow: 0 24px 60px rgba(36, 30, 27, 0.12);
        overflow: hidden;
    }

    .sim-status-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px 14px;
        border-bottom: 1px solid #ddd0c1;
    }

    .sim-status-modal-title {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #2d2927;
        font-family: Georgia, 'Times New Roman', serif;
    }

    .sim-status-modal-close {
        border: none;
        background: transparent;
        color: #544d49;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
    }

    .sim-status-modal-body {
        padding: 18px 20px 10px;
    }

    .sim-status-modal-panel {
        background: rgba(255,255,255,0.2);
        border: 1px solid #d8cbb9;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 18px;
    }

    .sim-status-modal-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
        gap: 14px 18px;
        align-items: stretch;
    }

    .sim-status-modal-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .sim-status-modal-label {
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #69615d;
        font-weight: 700;
    }

    .sim-status-badge-box {
        min-height: 38px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #d9cab6;
        background: rgba(255,255,255,0.35);
        font-size: 14px;
        font-weight: 700;
        color: #2d2927;
        white-space: normal;
        overflow-wrap: anywhere;
        min-width: 0;
        width: 100%;
    }

    .sim-status-select {
        width: 100%;
        border: 1px solid #d8cbb9;
        border-radius: 8px;
        background: rgba(255,255,255,0.35);
        color: #2d2927;
        padding: 10px 12px;
        font-size: 14px;
        box-sizing: border-box;
        appearance: none;
        background-image: linear-gradient(45deg, transparent 50%, #4a4039 50%), linear-gradient(135deg, #4a4039 50%, transparent 50%);
        background-position: calc(100% - 16px) calc(50% - 2px), calc(100% - 11px) calc(50% - 2px);
        background-size: 5px 5px, 5px 5px;
        background-repeat: no-repeat;
    }

    .sim-status-note {
        margin-top: 4px;
        padding: 12px 14px;
        background: #f7e7d7;
        border: 1px solid #e7c6a1;
        border-radius: 10px;
        color: #7a562c;
        font-size: 13px;
        line-height: 1.5;
    }

    .sim-status-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 14px 20px 18px;
    }

    .sim-status-modal-btn {
        border-radius: 9px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .sim-status-modal-btn--secondary {
        background: transparent;
        border: 1px solid #d8cbb9;
        color: #413a36;
    }

    .sim-status-modal-btn--primary {
        background: #3d312d;
        border: 1px solid #3d312d;
        color: #fff;
    }

    .sim-anggota-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(33, 25, 21, 0.28);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
        padding: 20px;
    }

    .sim-anggota-modal-backdrop.open {
        display: flex;
    }

    .sim-anggota-modal {
        width: min(760px, calc(100vw - 40px));
        background: #f4f0ea;
        border: 1px solid #d8cbb9;
        border-radius: 14px;
        box-shadow: 0 24px 60px rgba(36, 30, 27, 0.12);
        overflow: hidden;
    }

    .sim-anggota-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px 12px;
        border-bottom: 1px solid #ddd1c1;
    }

    .sim-anggota-modal-title {
        font-size: 22px;
        font-weight: 800;
        color: #2d2927;
        font-family: Georgia, 'Times New Roman', serif;
        margin: 0;
    }

    .sim-anggota-modal-close {
        border: none;
        background: transparent;
        color: #544d49;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
    }

    .sim-anggota-modal-body {
        padding: 18px 20px 20px;
    }

    .sim-anggota-panel {
        background: rgba(255,255,255,0.2);
        border: 1px solid #d8cbb9;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 16px;
    }

    .sim-anggota-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 18px;
    }

    .sim-anggota-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .sim-anggota-label {
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6e665f;
        font-weight: 700;
    }

    .sim-anggota-input,
    .sim-anggota-select {
        width: 100%;
        border: 1px solid #d8cbb9;
        border-radius: 8px;
        background: rgba(255,255,255,0.4);
        color: #2d2927;
        padding: 10px 12px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .sim-anggota-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 18px;
    }

    .sim-anggota-btn {
        border-radius: 9px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .sim-anggota-btn--secondary {
        background: transparent;
        border: 1px solid #d8cbb9;
        color: #403930;
    }

    .sim-anggota-btn--primary {
        background: #3d312d;
        border: 1px solid #3d312d;
        color: white;
    }

    @media (max-width: 980px) {
        .sim-detail-summary,
        .sim-detail-project-grid,
        .sim-detail-content {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="sim-detail-page">
    <div class="sim-detail-panel">
        <div class="sim-detail-breadcrumb">
            <span class="left">SIM-PBL / ADMINISTRASI PROGRAM</span>
            <span>{{ $periode }}</span>
        </div>

        <div class="sim-detail-header">
            <div>
                <h1>{{ $projectName }}</h1>
                <div class="sim-detail-code">
                    <strong>PBL-2026-3C-{{ str_pad($group->id ?? 1, 2, '0', STR_PAD_LEFT) }}</strong>
                    <span>•</span>
                    <span>{{ $kelas }}</span>
                </div>
            </div>
            <button type="button" class="sim-detail-status-btn" onclick="openStatusModal()">Ubah Status</button>
        </div>

        <div class="sim-detail-summary">
            <div class="sim-detail-metric">
                <div class="sim-detail-label">Status Kelompok</div>
                <div class="sim-detail-badge">{{ $statusKelompok }}</div>
            </div>
            <div class="sim-detail-metric">
                <div class="sim-detail-label">Kelas</div>
                <div class="sim-detail-value">{{ $kelas }}</div>
            </div>
            <div class="sim-detail-metric">
                <div class="sim-detail-label">Dosen Pembimbing</div>
                <div class="sim-detail-value">{{ $dosenName }}</div>
            </div>
            <div class="sim-detail-metric">
                <div class="sim-detail-label">Capaian</div>
                <div class="sim-detail-progress">
                    <div class="sim-detail-progress-bar"><span></span></div>
                    <strong style="font-size:14px; color:#2d2927;">{{ $milestone }}%</strong>
                </div>
            </div>
        </div>

        <div class="sim-detail-tabs" role="tablist" aria-label="Akun kelompok">
            <button type="button" class="sim-detail-tab active" data-target="overview" role="tab" aria-selected="true">Overview</button>
            <button type="button" class="sim-detail-tab" data-target="anggota" role="tab" aria-selected="false">Anggota</button>
            <button type="button" class="sim-detail-tab" data-target="milestone" role="tab" aria-selected="false">Progress/Milestone</button>
        </div>

        <div id="overview" class="sim-detail-tab-panel active" role="tabpanel">
            <div class="sim-detail-content">
                <div class="sim-detail-main">
                    <section class="sim-detail-card">
                        <div class="sim-detail-card-title">Informasi Proyek</div>
                        <div class="sim-detail-project-grid">
                            <div>
                                <div class="sim-detail-label" style="margin-bottom:8px;">Nama Proyek</div>
                                <div class="sim-detail-project-name">{{ $projectName }}</div>
                                <div class="sim-detail-copy">{{ $projectTitle }}</div>
                            </div>
                            <div>
                                <div class="sim-detail-subitem">
                                    <div class="sim-detail-label">Tanggal Pengajuan</div>
                                    <div class="sim-detail-value">{{ now()->subDays(20)->translatedFormat('d F Y') }}</div>
                                </div>
                                <div class="sim-detail-subitem">
                                    <div class="sim-detail-label">Status Proposal</div>
                                    <div class="sim-detail-value">Proposal disetujui</div>
                                </div>
                                <div class="sim-detail-subitem">
                                    <div class="sim-detail-label">Dokumen</div>
                                    <div class="sim-detail-value">4 file proposal</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="sim-detail-card">
                        <div class="sim-detail-card-title">Aktivitas Terkini</div>
                        <div style="display:flex; flex-direction:column; gap:18px;">
                            @foreach([
                                ['date' => now()->subDays(9)->translatedFormat('d F Y'), 'text' => 'Revisi proposal dan persetujuan dokumen final.'],
                                ['date' => now()->subDays(4)->translatedFormat('d F Y'), 'text' => 'Progress milestone 1 berhasil diselesaikan.'],
                                ['date' => now()->subDays(1)->translatedFormat('d F Y'), 'text' => 'Tim menambahkan pembaruan data perangkat laboratorium.']
                            ] as $activity)
                                <div style="display:flex; align-items:flex-start; gap:12px;">
                                    <div style="width:10px; height:10px; background:#2d2a28; border-radius:50%; margin-top:7px;"></div>
                                    <div>
                                        <div style="font-weight:700; color:#2d2827;">{{ $activity['date'] }}</div>
                                        <div style="color:#4d4845;">{{ $activity['text'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                <div class="sim-detail-side">
                    <aside class="sim-detail-side-box">
                        <div class="sim-detail-label">Tanggal Berikutnya</div>
                        <div class="sim-detail-next-date">{{ $nextDate }}</div>
                        <div style="font-size:14px; color:#4d4743;">Implementasi &amp; pengujian</div>
                        <div style="margin-top:14px; font-size:13px; color:#6f6a66; line-height:1.6;">Progress minggu ini diperkirakan mencapai {{ $milestone }}% dengan penjadwalan milestone yang sudah terverifikasi.</div>
                    </aside>

                    <aside class="sim-detail-side-box">
                        <div class="sim-detail-label">Dosen Pembimbing</div>
                        <div class="sim-detail-dosen-wrap">
                            <div class="sim-detail-avatar">{{ strtoupper(substr($dosenName,0,2)) }}</div>
                            <div>
                                <div class="sim-detail-dosen-name">{{ $dosenName }}</div>
                                <div class="sim-detail-dosen-nip">{{ $dosenNip }}</div>
                            </div>
                        </div>
                        <div style="font-size:13px; color:#625d59; line-height:1.7;">Pembimbing yang memantau progres kelompok, mengawal revisi proposal, serta mengecek kesiapan deliverable.</div>
                    </aside>
                </div>
            </div>
        </div>

        <div id="anggota" class="sim-detail-tab-panel" role="tabpanel">
            <div class="sim-detail-member-box">
                <div class="sim-detail-member-header">
                    <h2 class="sim-detail-member-title">Anggota Kelompok</h2>
                    <button type="button" class="sim-detail-add-btn" onclick="openAnggotaModal()">+ Tambah Anggota</button>
                </div>

                <div class="sim-detail-table-wrap">
                    <table class="sim-detail-table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Nama Mahasiswa</th>
                                <th style="width: 14%;">NIM</th>
                                <th style="width: 12%;">Kelas</th>
                                <th style="width: 18%;">Peran</th>
                                <th style="width: 26%;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $anggota)
                                @php
                                    $mahasiswa = $anggota->mahasiswa;
                                @endphp
                                <tr>
                                    <td>{{ $mahasiswa?->nama ?? 'Mahasiswa' }}</td>
                                    <td>{{ $mahasiswa?->nim ?? '-' }}</td>
                                    <td>{{ $kelas }}</td>
                                    <td>
                                        <span class="sim-detail-role {{ strtolower($anggota->role ?? 'anggota') == 'ketua' ? 'ketua' : 'anggota' }}">
                                            {{ strtolower($anggota->role ?? 'anggota') == 'ketua' ? 'Ketua' : 'Anggota' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="sim-detail-actions">
                                            <a href="#" class="sim-detail-action-link">Edit</a>
                                            <a href="#" class="sim-detail-action-link">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 18px 14px; color: #635d59; text-align: center;">Belum ada anggota dalam kelompok ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="milestone" class="sim-detail-tab-panel" role="tabpanel">
            <div class="sim-detail-milestone-box">
                <div class="sim-detail-milestone-top">
                    <h2 class="sim-detail-milestone-title">Progress milestone kelompok</h2>
                    <div class="sim-detail-milestone-summary">
                        <span>3 dari 5 milestone</span>
                        <span>•</span>
                        <span>60% selesai</span>
                    </div>
                </div>

                <div class="sim-detail-milestone-progress-wrap">
                    <div class="sim-detail-milestone-progress-label">
                        <span>60% tercapai</span>
                    </div>
                    <div class="sim-detail-milestone-progress-bar">
                        <span></span>
                    </div>
                </div>

                <div class="sim-detail-milestone-table-wrap">
                    <table class="sim-detail-milestone-table">
                        <thead>
                            <tr>
                                <th style="width: 22%;">Milestone Global</th>
                                <th style="width: 14%;">Tanggal Global</th>
                                <th style="width: 18%;">Penyelesaian Kelompok</th>
                                <th style="width: 18%;">Bukti / Catatan</th>
                                <th style="width: 14%;">Status</th>
                                <th style="width: 14%;">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $milestones = [
                                    ['name' => 'Pemenuhan tim & tonik', 'date' => '11 Sep 2026', 'progress' => 'Selesai', 'bukti' => 'Dokumen tim & topik', 'status' => 'Selesai', 'note' => 'Tepat waktu'],
                                    ['name' => 'Proposal & persetujuan mitra', 'date' => '16 Okt 2026', 'progress' => 'Selesai', 'bukti' => 'Proposal final', 'status' => 'Selesai', 'note' => 'Tepat waktu'],
                                    ['name' => 'Prototipe 1 & awal', 'date' => '9 Nov 2026', 'progress' => 'Selesai', 'bukti' => 'Catatan progress', 'status' => 'Selesai', 'note' => 'Tepat waktu'],
                                    ['name' => 'Implementasi & pengujian', 'date' => '4 Des 2026', 'progress' => 'Belum selesai', 'bukti' => 'Belum ada bukti', 'status' => 'Pending', 'note' => 'Belum waktunya'],
                                    ['name' => 'Presentasi & laporan akhir', 'date' => '8 Jan 2027', 'progress' => 'Belum selesai', 'bukti' => 'Belum ada bukti', 'status' => 'Pending', 'note' => 'Belum waktunya'],
                                ];
                            @endphp
                            @foreach ($milestones as $row)
                                <tr>
                                    <td>
                                        <div style="font-weight:700; color:#2d2927;">{{ $row['name'] }}</div>
                                        <div style="font-size:12px; color:#605a56; margin-top:4px;">{{ $row['date'] }}</div>
                                    </td>
                                    <td>{{ $row['date'] }}</td>
                                    <td>{{ $row['progress'] }}</td>
                                    <td>{{ $row['bukti'] }}</td>
                                    <td>
                                        <span class="sim-detail-status-chip {{ $row['status'] === 'Pending' ? 'sim-detail-status-chip--pending' : '' }}">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                    <td>{{ $row['note'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="anggotaModal" class="sim-anggota-modal-backdrop" aria-hidden="true">
    <div class="sim-anggota-modal" role="dialog" aria-modal="true" aria-labelledby="anggotaModalTitle">
        <div class="sim-anggota-modal-header">
            <h3 id="anggotaModalTitle" class="sim-anggota-modal-title">Tambah Anggota</h3>
            <button type="button" class="sim-anggota-modal-close" aria-label="Tutup" onclick="closeAnggotaModal()">×</button>
        </div>

        <form method="POST" action="{{ route('koordinator.kelompok.anggota.store', $kelompok->id) }}">
            @csrf
            <div class="sim-anggota-modal-body">
                <div class="sim-anggota-panel">
                    <div style="font-size:12px; letter-spacing:0.08em; text-transform:uppercase; color:#6a625d; font-weight:700; margin-bottom:8px;">Kapasitas Kelompok</div>
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; color:#2d2927; font-size:14px; font-weight:600;">
                        <span>{{ $members->count() }}/4 anggota</span>
                        <span style="color:#6d6560; font-size:12px;">Maksimal 4 anggota</span>
                    </div>
                </div>

                <div class="sim-anggota-row">
                    <div class="sim-anggota-field">
                        <label for="mahasiswa_id" class="sim-anggota-label">Pilih Mahasiswa</label>
                        <select id="mahasiswa_id" name="mahasiswa_id" class="sim-anggota-select" required>
                            <option value="">-- Pilih mahasiswa --</option>
                            @foreach($mahasiswaList ?? collect() as $mahasiswa)
                                <option value="{{ $mahasiswa->id }}">{{ $mahasiswa->nama }} • {{ $mahasiswa->nim }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sim-anggota-field">
                        <label for="role_label" class="sim-anggota-label">Peran</label>
                        <select id="role_label" name="role_label" class="sim-anggota-select">
                            <option value="Anggota">Anggota</option>
                            <option value="Ketua">Ketua</option>
                        </select>
                    </div>
                </div>

                @if ($errors->any())
                    <div style="margin-top: 16px; background: #fbeae8; border: 1px solid #efc4bd; color: #8a3d34; border-radius: 8px; padding: 10px 12px; font-size: 13px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="sim-anggota-footer">
                    <button type="button" class="sim-anggota-btn sim-anggota-btn--secondary" onclick="closeAnggotaModal()">Batal</button>
                    <button type="submit" class="sim-anggota-btn sim-anggota-btn--primary">Simpan Anggota</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="statusModal" class="sim-status-modal-backdrop" aria-hidden="true">
    <div class="sim-status-modal" role="dialog" aria-modal="true" aria-labelledby="statusModalTitle">
        <div class="sim-status-modal-header">
            <h2 id="statusModalTitle" class="sim-status-modal-title">Konfirmasi Ubah Status</h2>
            <button type="button" class="sim-status-modal-close" onclick="closeStatusModal()" aria-label="Tutup">×</button>
        </div>

        <div class="sim-status-modal-body">
            <div class="sim-status-modal-panel">
                <div class="sim-status-modal-grid">
                    <div class="sim-status-modal-field">
                        <div class="sim-status-modal-label">Kelompok</div>
                        <div class="sim-status-badge-box">{{ $projectName }}</div>
                    </div>
                    <div class="sim-status-modal-field">
                        <div class="sim-status-modal-label">Status saat ini</div>
                        <div class="sim-status-badge-box" id="statusCurrentBadge">{{ $statusKelompok }}</div>
                    </div>
                </div>
            </div>

            <div class="sim-status-modal-field">
                <label for="statusSelect" class="sim-status-modal-label">Ubah status menjadi</label>
                <select id="statusSelect" class="sim-status-select">
                    <option value="Aktif" {{ $statusKelompok === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Draft" {{ $statusKelompok === 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Ditangguhkan" {{ $statusKelompok === 'Ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                </select>
            </div>

            <div class="sim-status-note">
                Periksa kembali perubahan status sebelum menyimpannya. Status yang diubah akan langsung terlihat di daftar kelompok dan halaman detail.
            </div>
        </div>

        <div class="sim-status-modal-footer">
            <button type="button" class="sim-status-modal-btn sim-status-modal-btn--secondary" onclick="closeStatusModal()">Batal</button>
            <button type="button" class="sim-status-modal-btn sim-status-modal-btn--primary" onclick="applyStatusChange()">Ubah Status</button>
        </div>
    </div>
</div>

<script>
    function openStatusModal() {
        const modal = document.getElementById('statusModal');
        if (modal) modal.classList.add('open');
    }

    function closeStatusModal() {
        const modal = document.getElementById('statusModal');
        if (modal) modal.classList.remove('open');
    }

    function applyStatusChange() {
        const select = document.getElementById('statusSelect');
        const badge = document.getElementById('statusCurrentBadge');
        const newStatus = select ? select.value : 'Aktif';

        if (badge) {
            badge.textContent = newStatus;
        }

        const detailBadge = document.querySelector('.sim-detail-badge');
        if (detailBadge) {
            detailBadge.textContent = newStatus;
        }

        closeStatusModal();
    }

    function openAnggotaModal() {
        const modal = document.getElementById('anggotaModal');
        if (modal) {
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
        }
    }

    function closeAnggotaModal() {
        const modal = document.getElementById('anggotaModal');
        if (modal) {
            modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.sim-detail-tab');
        const panels = document.querySelectorAll('.sim-detail-tab-panel');

        tabs.forEach((tab) => {
            tab.addEventListener('click', function () {
                const target = this.dataset.target;

                tabs.forEach((item) => {
                    const isActive = item === tab;
                    item.classList.toggle('active', isActive);
                    item.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                panels.forEach((panel) => {
                    panel.classList.toggle('active', panel.id === target);
                });
            });
        });

        const modalBackdrop = document.getElementById('anggotaModal');
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', function (event) {
                if (event.target === modalBackdrop) {
                    closeAnggotaModal();
                }
            });
        }

        const statusBackdrop = document.getElementById('statusModal');
        if (statusBackdrop) {
            statusBackdrop.addEventListener('click', function (event) {
                if (event.target === statusBackdrop) {
                    closeStatusModal();
                }
            });
        }
    });
</script>
@endsection
