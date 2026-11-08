@extends('layouts.koordinator')

@section('title', 'Semester & Periode — SIM-PBL')
@section('breadcrumb_title', 'Semester & Periode')

@push('styles')
<style>
    /* ── Typography & Base Tokens (Sesuai Standar Figma SIM-PBL) ── */
    .figma-title-eyebrow {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--sim-text-light);
        margin: 0 0 6px 0;
    }

    .figma-title-h1 {
        font-family: 'DM Serif Display', Georgia, serif;
        font-size: 38px;
        font-weight: 400;
        color: var(--sim-brown-dark);
        margin: 0 0 6px 0;
        line-height: 1.15;
    }

    .figma-title-desc {
        font-size: 13.5px;
        color: var(--sim-text-muted);
        margin: 0;
        line-height: 1.5;
    }

    /* ── Buttons ── */
    .btn-figma-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--sim-brown-btn);
        color: #FAF8F5;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, transform 0.1s;
        white-space: nowrap;
    }
    .btn-figma-primary:hover {
        background: var(--sim-brown-btn-hover);
    }
    .btn-figma-primary:active {
        transform: scale(0.99);
    }

    .btn-figma-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #FFFFFF;
        color: var(--sim-brown-dark);
        border: 1px solid var(--sim-border-input);
        border-radius: 8px;
        padding: 9px 20px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s;
    }
    .btn-figma-outline:hover {
        background: #F8F5F0;
        border-color: #C8BFB4;
    }

    /* ── Cards & Grid System ── */
    .figma-bottom-grid {
        display: grid;
        grid-template-columns: 1.65fr 1fr;
        gap: 24px;
        align-items: stretch;
    }

    .figma-card {
        background: #FFFFFF;
        border: 1px solid var(--sim-border-warm);
        border-radius: 16px;
        padding: 28px 32px;
        box-shadow: 0 1px 3px rgba(44, 26, 14, 0.03);
        box-sizing: border-box;
    }

    .figma-card-title {
        font-family: 'DM Serif Display', Georgia, serif;
        font-size: 24px;
        font-weight: 400;
        color: var(--sim-brown-dark);
        margin: 0 0 4px 0;
    }
    .figma-card-subtitle {
        font-size: 12.5px;
        color: var(--sim-text-muted);
        margin: 0 0 24px 0;
    }

    .figma-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    .figma-form-group {
        margin-bottom: 16px;
    }
    .figma-form-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--sim-brown-dark);
        margin-bottom: 7px;
    }
    .figma-input, .figma-input-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--sim-border-input);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: 'Inter', sans-serif;
        color: var(--sim-brown-dark);
        background: #FFFFFF;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.15s;
    }
    .figma-input:focus, .figma-input-select:focus {
        border-color: var(--sim-brown-dark);
    }

    /* ── Status Badges ── */
    .figma-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
    }
    .figma-status-badge--active {
        background: var(--sim-green-badge-bg);
        color: var(--sim-green-badge-text);
    }
    .figma-status-badge--inactive {
        background: #FDF2F2;
        color: #9B1C1C;
    }
    .figma-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .figma-status-badge--active .figma-status-dot { background: var(--sim-green-badge-dot); }
    .figma-status-badge--inactive .figma-status-dot { background: #DC2626; }

    /* ── Table Card ── */
    .figma-table-card {
        background: #FFFFFF;
        border: 1px solid var(--sim-border-warm);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(44, 26, 14, 0.03);
        margin-top: 28px;
    }
    .figma-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }
    .figma-table thead tr {
        background: var(--sim-cream-head);
        border-bottom: 1px solid var(--sim-border-warm);
    }
    .figma-table th {
        padding: 12px 20px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--sim-text-muted);
    }
    .figma-table th.col-aksi {
        text-align: right;
        padding-right: 28px;
    }
    .figma-table tbody tr {
        border-bottom: 1px solid #EFEAE2;
        transition: background 0.12s ease;
    }
    .figma-table tbody tr:last-child {
        border-bottom: none;
    }
    .figma-table tbody tr:hover {
        background: #FCFAF6;
    }
    .figma-table td {
        padding: 18px 20px;
        vertical-align: middle;
        color: var(--sim-brown-dark);
    }
    .figma-table td.col-aksi {
        text-align: right;
        padding-right: 28px;
    }

    /* Flash Alerts */
    .figma-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 24px;
    }
    .figma-alert--success {
        background: var(--sim-green-badge-bg);
        color: var(--sim-green-badge-text);
        border: 1px solid #D1E5CF;
    }

    /* Modal Styling */
    .figma-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(43, 34, 29, 0.45);
        backdrop-filter: blur(2px);
        z-index: 300;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .figma-modal-overlay.open {
        display: flex;
    }
    .figma-modal {
        background: #FFFFFF;
        border-radius: 16px;
        padding: 32px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 20px 48px rgba(34, 23, 16, 0.18);
    }

    @media (max-width: 992px) {
        .figma-bottom-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

{{-- Flash Message Success --}}
@if(session('success'))
<div class="figma-alert figma-alert--success">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

{{-- ═══════════ 1. Judul Halaman Standar Figma ═══════════ --}}
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px;">
    <div>
        <p class="figma-title-eyebrow">SIM-PBL / ADMINISTRASI PROGRAM</p>
        <h1 class="figma-title-h1">Semester & Periode</h1>
        <p class="figma-title-desc">Atur kalender pelaksanaan dan semester acuan untuk seluruh aktivitas PBL.</p>
    </div>

    <button type="button" class="btn-figma-primary" onclick="openModalTambah()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>Tambah semester</span>
    </button>
</div>

{{-- ═══════════ 2. Grid 2 Kolom (Pengaturan Semester & Sidebar Info) ═══════════ --}}
<div class="figma-bottom-grid">
    
    <!-- KOLOM KIRI: Pengaturan Semester Berjalan -->
    <div class="figma-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
            <h2 class="figma-card-title">Pengaturan semester berjalan</h2>
            @if($periodeAktif && $periodeAktif->is_aktif)
                <span class="figma-status-badge figma-status-badge--active">
                    <span class="figma-status-dot"></span>
                    <span>Aktif</span>
                </span>
            @else
                <span class="figma-status-badge figma-status-badge--inactive">
                    <span class="figma-status-dot"></span>
                    <span>Nonaktif</span>
                </span>
            @endif
        </div>
        <p class="figma-card-subtitle">Perubahan berlaku untuk seluruh kelas, kelompok, dan dosen.</p>

        @if($periodeAktif)
        <form action="{{ route('koordinator.periode.update', $periodeAktif->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-light); text-transform: uppercase; margin-bottom: 12px;">
                SEMESTER UTAMA
            </div>

            <div class="figma-form-row">
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label">Nama semester</label>
                    <input type="text" name="nama_periode" value="{{ old('nama_periode', $periodeAktif->nama_periode) }}" class="figma-input" required>
                </div>
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label">Tahun akademik</label>
                    <select name="tahun_akademik" class="figma-input-select">
                        <option value="2026/2027" {{ $periodeAktif->tahun_akademik == '2026/2027' ? 'selected' : '' }}>2026/2027</option>
                        <option value="2025/2026" {{ $periodeAktif->tahun_akademik == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                    </select>
                </div>
            </div>

            <div class="figma-form-row">
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label">Tanggal mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $periodeAktif->tanggal_mulai->format('Y-m-d')) }}" class="figma-input" required>
                </div>
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label">Tanggal selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $periodeAktif->tanggal_selesai->format('Y-m-d')) }}" class="figma-input" required>
                </div>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label">Status periode</label>
                <select class="figma-input-select">
                    <option value="aktif">Aktif — menerima aktivitas PBL</option>
                </select>
            </div>

            <!-- Switch Status Aktif -->
            <div style="background: var(--sim-cream-head); border: 1px solid var(--sim-border-warm); border-radius: 12px; padding: 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <div>
                    <h4 style="font-size: 13px; font-weight: 700; color: var(--sim-brown-dark); margin: 0 0 2px 0;">Jadikan semester aktif</h4>
                    <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0;">Menjadi acuan default seluruh ruang kerja.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_aktif" value="1" class="sr-only peer" {{ $periodeAktif->is_aktif ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[var(--sim-brown-btn)]"></div>
                </label>
            </div>

            <!-- Warning Notice -->
            <div style="background: #FAF8F5; border: 1px solid var(--sim-border-warm); border-radius: 12px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; margin-bottom: 24px;">
                <span style="color: var(--sim-text-muted); font-size: 14px;">🛡️</span>
                <div>
                    <h5 style="font-size: 12.5px; font-weight: 600; color: var(--sim-brown-dark); margin: 0 0 2px 0;">Tidak ada periode aktif yang tumpang tindih</h5>
                    <p style="font-size: 11.5px; color: var(--sim-text-muted); margin: 0;">Rentang tanggal valid. Sistem menolak aktivasi jika beririsan dengan semester lain yang masih aktif.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 16px; border-top: 1px solid var(--sim-border-warm);">
                <button type="button" style="color: #DC2626; background: none; border: none; font-size: 13px; font-weight: 500; cursor: pointer; text-decoration: underline;">
                    Nonaktifkan semester
                </button>

                <div style="display: flex; items-center; gap: 10px;">
                    <button type="reset" class="btn-figma-outline">Batal</button>
                    <button type="submit" class="btn-figma-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Simpan perubahan</span>
                    </button>
                </div>
            </div>
        </form>
        @endif
    </div>

    <!-- KOLOM KANAN: Sidebar Info Satu Periode Satu Acuan -->
    <div style="display: flex; flex-direction: column; gap: 18px;">
        <div class="figma-card" style="padding: 24px;">
            <h3 style="font-family: 'DM Serif Display', Georgia, serif; font-size: 20px; color: var(--sim-brown-dark); margin: 0 0 8px 0;">Satu periode, satu acuan</h3>
            <p style="font-size: 12.5px; color: var(--sim-text-muted); line-height: 1.5; margin: 0 0 18px 0;">
                Aktivasi semester mengubah filter default dashboard, penugasan dosen, kelompok PBL, dan jadwal milestone global.
            </p>

            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px; color: var(--sim-brown-dark); border-top: 1px solid var(--sim-border-warm); padding-top: 16px;">
                <div><span style="color: var(--sim-text-muted); margin-right: 6px;">•</span><strong>{{ $periodeAktif ? $periodeAktif->kelas_count : 3 }}</strong> kelas terhubung</div>
                <div><span style="color: var(--sim-text-muted); margin-right: 6px;">•</span><strong>{{ $periodeAktif ? ($periodeAktif->kelompok_count * 4) : 72 }}</strong> mahasiswa</div>
                <div><span style="color: var(--sim-text-muted); margin-right: 6px;">•</span><strong>{{ $periodeAktif ? $periodeAktif->milestone_count : 5 }}</strong> milestone global</div>
            </div>
        </div>

        <div style="background: var(--sim-cream-head); border: 1px solid var(--sim-border-warm); border-radius: 16px; padding: 20px; font-size: 12.5px; color: var(--sim-text-body);">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--sim-brown-dark); margin-bottom: 4px;">
                <span>🛡️</span>
                <span>Sebelum menonaktifkan</span>
            </div>
            <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0; line-height: 1.5; padding-left: 24px;">
                Pastikan aktivitas PBL sudah ditinjau. Data dan riwayat semester tetap tersimpan; aktivitas baru tidak dapat ditambahkan.
            </p>
        </div>
    </div>

</div>

{{-- ═══════════ 3. Tabel Riwayat Semester ═══════════ --}}
<div class="figma-table-card">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 16px 24px; border-bottom: 1px solid var(--sim-border-warm);">
        <h2 style="font-family: 'DM Serif Display', Georgia, serif; font-size: 22px; color: var(--sim-brown-dark); margin: 0;">Riwayat semester</h2>
        <span style="font-size: 12.5px; color: var(--sim-text-muted);">{{ $riwayatPeriode->count() }} periode akademik</span>
    </div>

    <table class="figma-table">
        <thead>
            <tr>
                <th style="width: 25%;">SEMESTER</th>
                <th style="width: 30%;">PERIODE PELAKSANAAN</th>
                <th style="width: 15%;">STATUS</th>
                <th style="width: 18%;">KELAS / KELOMPOK</th>
                <th class="col-aksi" style="width: 12%;">TINDAKAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayatPeriode as $row)
                <tr>
                    <td style="font-weight: 700; color: var(--sim-brown-dark);">
                        {{ $row->nama_periode }} {{ $row->tahun_akademik }}
                    </td>
                    <td style="color: var(--sim-text-muted);">
                        {{ $row->tanggal_mulai->format('d M Y') }} – {{ $row->tanggal_selesai->format('d M Y') }}
                    </td>
                    <td>
                        @if($row->is_aktif)
                            <span class="figma-status-badge figma-status-badge--active">
                                <span class="figma-status-dot"></span>
                                <span>Aktif</span>
                            </span>
                        @else
                            <span class="figma-status-badge figma-status-badge--inactive">
                                <span class="figma-status-dot"></span>
                                <span>Nonaktif</span>
                            </span>
                        @endif
                    </td>
                    <td style="color: var(--sim-text-muted);">
                        {{ $row->kelas_count }} kelas / {{ $row->kelompok_count }} kelompok
                    </td>
                    <td class="col-aksi">
                        @if(!$row->is_aktif)
                            <form action="{{ route('koordinator.periode.toggle-aktif', $row->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background: none; border: none; color: var(--sim-brown-btn); font-weight: 600; font-size: 13px; cursor: pointer; text-decoration: underline;">
                                    Aktifkan
                                </button>
                            </form>
                        @else
                            <span style="color: var(--sim-text-light); font-size: 12.5px;">Edit periode</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ═══════════ Modal Tambah Semester ═══════════ --}}
<div id="modal-tambah-semester" class="figma-modal-overlay">
    <div class="figma-modal" role="dialog" aria-modal="true">
        <h3 style="font-family: 'DM Serif Display', Georgia, serif; font-size: 24px; font-weight: 400; color: var(--sim-brown-dark); margin: 0 0 6px 0;">
            Tambah Semester Baru
        </h3>
        <p style="font-size: 13px; color: var(--sim-text-muted); margin: 0 0 20px 0;">
            Buat semester baru untuk kalender akademik PBL.
        </p>

        <!-- Tambahkan id="form-tambah-semester" pada tag form -->
        <form id="form-tambah-semester" action="{{ route('koordinator.periode.store') }}" method="POST">
            @csrf

            <div class="figma-form-group">
                <label class="figma-form-label">Nama Semester <span style="color:#DC2626">*</span></label>
                <select name="nama_periode" class="figma-input-select" required>
                    <option value="" disabled selected>-- Pilih Semester --</option>
                    <option value="Ganjil" {{ old('nama_periode') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ old('nama_periode') == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>

            <div class="figma-form-group">
                <label class="figma-form-label">Tahun Akademik <span style="color:#DC2626">*</span></label>
                <input type="text" name="tahun_akademik" value="{{ old('tahun_akademik') }}" placeholder="cth: 2026/2027" class="figma-input" required>
            </div>

            <div class="figma-form-row">
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label">Tanggal Mulai <span style="color:#DC2626">*</span></label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="figma-input" required>
                </div>
                <div class="figma-form-group" style="margin: 0;">
                    <label class="figma-form-label">Tanggal Selesai <span style="color:#DC2626">*</span></label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="figma-input" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-top: 14px;">
                <input type="checkbox" name="is_aktif" id="is_aktif_baru" value="1" {{ old('is_aktif') ? 'checked' : '' }}>
                <label for="is_aktif_baru" style="font-size: 12.5px; font-weight: 600; color: var(--sim-brown-dark);">Langsung jadikan semester aktif</label>
            </div>

            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="btn-figma-outline" onclick="closeModalTambah()">Batal</button>
                <button type="submit" class="btn-figma-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    function openModalTambah() {
        document.getElementById('modal-tambah-semester').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModalTambah() {
        const modal = document.getElementById('modal-tambah-semester');
        const form = document.getElementById('form-tambah-semester');

        // Reset seluruh form (termasuk dropdown & input) saat modal ditutup/dibatal
        if (form) {
            form.reset();
        }

        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.getElementById('modal-tambah-semester').addEventListener('click', function(e) {
        if (e.target === this) closeModalTambah();
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModalTambah();
    });
</script>
@endpush