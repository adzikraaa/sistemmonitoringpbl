@extends('layouts.koordinator')

@section('title', 'Kelompok PBL')
@section('breadcrumb_title', 'Kelompok PBL')

@section('content')
<style>
    .sim-kelompok-panel {
        max-width: 1180px;
        margin: 0 auto;
    }

    .sim-kelompok-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .sim-kelompok-title {
        margin: 0;
        font-size: 42px;
        line-height: 1.1;
        letter-spacing: -0.05em;
        font-weight: 800;
        color: #2d2724;
        font-family: Georgia, 'Times New Roman', serif;
    }

    .sim-kelompok-subtitle {
        margin-top: 8px;
        color: #756d68;
        font-size: 14px;
    }

    .sim-kelompok-create-btn {
        border: 1px solid #d3c4b2;
        background: #3d312d;
        color: #fff;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.04);
    }

    .sim-kelompok-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(35, 28, 25, 0.25);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .sim-kelompok-modal-backdrop.open {
        display: flex;
    }

    .sim-kelompok-modal {
        width: min(760px, calc(100vw - 40px));
        background: #f3efe8;
        border: 1px solid #d8c7b3;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(38,31,28,0.12);
    }

    .sim-kelompok-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 20px 12px;
        border-bottom: 1px solid #e0d6c8;
    }

    .sim-kelompok-modal-title {
        font-size: 14px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #4f4741;
        font-weight: 700;
    }

    .sim-kelompok-modal-close {
        border: none;
        background: transparent;
        font-size: 26px;
        line-height: 1;
        cursor: pointer;
        color: #544d49;
    }

    .sim-kelompok-modal-body {
        padding: 18px 20px 10px;
    }

    .sim-kelompok-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px 18px;
    }

    .sim-kelompok-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .sim-kelompok-form-group.full {
        grid-column: 1 / -1;
    }

    .sim-kelompok-label {
        font-size: 12px;
        color: #5a514b;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .sim-kelompok-input,
    .sim-kelompok-select,
    .sim-kelompok-textarea {
        width: 100%;
        border: 1px solid #d7cab9;
        border-radius: 8px;
        background: rgba(255,255,255,0.35);
        color: #2b2927;
        padding: 10px 12px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .sim-kelompok-textarea {
        min-height: 90px;
        resize: vertical;
    }

    .sim-kelompok-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 20px 20px;
    }

    .sim-kelompok-btn-secondary,
    .sim-kelompok-btn-primary {
        border-radius: 8px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .sim-kelompok-btn-secondary {
        background: transparent;
        color: #514b48;
        border: 1px solid #d7cab9;
    }

    .sim-kelompok-btn-primary {
        background: #3b2d26;
        color: #fff;
        border: 1px solid #3b2d26;
    }

    .sim-kelompok-filter-wrap {
        background: transparent;
        border: none;
        border-radius: 0;
        padding: 0;
        margin: 0 0 18px;
    }

    .sim-kelompok-filters {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr) 180px;
        gap: 12px;
        align-items: center;
        width: 100%;
    }

    .sim-kelompok-select {
        appearance: none;
        background-image: linear-gradient(45deg, transparent 50%, #4a4039 50%), linear-gradient(135deg, #4a4039 50%, transparent 50%);
        background-position: calc(100% - 16px) calc(50% - 2px), calc(100% - 11px) calc(50% - 2px);
        background-size: 5px 5px, 5px 5px;
        background-repeat: no-repeat;
        min-width: 0;
        width: 100%;
    }

    .sim-kelompok-search {
        flex: none;
        min-width: 0;
        width: 100%;
    }

    .sim-kelompok-table-wrap {
        background: rgba(255,255,255,0.18);
        border: 1px solid #d8cdbd;
        border-radius: 12px;
        overflow: hidden;
    }

    .sim-kelompok-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .sim-kelompok-table thead th {
        background: #e7e0d6;
        color: #5f564f;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 11px;
        text-align: left;
        padding: 15px 14px;
        border-bottom: 1px solid #d8cab7;
    }

    .sim-kelompok-table tbody td {
        padding: 18px 14px;
        border-bottom: 1px solid #e3d9ca;
        vertical-align: middle;
        font-size: 14px;
        color: #2b2927;
    }

    .sim-kelompok-table tbody tr:hover {
        background: rgba(255,255,255,0.12);
    }

    .sim-kelompok-code {
        font-size: 11px;
        color: #726b65;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
    }

    .sim-kelompok-name {
        font-size: 20px;
        font-weight: 700;
        color: #2b2927;
        line-height: 1.3;
        font-family: Georgia, 'Times New Roman', serif;
    }

    .sim-kelompok-people {
        color: #4d4743;
        font-size: 14px;
        line-height: 1.5;
    }

    .sim-kelompok-dosen {
        color: #4d4743;
        font-size: 14px;
        line-height: 1.5;
        font-weight: 600;
    }

    .sim-kelompok-milestone {
        min-width: 150px;
    }

    .sim-kelompok-progress {
        position: relative;
        width: 100%;
        height: 8px;
        border-radius: 999px;
        background: #dbd2c8;
        overflow: hidden;
        margin-bottom: 6px;
    }

    .sim-kelompok-progress span {
        position: absolute;
        inset: 0 auto 0 0;
        border-radius: 999px;
        background: #8d7f72;
    }

    .sim-kelompok-progress-note {
        color: #5c5652;
        font-size: 12px;
        display: block;
    }

    .sim-kelompok-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border: 1px solid transparent;
        line-height: 1.2;
        white-space: nowrap;
    }

    .sim-kelompok-status.active {
        background: #edf7ef;
        color: #2f7a5b;
        border-color: #cfe6d7;
    }

    .sim-kelompok-status.draft {
        background: #f8efe5;
        color: #bf7f39;
        border-color: #efd9b8;
    }

    .sim-kelompok-status.pending {
        background: #f9efe7;
        color: #926127;
        border-color: #ebd3a9;
    }

    .sim-kelompok-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        white-space: nowrap;
        flex-wrap: nowrap;
    }

    .sim-kelompok-detail {
        color: #2d2724;
        font-weight: 700;
        text-decoration: none;
        font-size: 12px;
        line-height: 1.2;
        padding: 5px 0;
    }

    .sim-kelompok-action-select {
        border: 1px solid #d4c4af;
        background: rgba(255,255,255,0.45);
        color: #4a4039;
        padding: 7px 8px;
        border-radius: 8px;
        font-size: 11px;
        min-width: 92px;
        height: 30px;
    }

    .sim-kelompok-action-btn {
        border: 1px solid #d4c4af;
        background: rgba(255,255,255,0.45);
        color: #4a4039;
        padding: 7px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        min-width: 92px;
        height: 30px;
    }

    .sim-kelompok-status-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(31, 25, 22, 0.28);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
        padding: 20px;
    }

    .sim-kelompok-status-backdrop.open {
        display: flex;
    }

    .sim-kelompok-status-modal {
        width: min(620px, calc(100vw - 40px));
        background: #f4efe9;
        border: 1px solid #d9cab6;
        border-radius: 14px;
        box-shadow: 0 24px 60px rgba(34, 28, 24, 0.12);
        overflow: hidden;
    }

    .sim-kelompok-status-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px 14px;
        border-bottom: 1px solid #ddd0c1;
    }

    .sim-kelompok-status-title {
        margin: 0;
        font-size: 22px;
        font-family: Georgia, 'Times New Roman', serif;
        font-weight: 800;
        color: #2d2927;
    }

    .sim-kelompok-status-close {
        border: none;
        background: transparent;
        color: #544d49;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
    }

    .sim-kelompok-status-body {
        padding: 18px 20px 10px;
    }

    .sim-kelompok-status-panel {
        border: 1px solid #d8cbb9;
        border-radius: 12px;
        background: rgba(255,255,255,0.18);
        padding: 14px 14px 12px;
        margin-bottom: 16px;
    }

    .sim-kelompok-status-row {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
        gap: 14px 18px;
        align-items: stretch;
    }

    .sim-kelompok-status-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    .sim-kelompok-status-label {
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #69615d;
        font-weight: 700;
    }

    .sim-kelompok-status-value {
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        min-height: 38px;
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

    .sim-kelompok-status-select {
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

    .sim-kelompok-status-note {
        margin-top: 4px;
        padding: 12px 14px;
        background: #f7e7d7;
        border: 1px solid #e7c6a1;
        border-radius: 10px;
        color: #7a562c;
        font-size: 13px;
        line-height: 1.5;
    }

    .sim-kelompok-status-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 14px 20px 18px;
    }

    .sim-kelompok-status-btn {
        border-radius: 9px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .sim-kelompok-status-btn--secondary {
        background: transparent;
        border: 1px solid #d8cbb9;
        color: #413a36;
    }

    .sim-kelompok-status-btn--primary {
        background: #3d312d;
        border: 1px solid #3d312d;
        color: #fff;
    }

    .sim-kelompok-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #706962;
        font-size: 12px;
        padding: 18px 10px 0;
    }

    .sim-kelompok-pagination {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sim-kelompok-pagination .page-btn {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        border: 1px solid #d2c5b5;
        background: transparent;
        color: #4a4039;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .sim-kelompok-pagination .page-btn.current {
        background: #2d2927;
        color: #fff;
        border-color: #2d2927;
    }

    @media (max-width: 980px) {
        .sim-kelompok-table-wrap {
            overflow-x: auto;
        }

        .sim-kelompok-table {
            min-width: 900px;
        }
    }
</style>

<div class="sim-kelompok-page">
    <div class="sim-kelompok-panel">
        <div class="sim-kelompok-header">
            <div>
                <div class="sim-kelompok-subtitle" style="margin:0 0 10px;">SIM-PBL / ADMINISTRASI PROGRAM</div>
                <h1 class="sim-kelompok-title">Kelompok PBL</h1>
                <div class="sim-kelompok-subtitle">Kelola proyek, rekan kerja, dan pembimbing kelompok PBL sesuai status aktif.</div>
            </div>
            <button type="button" class="sim-kelompok-create-btn" onclick="openKelompokModal()">+ Buat Kelompok</button>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 14px; background: #edf7ee; border: 1px solid #cfe7d4; color: #2d6a3d; padding: 10px 12px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif

        <div class="sim-kelompok-filter-wrap">
            <div class="sim-kelompok-filters">
                <select class="sim-kelompok-select" aria-label="Filter kelas">
                    <option>Kelas: SIB 3C</option>
                </select>
                <input type="text" class="sim-kelompok-search sim-kelompok-input" placeholder="Cari kelompok, proyek, mahasiswa..." aria-label="Cari kelompok" />
                <select class="sim-kelompok-select" aria-label="Filter status">
                    <option>Semua status</option>
                </select>
            </div>
        </div>

        <div class="sim-kelompok-table-wrap">
            <table class="sim-kelompok-table">
                <thead>
                    <tr>
                        <th style="width: 32%;">Kelompok / Proyek</th>
                        <th style="width: 17%;">Anggota</th>
                        <th style="width: 18%;">Dosen Pembimbing</th>
                        <th style="width: 15%;">Milestone</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 12%;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelompokList as $item)
                        @php
                            $memberNames = $item->anggotaKelompok->take(2)->map(fn($anggota) => $anggota->mahasiswa?->nama ?? 'Mahasiswa')->all();
                            $memberCount = $item->anggotaKelompok->count();
                            $milestoneCount = $item->milestoneKelompok->count();
                            $milestonePercent = $milestoneCount > 0 ? min(100, (int) (($milestoneCount / 5) * 100)) : 20;
                            $status = $item->dosen ? 'Aktif' : 'Draft';
                            $statusClass = $item->dosen ? 'active' : 'draft';
                        @endphp
                        <tr>
                            <td>
                                <span class="sim-kelompok-code">{{ strtoupper($item->kelas?->nama_kelas ?? 'PBL') }} • {{ $item->periode?->nama_periode ?? '2026/2027' }}</span>
                                <div class="sim-kelompok-name">{{ $item->nama_kelompok }}</div>
                            </td>
                            <td>
                                <div class="sim-kelompok-people">
                                    {{ $memberCount ?: 0 }} mahasiswa<br>
                                    <span style="color:#5d5551;">{{ implode(', ', $memberNames) ?: 'Belum ada anggota' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="sim-kelompok-dosen">{{ $item->dosen?->nama ?? 'Belum ditugaskan' }}</div>
                            </td>
                            <td class="sim-kelompok-milestone">
                                <div class="sim-kelompok-progress"><span style="width: {{ $milestonePercent }}%"></span></div>
                                <span class="sim-kelompok-progress-note">{{ $milestoneCount }}/5 selesai • {{ $milestonePercent }}%</span>
                            </td>
                            <td>
                                <span class="sim-kelompok-status {{ $statusClass }}" data-status-chip="{{ $item->id }}">{{ $status }}</span>
                            </td>
                            <td>
                                <div class="sim-kelompok-actions">
                                    <a class="sim-kelompok-detail" href="{{ route('koordinator.kelompok.show', $item->id) }}">Detail →</a>
                                    <button type="button" class="sim-kelompok-action-btn" data-status-toggle="open" data-kelompok-id="{{ $item->id }}" data-kelompok-name="{{ $item->nama_kelompok }}" data-current-status="{{ $status }}" aria-label="Ubah status {{ $item->nama_kelompok }}">Ubah Status</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 32px 14px; color: #665f59; text-align: center;">Belum ada kelompok PBL yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sim-kelompok-footer">
            <div>Menampilkan {{ $kelompokList->count() }} kelompok</div>
            <div class="sim-kelompok-pagination">
                <button type="button" class="page-btn">‹</button>
                <button type="button" class="page-btn current">1</button>
                <button type="button" class="page-btn">2</button>
                <button type="button" class="page-btn">3</button>
                <button type="button" class="page-btn">›</button>
            </div>
        </div>
    </div>
</div>

<div id="kelompokModal" class="sim-kelompok-modal-backdrop" aria-hidden="true">
    <div class="sim-kelompok-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title-kelompok">
        <div class="sim-kelompok-modal-header">
            <div id="modal-title-kelompok" class="sim-kelompok-modal-title">Buat Kelompok</div>
            <button type="button" class="sim-kelompok-modal-close" onclick="closeKelompokModal()" aria-label="Tutup">×</button>
        </div>

        <form method="POST" action="{{ route('koordinator.kelompok.store') }}">
            @csrf
            <div class="sim-kelompok-modal-body">
                <div class="sim-kelompok-form-grid">
                    <div class="sim-kelompok-form-group full">
                        <label for="nama_kelompok" class="sim-kelompok-label">Nama Kelompok</label>
                        <input id="nama_kelompok" name="nama_kelompok" class="sim-kelompok-input" type="text" placeholder="Kelompok 1" required>
                    </div>

                    <div class="sim-kelompok-form-group full">
                        <label for="judul_proyek" class="sim-kelompok-label">Judul Proyek</label>
                        <input id="judul_proyek" name="judul_proyek" class="sim-kelompok-input" type="text" placeholder="Sistem Inventaris Laboratorium">
                    </div>

                    <div class="sim-kelompok-form-group">
                        <label for="kelas_id" class="sim-kelompok-label">Kelas</label>
                        <select id="kelas_id" name="kelas_id" class="sim-kelompok-select" required>
                            <option value="">Pilih kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sim-kelompok-form-group">
                        <label for="periode_id" class="sim-kelompok-label">Semester / Periode</label>
                        <select id="periode_id" name="periode_id" class="sim-kelompok-select" required>
                            <option value="">Pilih periode</option>
                            @foreach($periodeList as $periode)
                                <option value="{{ $periode->id }}">{{ $periode->nama_periode }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sim-kelompok-form-group full">
                        <label for="dosen_id" class="sim-kelompok-label">Dosen Pembimbing</label>
                        <select id="dosen_id" name="dosen_id" class="sim-kelompok-select">
                            <option value="">Belum ditugaskan</option>
                            @foreach($dosenList as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sim-kelompok-form-group full">
                        <label for="jadwal_bimbingan" class="sim-kelompok-label">Jadwal Bimbingan</label>
                        <textarea id="jadwal_bimbingan" name="jadwal_bimbingan" class="sim-kelompok-textarea" placeholder="Contoh: Senin, 13.00 - 15.00"></textarea>
                    </div>
                </div>
            </div>

            <div class="sim-kelompok-modal-footer">
                <button type="button" class="sim-kelompok-btn-secondary" onclick="closeKelompokModal()">Batal</button>
                <button type="submit" class="sim-kelompok-btn-primary">Buat Kelompok</button>
            </div>
        </form>
    </div>
</div>

<div id="kelompokStatusModal" class="sim-kelompok-status-backdrop" aria-hidden="true">
    <div class="sim-kelompok-status-modal" role="dialog" aria-modal="true" aria-labelledby="statusModalTitleKelompok">
        <div class="sim-kelompok-status-header">
            <h2 id="statusModalTitleKelompok" class="sim-kelompok-status-title">Konfirmasi Ubah Status</h2>
            <button type="button" class="sim-kelompok-status-close" onclick="closeKelompokStatusModal()" aria-label="Tutup">×</button>
        </div>

        <div class="sim-kelompok-status-body">
            <div class="sim-kelompok-status-panel">
                <div class="sim-kelompok-status-row">
                    <div class="sim-kelompok-status-field">
                        <div class="sim-kelompok-status-label">Kelompok</div>
                        <div class="sim-kelompok-status-value" id="statusKelompokName">-</div>
                    </div>
                    <div class="sim-kelompok-status-field">
                        <div class="sim-kelompok-status-label">Status saat ini</div>
                        <div class="sim-kelompok-status-value" id="statusKelompokCurrent">Aktif</div>
                    </div>
                </div>
            </div>

            <div class="sim-kelompok-status-field">
                <label for="statusKelompokSelect" class="sim-kelompok-status-label">Ubah status menjadi</label>
                <select id="statusKelompokSelect" class="sim-kelompok-status-select">
                    <option value="Aktif">Aktif</option>
                    <option value="Draft">Draft</option>
                    <option value="Ditangguhkan">Ditangguhkan</option>
                </select>
            </div>

            <div class="sim-kelompok-status-note">
                Periksa kembali perubahan status sebelum menyimpannya. Status yang diubah akan langsung terlihat di daftar kelompok.
            </div>
        </div>

        <div class="sim-kelompok-status-footer">
            <button type="button" class="sim-kelompok-status-btn sim-kelompok-status-btn--secondary" onclick="closeKelompokStatusModal()">Batal</button>
            <button type="button" id="confirmKelompokStatus" class="sim-kelompok-status-btn sim-kelompok-status-btn--primary">Ubah Status</button>
        </div>
    </div>
</div>

<script>
    function openKelompokModal() {
        const modal = document.getElementById('kelompokModal');
        if (modal) modal.classList.add('open');
    }

    function closeKelompokModal() {
        const modal = document.getElementById('kelompokModal');
        if (modal) modal.classList.remove('open');
    }

    function openKelompokStatusModal(button) {
        const modal = document.getElementById('kelompokStatusModal');
        const nameEl = document.getElementById('statusKelompokName');
        const currentEl = document.getElementById('statusKelompokCurrent');
        const selectEl = document.getElementById('statusKelompokSelect');

        if (!modal || !button) return;

        const groupId = button.dataset.kelompokId;
        const groupName = button.dataset.kelompokName || 'Kelompok';
        const currentStatus = button.dataset.currentStatus || 'Aktif';

        nameEl.textContent = groupName;
        currentEl.textContent = currentStatus;
        selectEl.value = currentStatus;
        modal.dataset.kelompokId = groupId;
        modal.classList.add('open');
    }

    function closeKelompokStatusModal() {
        const modal = document.getElementById('kelompokStatusModal');
        if (modal) modal.classList.remove('open');
    }

    document.querySelectorAll('[data-status-toggle="open"]').forEach((button) => {
        button.addEventListener('click', function () {
            openKelompokStatusModal(this);
        });
    });

    document.getElementById('confirmKelompokStatus')?.addEventListener('click', function () {
        const modal = document.getElementById('kelompokStatusModal');
        const selectEl = document.getElementById('statusKelompokSelect');
        const status = selectEl ? selectEl.value : 'Aktif';
        const groupId = modal ? modal.dataset.kelompokId : null;
        const chip = groupId ? document.querySelector('[data-status-chip="' + groupId + '"]') : null;

        if (chip) {
            chip.textContent = status;
            chip.classList.remove('active', 'draft', 'pending');
            chip.classList.add(status === 'Aktif' ? 'active' : (status === 'Draft' ? 'draft' : 'pending'));
        }

        if (modal) {
            modal.classList.remove('open');
        }
    });

    document.addEventListener('click', function (event) {
        const modal = document.getElementById('kelompokModal');
        if (event.target === modal) {
            closeKelompokModal();
        }

        const statusModal = document.getElementById('kelompokStatusModal');
        if (event.target === statusModal) {
            closeKelompokStatusModal();
        }
    });
</script>
@endsection
