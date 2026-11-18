@extends('layouts.dosen')

@section('title', 'Laporan Aktivitas Kelompok')
@section('breadcrumb', 'Laporan aktivitas')
@section('meta_description', 'Pantau pembaruan pekerjaan dan perkembangan proyek mahasiswa.')

@push('styles')
<style>
/* Responsive Styles for Laporan Aktivitas */
.la-shell {
    padding: 0 32px 40px;
    box-sizing: border-box;
}

.la-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    gap: 16px;
    flex-wrap: wrap;
}

.la-filter-grid {
    background: #FFFFFF;
    border: 1px solid var(--sim-border);
    border-radius: 16px;
    padding: 18px 24px;
    margin-bottom: 24px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    align-items: flex-end;
}

.la-bottom-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 24px;
    align-items: start;
}

.la-item {
    padding: 18px 24px;
    border-bottom: 1px solid var(--sim-border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-decoration: none;
    color: inherit;
    transition: background 0.15s;
    cursor: pointer;
}

.la-item:last-child {
    border-bottom: none;
}

.la-item:hover {
    background: #FAF7F3;
}

.la-item__left {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    min-width: 0;
}

.la-item__right {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-shrink: 0;
}

.la-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    font-size: 13px;
    color: var(--sim-text-muted);
    flex-wrap: wrap;
    gap: 12px;
}

.la-page-btn {
    padding: 6px 12px;
    border: 1px solid var(--sim-border);
    background: #FFF;
    color: var(--sim-text-dark);
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    transition: background 0.12s;
}

.la-page-btn:hover {
    background: #F5EFE6;
}

.la-page-btn--active {
    background: #2C1A0E !important;
    color: #FAF7F3 !important;
    font-weight: 600;
    border-color: #2C1A0E !important;
}

.la-page-btn--disabled {
    color: var(--sim-text-light) !important;
    background: #FAF7F3 !important;
    cursor: not-allowed;
    pointer-events: none;
}

@media (max-width: 1024px) {
    .la-filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .la-bottom-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .la-shell {
        padding: 0 16px 32px;
    }
    .la-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .la-filter-grid {
        grid-template-columns: 1fr;
        padding: 16px;
    }
    .la-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
    }
    .la-item__right {
        width: 100%;
        justify-content: space-between;
    }
    .la-pagination {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endpush

@section('content')

{{-- Page Header Section --}}
<div class="sim-page-header la-header">
    <div style="flex: 1; min-width: 0;">
        <p class="sim-page-header__eyebrow" style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); margin-bottom: 4px;">RUANG DOSEN • LAPORAN AKTIVITAS</p>
        <h1 class="sim-page-header__title" style="font-family: 'DM Serif Display', serif; font-size: 28px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 6px;">
            Laporan aktivitas kelompok
        </h1>
        <p class="sim-page-header__desc" style="font-size: 13.5px; color: var(--sim-text-body); margin: 0;">
            Pantau pembaruan pekerjaan dan perkembangan proyek mahasiswa.
        </p>
    </div>

    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" id="btnEksporRekap" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 20px; font-size: 13px; font-weight: 500; color: var(--sim-text-dark); cursor: pointer;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Ekspor rekap
        </button>
        <button type="button" id="btnPerbaruiData" onclick="window.location.href='{{ route('dosen.laporan-aktivitas') }}'" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 20px; font-size: 13px; font-weight: 500; color: var(--sim-text-dark); cursor: pointer;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
            Reset filter
        </button>
    </div>
</div>

{{-- Main Container --}}
<div class="la-shell">

    {{-- Filter & Search Bar --}}
    <form method="GET" action="{{ route('dosen.laporan-aktivitas') }}" id="filterAktivitasForm">
        <div class="la-filter-grid">
            <div>
                <label for="filterKelas" style="display: block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin-bottom: 6px;">Kelas</label>
                <select id="filterKelas" name="kelas" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border: 1px solid var(--sim-border); border-radius: 8px; font-size: 13px; background: #FFF; color: var(--sim-text-dark); outline: none;">
                    <option value="semua">Semua Kelas</option>
                    @foreach(($daftarKelas ?? ['SIB 3A', 'SIB 3B', 'SIB 3C', 'SIB 3D', 'SIB 3E']) as $kls)
                        <option value="{{ $kls }}" {{ request('kelas') === $kls ? 'selected' : '' }}>{{ $kls }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filterJenisLaporan" style="display: block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin-bottom: 6px;">Jenis Laporan</label>
                <select id="filterJenisLaporan" name="jenis" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border: 1px solid var(--sim-border); border-radius: 8px; font-size: 13px; background: #FFF; color: var(--sim-text-dark); outline: none;">
                    <option value="semua">Semua jenis</option>
                    <option value="logbook" {{ request('jenis') === 'logbook' ? 'selected' : '' }}>Logbook</option>
                    <option value="proposal" {{ request('jenis') === 'proposal' ? 'selected' : '' }}>Proposal</option>
                    <option value="milestone" {{ request('jenis') === 'milestone' ? 'selected' : '' }}>Milestone</option>
                </select>
            </div>
            <div>
                <label for="filterStatusEvaluasi" style="display: block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin-bottom: 6px;">Status Evaluasi</label>
                <select id="filterStatusEvaluasi" name="status" onchange="this.form.submit()" style="width: 100%; padding: 8px 12px; border: 1px solid var(--sim-border); border-radius: 8px; font-size: 13px; background: #FFF; color: var(--sim-text-dark); outline: none;">
                    <option value="semua">Semua status</option>
                    <option value="menunggu-review" {{ request('status') === 'menunggu-review' ? 'selected' : '' }}>Menunggu review</option>
                    <option value="perlu-revisi" {{ request('status') === 'perlu-revisi' ? 'selected' : '' }}>Perlu revisi</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>
            <div>
                <label for="searchAktivitas" style="display: block; font-size: 9.5px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin-bottom: 6px;">Pencarian</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--sim-text-light); pointer-events: none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text"
                           id="searchAktivitas"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari topik atau lainnya..."
                           style="width: 100%; padding: 8px 32px 8px 34px; border: 1px solid var(--sim-border); border-radius: 8px; font-size: 13px; outline: none; box-sizing: border-box;">
                    @if(request('search'))
                        <a href="{{ route('dosen.laporan-aktivitas', request()->except('search', 'page')) }}"
                           style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: var(--sim-text-muted); text-decoration: none; font-size: 16px; font-weight: bold; line-height: 1;"
                           title="Hapus pencarian">&times;</a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- List Section Wrapper --}}
    <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 16px; margin-bottom: 24px; overflow: hidden;">
        
        {{-- List Header Info --}}
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--sim-border-light); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
            <span style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--sim-text-muted);">Daftar Pembaruan & Dokumen</span>
            <span id="jumlahAktivitas" style="font-size: 12px; color: var(--sim-text-muted);">
                Menampilkan {{ $aktivitasList->count() }} dari {{ $aktivitasList->total() }} aktivitas
            </span>
        </div>

        {{-- Items List --}}
        <div style="display: flex; flex-direction: column;" id="aktivitasContainer">

            @forelse($aktivitasList as $item)
                @php
                    $detailUrl = route('dosen.kelompok.show', $item['kelompok_id'] ?? 1);
                @endphp
                <a href="{{ $detailUrl }}"
                   class="la-item"
                   data-jenis-laporan="{{ $item['jenis_laporan'] }}"
                   data-status="{{ $item['status'] }}"
                   data-kelas="{{ $item['kelas'] }}"
                   title="Buka detail kelompok {{ $item['kelompok_nama'] }}">
                    <div class="la-item__left">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $item['icon_bg'] }}; color: {{ $item['icon_color'] }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                            @if($item['jenis_laporan'] === 'logbook')
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="13" y1="17" x2="8" y2="17"/></svg>
                            @elseif($item['jenis_laporan'] === 'proposal')
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.2"/></svg>
                            @else
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                            @endif
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px; flex-wrap: wrap;">
                                <span style="font-size: 11px; font-weight: 600; color: var(--sim-text-muted);">{{ $item['kelompok_nama'] }} - {{ $item['kelas'] }}</span>
                                <span style="color: var(--sim-text-light);">•</span>
                                <span style="font-size: 11px; color: var(--sim-text-muted);">{{ $item['proyek_nama'] }}</span>
                                @if(!empty($item['is_database']))
                                    <span style="font-size: 9.5px; font-weight: 700; background: #EAF4EE; color: #2E6B42; padding: 1px 6px; border-radius: 4px; text-transform: uppercase;">Database</span>
                                @endif
                            </div>
                            <h3 style="font-size: 14.5px; font-weight: 600; color: var(--sim-text-dark); margin: 0 0 6px;">
                                {{ $item['judul'] }}
                            </h3>
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: var(--sim-text-muted); flex-wrap: wrap;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span>Diunggah oleh {{ $item['pengunggah'] }}</span>
                                <span>•</span>
                                <span>{{ $item['waktu'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="la-item__right">
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; white-space: nowrap;" class="{{ $item['status_badge'] }}">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $item['status_dot'] }};"></span>
                            {{ $item['status_label'] }}
                        </span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--sim-text-light); flex-shrink: 0;"><polyline points="9 18 15 12 9 6"/></svg>
                    </div>
                </a>
            @empty
                <div style="padding: 48px 24px; text-align: center; color: var(--sim-text-muted);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px; opacity: 0.5;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <p style="font-size: 14px; margin: 0 0 6px; font-weight: 500; color: var(--sim-text-dark);">Tidak ada laporan aktivitas ditemukan.</p>
                    <p style="font-size: 12.5px; margin: 0;">Coba sesuaikan kata kunci pencarian atau ganti filter yang dipilih.</p>
                    <a href="{{ route('dosen.laporan-aktivitas') }}" style="display: inline-block; margin-top: 14px; font-size: 12.5px; color: #5C3317; font-weight: 600; text-decoration: underline;">
                        Reset semua filter
                    </a>
                </div>
            @endforelse

        </div>

    </div>

    {{-- Pagination Bar --}}
    <div class="la-pagination">
        <span>
            Menampilkan halaman {{ $aktivitasList->currentPage() }} dari {{ max(1, $aktivitasList->lastPage()) }}
            @if($aktivitasList->total() > 0)
                <span style="color: var(--sim-text-light);">• Total {{ $aktivitasList->total() }} aktivitas</span>
            @endif
        </span>

        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
            {{-- Tombol Sebelumnya --}}
            @if($aktivitasList->onFirstPage())
                <span class="la-page-btn la-page-btn--disabled">Sebelumnya</span>
            @else
                <a href="{{ $aktivitasList->previousPageUrl() }}" class="la-page-btn">Sebelumnya</a>
            @endif

            {{-- Angka Halaman --}}
            @for($p = 1; $p <= $aktivitasList->lastPage(); $p++)
                @if($p == $aktivitasList->currentPage())
                    <span class="la-page-btn la-page-btn--active">{{ $p }}</span>
                @else
                    <a href="{{ $aktivitasList->url($p) }}" class="la-page-btn">{{ $p }}</a>
                @endif
            @endfor

            {{-- Tombol Selanjutnya --}}
            @if($aktivitasList->hasMorePages())
                <a href="{{ $aktivitasList->nextPageUrl() }}" class="la-page-btn">Selanjutnya</a>
            @else
                <span class="la-page-btn la-page-btn--disabled">Selanjutnya</span>
            @endif
        </div>
    </div>

    {{-- Bottom Grid (2 Kolom: Prioritas & Statistik) --}}
    <div class="la-bottom-grid">

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
                Total dokumen menunggu evaluasi dosen minggu ini.
            </h2>

            <p style="font-size: 13px; color: #D1C4B8; line-height: 1.5; margin: 0 0 24px;">
                Pemberian catatan berkala membantu mahasiswa mempertahankan momentum kerja kelompok serta menghindari deviasi dari target capaian semester.
            </p>

            <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <a href="{{ route('dosen.kelompok') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #FAF7F3; color: #2C1A0E; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;">
                    Mulai review kelompok &rarr;
                </a>
                <span style="font-size: 12px; color: #A08C7D;">Estimasi waktu: ~15 menit</span>
            </div>
        </div>

        {{-- Kartu Kanan: Statistik Respon Dosen --}}
        <div style="background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 20px; padding: 28px 32px; display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-sizing: border-box;">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ekspor aktivitas yang sedang tampil ke file CSV.
    document.getElementById('btnEksporRekap')?.addEventListener('click', function () {
        const itemNodes = document.querySelectorAll('#aktivitasContainer .la-item');
        if (itemNodes.length === 0) {
            alert('Tidak ada aktivitas yang dapat diekspor.');
            return;
        }

        function escapeCSV(nilai) {
            return '"' + String(nilai).replace(/"/g, '""').trim() + '"';
        }

        const baris = [
            ['Kelas', 'Kelompok', 'Nama Proyek', 'Jenis Laporan', 'Judul Aktivitas', 'Status Evaluasi']
        ];

        itemNodes.forEach(function (node) {
            const judul = node.querySelector('h3')?.textContent.trim() || '';
            const kelas = node.dataset.kelas || '';
            const jenis = node.dataset.jenisLaporan || '';
            const status = node.dataset.status || '';

            baris.push([
                kelas,
                node.getAttribute('title') || '',
                jenis,
                judul,
                status
            ]);
        });

        const isiCSV = '\uFEFF' + baris.map(function (barisData) {
            return barisData.map(escapeCSV).join(',');
        }).join('\r\n');

        const file = new Blob([isiCSV], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(file);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'rekap-laporan-aktivitas.csv';
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
    });
});
</script>
@endpush

@endsection
