@extends('layouts.mahasiswa-pilih-semester')

@section('title', 'Pilih Semester & Proyek PBL')
@section('meta_description', 'Pilih periode aktif untuk masuk ke ruang kerja kelompok PBL Anda.')

@section('content')
{{-- Eyebrow breadcrumb --}}
<div class="sim-ps-eyebrow-bar">
    <span class="sim-ps-eyebrow-tag">RUANG MAHASISWA · PEMILIHAN PERIODE PBL</span>
</div>

{{-- Page Header --}}
<div class="sim-ps-header">
    <h1 class="sim-ps-header__title">Pilih Semester &amp; Proyek PBL</h1>
    <p class="sim-ps-header__desc">
        Pilih periode aktif untuk masuk ke ruang kerja kelompok, melacak milestone, dan mengisi logbook
        mingguan proyek. Riwayat semester lampau tersedia untuk referensi dan pengunduhan arsip.
    </p>
</div>

{{-- Filter Tabs + Count --}}
<div class="sim-ps-filter-bar">
    <div class="sim-ps-tabs" id="ps-tabs">
        <button class="sim-ps-tab sim-ps-tab--active" data-filter="terdaftar" onclick="filterSemester('terdaftar', this)">
            Terdaftar
            <span class="sim-ps-tab-badge" id="badge-terdaftar">4 Project</span>
        </button>
        <button class="sim-ps-tab" data-filter="selesai" onclick="filterSemester('selesai', this)">
            Selesai
            <span class="sim-ps-tab-count" id="badge-selesai">3</span>
        </button>
    </div>
    <p class="sim-ps-result-count" id="ps-result-count">Showing <strong>1 – 4</strong> of 4 result</p>
</div>

{{-- Semester Cards Grid --}}
<div class="sim-ps-grid" id="ps-grid">

    {{-- Card 1: Aktif --}}
    <div class="sim-ps-card sim-ps-card--active" data-status="terdaftar">
        <div class="sim-ps-card__head">
            <div class="sim-ps-card__title-row">
                <span class="sim-ps-card__label">PBL - Semester 5</span>
                <span class="sim-ps-badge sim-ps-badge--active">
                    <span class="sim-ps-badge__dot"></span> PERIODE AKTIF
                </span>
            </div>
        </div>
        <div class="sim-ps-card__body">
            <p class="sim-ps-card__kelas">SIB 3B · SEMESTER GANJIL 2026/2027</p>
            <h2 class="sim-ps-card__project">Pengembangan Sistem Monitoring PBL Berbasis Web &amp; Mobile</h2>
            <div class="sim-ps-card__meta">
                <div class="sim-ps-card__meta-row">
                    <span class="sim-ps-card__meta-label">Pembimbing:</span>
                    <span class="sim-ps-card__meta-val">Dr. Ahmad Fikri, S.Kom., M.T.</span>
                </div>
                <div class="sim-ps-card__meta-row">
                    <span class="sim-ps-card__meta-label">Kelompok:</span>
                    <span class="sim-ps-card__meta-val">Kelompok 01</span>
                </div>
            </div>
            <div class="sim-ps-card__progress-wrap">
                <div class="sim-ps-card__progress-row">
                    <span class="sim-ps-card__phase">Fase: Implementasi &amp; Pengujian</span>
                    <span class="sim-ps-card__pct">65%</span>
                </div>
                <div class="sim-ps-card__progress-bar">
                    <div class="sim-ps-card__progress-fill" style="width: 65%"></div>
                </div>
            </div>
        </div>
        <div class="sim-ps-card__foot">
            <a href="{{ route('mahasiswa.dashboard') }}" class="sim-ps-card__cta">
                Buka Ruang Kerja →
            </a>
        </div>
    </div>

    {{-- Card 2: Selesai --}}
    <div class="sim-ps-card" data-status="selesai">
        <div class="sim-ps-card__head">
            <div class="sim-ps-card__title-row">
                <span class="sim-ps-card__label">PBL - Semester 4</span>
                <span class="sim-ps-badge sim-ps-badge--done">SELESAI</span>
            </div>
        </div>
        <div class="sim-ps-card__body">
            <p class="sim-ps-card__kelas">SIB 2B · SEMESTER GENAP 2025/2026</p>
            <h2 class="sim-ps-card__project">Platform Monitoring Inventaris Alat Lab IoT</h2>
            <div class="sim-ps-card__meta">
                <div class="sim-ps-card__meta-row">
                    <span class="sim-ps-card__meta-label">Pembimbing:</span>
                    <span class="sim-ps-card__meta-val">Dr. Nadira Kusuma S.Kom</span>
                </div>
                <div class="sim-ps-card__meta-row">
                    <span class="sim-ps-card__meta-label">Kelompok:</span>
                    <span class="sim-ps-card__meta-val">Kelompok 04</span>
                </div>
            </div>
            <div class="sim-ps-card__hasil-row">
                <span class="sim-ps-card__hasil-label">Hasil Akhir:</span>
                <span class="sim-ps-badge sim-ps-badge--nilai">Nilai A (100%)</span>
            </div>
        </div>
        <div class="sim-ps-card__foot">
            <button class="sim-ps-card__cta-outline">Lihat Arsip Proyek ›</button>
        </div>
    </div>

    {{-- Card 3: Selesai --}}
    <div class="sim-ps-card" data-status="selesai">
        <div class="sim-ps-card__head">
            <div class="sim-ps-card__title-row">
                <span class="sim-ps-card__label">PBL - Semester 3</span>
                <span class="sim-ps-badge sim-ps-badge--done">SELESAI</span>
            </div>
        </div>
        <div class="sim-ps-card__body">
            <p class="sim-ps-card__kelas">SIB 2A · SEMESTER GANJIL 2025/2026</p>
            <h2 class="sim-ps-card__project">Sistem Reservasi Ruang Baca Digital Kampus</h2>
            <div class="sim-ps-card__meta">
                <div class="sim-ps-card__meta-row">
                    <span class="sim-ps-card__meta-label">Pembimbing:</span>
                    <span class="sim-ps-card__meta-val">Bambang Sudarmanto, M.Kom.</span>
                </div>
                <div class="sim-ps-card__meta-row">
                    <span class="sim-ps-card__meta-label">Kelompok:</span>
                    <span class="sim-ps-card__meta-val">Kelompok 02</span>
                </div>
            </div>
            <div class="sim-ps-card__hasil-row">
                <span class="sim-ps-card__hasil-label">Hasil Akhir:</span>
                <span class="sim-ps-badge sim-ps-badge--nilai">Nilai A– (92%)</span>
            </div>
        </div>
        <div class="sim-ps-card__foot">
            <button class="sim-ps-card__cta-outline">Lihat Arsip Proyek ›</button>
        </div>
    </div>

    {{-- Card 4: Selesai --}}
    <div class="sim-ps-card" data-status="selesai">
        <div class="sim-ps-card__head">
            <div class="sim-ps-card__title-row">
                <span class="sim-ps-card__label">PBL - Semester 2</span>
                <span class="sim-ps-badge sim-ps-badge--done">SELESAI</span>
            </div>
        </div>
        <div class="sim-ps-card__body">
            <p class="sim-ps-card__kelas">SIB 1B · SEMESTER GENAP 2024/2025</p>
            <h2 class="sim-ps-card__project">Aplikasi Pengelolaan Kas &amp; Donasi Mahasiswa</h2>
            <div class="sim-ps-card__meta">
                <div class="sim-ps-card__meta-row">
                    <span class="sim-ps-card__meta-label">Pembimbing:</span>
                    <span class="sim-ps-card__meta-val">Indra Hermawan, M.T.</span>
                </div>
                <div class="sim-ps-card__meta-row">
                    <span class="sim-ps-card__meta-label">Kelompok:</span>
                    <span class="sim-ps-card__meta-val">Kelompok 05</span>
                </div>
            </div>
            <div class="sim-ps-card__hasil-row">
                <span class="sim-ps-card__hasil-label">Hasil Akhir:</span>
                <span class="sim-ps-badge sim-ps-badge--nilai">Nilai A (96%)</span>
            </div>
        </div>
        <div class="sim-ps-card__foot">
            <button class="sim-ps-card__cta-outline">Lihat Arsip Proyek ›</button>
        </div>
    </div>

</div>

{{-- Catatan --}}
<div class="sim-ps-catatan">
    <div class="sim-ps-catatan__icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div class="sim-ps-catatan__content">
        <p class="sim-ps-catatan__title">Catatan Pemilihan Semester:</p>
        <p class="sim-ps-catatan__text">Memerlukan akses ke periode PBL yang belum terdaftar di atas atau pengajuan dispensasi registrasi proyek? Silakan hubungi Koordinator Program Studi atau Panitia Pelaksana PBL di Gedung Jurusan TI lantai 2.</p>
    </div>
    <a href="#" class="sim-ps-catatan__btn">Pusat Bantuan →</a>
</div>

{{-- Footer --}}
<footer class="sim-ps-footer">
    <span>Sistem Informasi Bisnis - Project Based Learning (SIM-PBL) © 2026. Hak Cipta Dilindungi.</span>
    <div class="sim-ps-footer__links">
        <a href="#">Panduan Pengguna</a>
        <a href="#">Kebijakan Privasi</a>
        <a href="#">Status Server: Online</a>
    </div>
</footer>

@endsection

@push('scripts')
<script>
// ============================================================
// FILTER TABS
// ============================================================
const allCards = document.querySelectorAll('#ps-grid .sim-ps-card');
const resultCount = document.getElementById('ps-result-count');

// Initial count
updateCount();

function filterSemester(filter, btn) {
    // Update active tab
    document.querySelectorAll('.sim-ps-tab').forEach(function(t) {
        t.classList.remove('sim-ps-tab--active');
    });
    btn.classList.add('sim-ps-tab--active');

    let visible = 0;
    allCards.forEach(function(card) {
        const status = card.getAttribute('data-status');
        if (filter === 'terdaftar') {
            // Show all
            card.style.display = '';
            visible++;
        } else {
            // Show only selesai
            if (status === 'selesai') {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        }
    });

    resultCount.innerHTML = 'Showing <strong>1 – ' + visible + '</strong> of ' + visible + ' result';
}

function updateCount() {
    resultCount.innerHTML = 'Showing <strong>1 – ' + allCards.length + '</strong> of ' + allCards.length + ' result';
}
</script>
@endpush
