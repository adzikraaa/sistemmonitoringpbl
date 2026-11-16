@extends('layouts.dosen-pilih-semester')

@section('title', 'Pilih Semester – Dosen Pengampu')
@section('meta_description', 'Pilih semester akademik aktif untuk memantau progres dan evaluasi tim proyek PBL.')

@push('styles')
<style>
/* ── Root & Global ──────────────────────────────────────── */
:root {
    --dp-green:       #3B7454;
    --dp-green-light: #D6ECDE;
    --dp-green-pale:  #EAF4EE;
    --dp-brown:       #2D2621;
    --dp-brown-deep:  #261E19;
    --dp-taupe:       #A3978A;
    --dp-ochre:       #B87C2B;
    --dp-gold:        #CBA153;
    --dp-beige:       #E8E2D8;
    --dp-ivory:       #F4F0E8;
    --dp-offwhite:    #FAF8F4;
    --dp-border:      #CFC7BC;
    --dp-border-light:#E3DDD6;
}
* { box-sizing: border-box; }

/* ── Page wrapper ───────────────────────────────────────── */
.dp-page {
    min-height: calc(100vh - 60px);
    background: var(--dp-offwhite);
    display: flex;
    flex-direction: column;
}

/* ── Error / info flash ─────────────────────────────────── */
.dp-flash {
    max-width: 860px;
    margin: 20px auto 0;
    padding: 12px 18px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 500;
    width: 100%;
}
.dp-flash--error { background: #FEF3F2; color: #B91C1C; border: 1px solid #FCA5A5; }
.dp-flash--info  { background: var(--dp-green-pale); color: var(--dp-green); border: 1px solid var(--dp-green-light); }

/* ── Hero / Intro ───────────────────────────────────────── */
.dp-hero {
    text-align: center;
    padding: 52px 24px 36px;
}
.dp-hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--dp-green-light);
    color: var(--dp-green);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 5px 14px;
    border-radius: 20px;
    margin-bottom: 20px;
}
.dp-hero__eyebrow-dot {
    width: 6px; height: 6px;
    background: var(--dp-green);
    border-radius: 50%;
}
.dp-hero__title {
    font-family: 'DM Serif Display', serif;
    font-size: 38px;
    font-weight: 400;
    color: var(--dp-brown);
    margin: 0 0 12px;
    line-height: 1.15;
}
.dp-hero__desc {
    font-size: 14.5px;
    color: var(--dp-taupe);
    margin: 0 auto 20px;
    max-width: 520px;
    line-height: 1.6;
}
.dp-hero__count {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #fff;
    border: 1px solid var(--dp-border);
    border-radius: 20px;
    padding: 6px 16px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--dp-brown);
}

/* ── Cards Grid ─────────────────────────────────────────── */
.dp-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 24px 40px;
    width: 100%;
}

/* ── Single Card ────────────────────────────────────────── */
.dp-card {
    background: #fff;
    border: 1.5px solid var(--dp-border);
    border-radius: 16px;
    padding: 20px;
    cursor: pointer;
    transition: box-shadow 0.2s, border-color 0.2s, transform 0.15s;
    position: relative;
    user-select: none;
    display: flex;
    flex-direction: column;
    gap: 0;
}
.dp-card:hover {
    box-shadow: 0 4px 20px rgba(59,116,84,0.10);
    transform: translateY(-2px);
}
.dp-card--active {
    border-color: var(--dp-green);
    border-width: 2px;
    box-shadow: 0 0 0 3px rgba(59,116,84,0.08);
}
.dp-card--selected {
    border-color: var(--dp-green);
    border-width: 2.5px;
    box-shadow: 0 0 0 4px rgba(59,116,84,0.13);
}

/* Checkmark (visible when selected) */
.dp-card__check {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 24px;
    height: 24px;
    background: var(--dp-green);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: scale(0.7);
    transition: opacity 0.2s, transform 0.2s;
}
.dp-card--selected .dp-card__check,
.dp-card--active.dp-card--selected .dp-card__check { opacity: 1; transform: scale(1); }

/* Status badge */
.dp-card__status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 20px;
    margin-bottom: 10px;
    width: fit-content;
}
.dp-card__status--berjalan {
    background: var(--dp-green-light);
    color: var(--dp-green);
}
.dp-card__status-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: currentColor;
}
.dp-card__status--selesai {
    background: var(--dp-beige);
    color: var(--dp-taupe);
}
.dp-card__status-icon { color: currentColor; opacity: 0.7; }

/* Tingkat */
.dp-card__tingkat {
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--dp-taupe);
    margin-bottom: 4px;
}

/* Nama semester */
.dp-card__nama {
    font-family: 'DM Serif Display', serif;
    font-size: 26px;
    font-weight: 400;
    color: var(--dp-brown);
    line-height: 1.1;
    margin-bottom: 2px;
}

/* Tahun akademik */
.dp-card__tahun {
    font-size: 12px;
    font-weight: 600;
    color: var(--dp-brown);
    margin-bottom: 10px;
}

/* Divider tipis */
.dp-card__divider {
    height: 1px;
    background: var(--dp-border-light);
    margin: 10px 0;
}

/* Deskripsi */
.dp-card__desc {
    font-size: 12.5px;
    color: var(--dp-taupe);
    line-height: 1.5;
    margin-bottom: 14px;
    flex: 1;
}

/* Progress bar (only for semester berjalan) */
.dp-card__progress-wrap { margin-bottom: 14px; }
.dp-card__progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 11.5px;
    color: var(--dp-taupe);
    margin-bottom: 5px;
}
.dp-card__progress-label-left { font-weight: 500; color: var(--dp-brown); }
.dp-card__progress-bar {
    height: 5px;
    background: var(--dp-beige);
    border-radius: 4px;
    overflow: hidden;
}
.dp-card__progress-fill {
    height: 100%;
    background: var(--dp-green);
    border-radius: 4px;
    transition: width 0.6s ease;
}

/* Status penilaian (semester selesai) */
.dp-card__penilaian {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    margin-bottom: 14px;
}
.dp-card__penilaian-label { color: var(--dp-taupe); }
.dp-card__penilaian-val { font-weight: 600; color: var(--dp-brown); }

/* CTA Button inside card */
.dp-card__btn {
    width: 100%;
    padding: 10px 0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
    text-align: center;
    border: none;
}
.dp-card__btn--primary {
    background: var(--dp-brown);
    color: #fff;
    border: 2px solid var(--dp-brown);
}
.dp-card__btn--primary:hover { background: var(--dp-brown-deep); }
.dp-card__btn--outline {
    background: #fff;
    color: var(--dp-brown);
    border: 1.5px solid var(--dp-border);
}
.dp-card__btn--outline:hover {
    border-color: var(--dp-green);
    color: var(--dp-green);
    background: var(--dp-green-pale);
}

/* ── Bottom Bar ─────────────────────────────────────────── */
.dp-bottom {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 24px 32px;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.dp-bottom__nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.dp-bottom__back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    font-weight: 500;
    color: var(--dp-taupe);
    text-decoration: none;
    transition: color 0.15s;
}
.dp-bottom__back:hover { color: var(--dp-brown); }
.dp-bottom__actions { display: flex; align-items: center; gap: 10px; }
.dp-bottom__sync {
    font-size: 12px;
    color: var(--dp-taupe);
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Lanjutkan button */
.dp-btn-lanjutkan {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--dp-brown);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 11px 24px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, opacity 0.15s;
    font-family: inherit;
}
.dp-btn-lanjutkan:hover { background: var(--dp-brown-deep); }
.dp-btn-lanjutkan:disabled { opacity: 0.45; cursor: not-allowed; }

/* Validation message */
.dp-validation {
    display: none;
    background: #FEF3F2;
    color: #B91C1C;
    border: 1px solid #FCA5A5;
    border-radius: 8px;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 500;
    margin-top: 4px;
}
.dp-validation--visible { display: block; }

/* ── Footer ─────────────────────────────────────────────── */
.dp-footer {
    margin-top: auto;
    border-top: 1px solid var(--dp-border-light);
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    color: var(--dp-taupe);
    flex-wrap: wrap;
    gap: 8px;
}

/* ── Responsive ─────────────────────────────────────────── */
@media (max-width: 860px) {
    .dp-grid { grid-template-columns: repeat(2, 1fr); }
    .dp-hero__title { font-size: 30px; }
}
@media (max-width: 580px) {
    .dp-grid { grid-template-columns: 1fr; }
    .dp-hero { padding: 36px 16px 24px; }
    .dp-hero__title { font-size: 26px; }
    .dp-bottom { padding: 0 16px 28px; }
    .dp-bottom__nav { flex-direction: column; align-items: stretch; }
    .dp-btn-lanjutkan { width: 100%; justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="dp-page">

    {{-- Flash messages --}}
    @if(session('error'))
        <div class="dp-flash dp-flash--error" style="max-width:1000px;margin:20px auto 0;width:calc(100% - 48px);">
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="dp-flash dp-flash--info" style="max-width:1000px;margin:20px auto 0;width:calc(100% - 48px);">
            {{ session('info') }}
        </div>
    @endif

    {{-- ══════════════ HERO ══════════════ --}}
    <div class="dp-hero">
        <div class="dp-hero__eyebrow">
            <span class="dp-hero__eyebrow-dot"></span>
            RUANG DOSEN &bull; T.A. {{ $tahunAkademik }}
        </div>
        <h1 class="dp-hero__title">Silahkan Pilih Semester</h1>
        <p class="dp-hero__desc">
            Pilih semester akademik aktif untuk memantau progres, rubrik penilaian, dan mengevaluasi tim proyek PBL Anda.
        </p>
        <div class="dp-hero__count">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Semester Tersedia ({{ count($semesters) }})
        </div>
    </div>

    {{-- ══════════════ FORM + CARDS ══════════════ --}}
    <form id="dp-form" method="POST" action="{{ route('dosen.set-semester') }}">
        @csrf
        <input type="hidden" name="semester_id" id="dp-semester-input" value="">

        <div class="dp-grid" id="dp-grid">

            @foreach($semesters as $smt)
            @php
                $isBerjalan = $smt['status'] === 'berjalan';
            @endphp

            <div class="dp-card {{ $isBerjalan ? 'dp-card--active' : '' }}"
                 id="dp-card-{{ $smt['id'] }}"
                 data-semester="{{ $smt['id'] }}"
                 onclick="selectSemester('{{ $smt['id'] }}')"
                 role="button"
                 tabindex="0"
                 aria-pressed="false"
                 onkeydown="if(event.key==='Enter'||event.key===' ')selectSemester('{{ $smt['id'] }}')">

                {{-- Checkmark --}}
                <div class="dp-card__check" aria-hidden="true">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>

                {{-- Status badge --}}
                @if($isBerjalan)
                <div class="dp-card__status dp-card__status--berjalan">
                    <span class="dp-card__status-dot"></span>
                    SEMESTER BERJALAN
                </div>
                @else
                <div class="dp-card__status dp-card__status--selesai">
                    <svg class="dp-card__status-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    SEMESTER SEBELUMNYA
                </div>
                @endif

                {{-- Tingkat --}}
                <div class="dp-card__tingkat">{{ $smt['tingkat'] }}</div>

                {{-- Nama --}}
                <div class="dp-card__nama">{{ $smt['nama'] }}</div>

                {{-- Tahun akademik --}}
                <div class="dp-card__tahun">{{ $smt['tahun'] }}</div>

                <div class="dp-card__divider"></div>

                {{-- Deskripsi --}}
                <div class="dp-card__desc">{{ $smt['deskripsi'] }}</div>

                {{-- Progress bar (hanya semester berjalan) --}}
                @if($isBerjalan && $smt['progress'] !== null)
                <div class="dp-card__progress-wrap">
                    <div class="dp-card__progress-label">
                        <span class="dp-card__progress-label-left">Progres PBL</span>
                        <span>Minggu {{ $smt['minggu_ke'] }} / {{ $smt['total_minggu'] }}</span>
                    </div>
                    <div class="dp-card__progress-bar">
                        <div class="dp-card__progress-fill" style="width:{{ $smt['progress'] }}%"></div>
                    </div>
                </div>
                @endif

                {{-- Status penilaian (semester selesai) --}}
                @if(!$isBerjalan && $smt['status_penilaian'])
                <div class="dp-card__penilaian">
                    <span class="dp-card__penilaian-label">Status Penilaian</span>
                    <span class="dp-card__penilaian-val">{{ $smt['status_penilaian'] }}</span>
                </div>
                @endif

                {{-- CTA — semua kartu mulai dengan style outline agar konsisten.
                     JS akan mengubahnya menjadi primary saat kartu dipilih. --}}
                <button type="button" class="dp-card__btn dp-card__btn--outline" onclick="event.stopPropagation();selectSemester('{{ $smt['id'] }}')">
                    Pilih Semester Ini
                </button>

            </div>
            @endforeach

        </div>

        {{-- Validation message --}}
        <div id="dp-validation" class="dp-validation" style="max-width:1000px;margin:0 auto 12px;width:calc(100% - 48px);">
            Silakan pilih semester terlebih dahulu sebelum melanjutkan.
        </div>

        {{-- ══════════════ BOTTOM BAR ══════════════ --}}
        <div class="dp-bottom">
            <div class="dp-bottom__nav">
                <a href="{{ route('login') }}" class="dp-bottom__back">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Kembali ke Login
                </a>
                <div class="dp-bottom__actions">
                    <button type="submit" id="dp-btn-lanjutkan" class="dp-btn-lanjutkan" onclick="return validateForm()">
                        Lanjutkan ke Dashboard
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>

    </form>

    {{-- ══════════════ FOOTER ══════════════ --}}
    <footer class="dp-footer">
        <span>&copy; {{ date('Y') }} Sistem Informasi Monitoring Pembelajaran Berbasis Proyek (SIM-PBL).</span>
        <span>Pusat Penjaminan Mutu &amp; Pembelajaran</span>
    </footer>

</div>
@endsection

@push('scripts')
<script>
// ── Semester selection ───────────────────────────────────────
var selectedId = '';

// Auto-select semester berjalan pada load
document.addEventListener('DOMContentLoaded', function () {
    var defaultCard = document.querySelector('.dp-card--active');
    if (defaultCard) {
        selectSemester(defaultCard.dataset.semester, false);
    }
});

function selectSemester(id, showValidation) {
    selectedId = id;
    document.getElementById('dp-semester-input').value = id;

    // Reset SEMUA kartu ke outline — termasuk kartu semester berjalan.
    // Kartu yang dipilih akan diubah ke primary di bawah.
    document.querySelectorAll('.dp-card').forEach(function (card) {
        card.classList.remove('dp-card--selected');
        card.setAttribute('aria-pressed', 'false');
        var btn = card.querySelector('.dp-card__btn');
        btn.textContent = 'Pilih Semester Ini';
        btn.className = 'dp-card__btn dp-card__btn--outline';
    });

    // Mark the chosen card as selected
    var chosen = document.getElementById('dp-card-' + id);
    if (chosen) {
        chosen.classList.add('dp-card--selected');
        chosen.setAttribute('aria-pressed', 'true');
        var btn = chosen.querySelector('.dp-card__btn');
        btn.textContent = 'Semester Terpilih \u2713';
        btn.className = 'dp-card__btn dp-card__btn--primary';
    }

    // Hide validation
    document.getElementById('dp-validation').classList.remove('dp-validation--visible');
}

function validateForm() {
    if (!selectedId) {
        document.getElementById('dp-validation').classList.add('dp-validation--visible');
        document.getElementById('dp-grid').scrollIntoView({ behavior: 'smooth', block: 'start' });
        return false;
    }
    return true;
}
</script>
@endpush
