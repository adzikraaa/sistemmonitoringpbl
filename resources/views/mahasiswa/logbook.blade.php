@extends('layouts.mahasiswa')

@section('title', 'Logbook')
@section('meta_description', 'Catatan kegiatan mingguan kelompok PBL — catat berdasarkan tanggal, bukan nomor minggu.')

@php
    /** Pemetaan status → class badge */
    $badgeClass = function(string $status): string {
        return match($status) {
            'Disetujui'       => 'sim-badge--approved',
            'Revisi'          => 'sim-badge--danger',
            'Menunggu Review' => 'sim-badge--warning',
            default           => 'sim-badge--pending',
        };
    };

    /** Format tanggal: 2026-04-28 → 28-04-2026 */
    $fmtDate = fn(string $d): string => date('d-m-Y', strtotime($d));

    /** Label minggu: 1 → MINGGU 01 */
    $mingguLabel = fn(int $n): string => 'MINGGU ' . str_pad($n, 2, '0', STR_PAD_LEFT);
@endphp

@section('content')

{{-- ─── Page Header ───────────────────────────────────────────────────────── --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">CATATAN PROSES</p>
        <h1 class="sim-page-header__title">Logbook</h1>
        <p class="sim-page-header__desc">
            Kelompok 01 – SIB 3B &middot; Catat kegiatan berdasarkan tanggal, bukan nomor minggu.
        </p>
    </div>
    <div class="sim-page-header__right">
        <button id="btn-tambah-logbook" class="sim-btn-primary" onclick="openLogbookModal()">
            <span class="sim-btn-primary__plus">+</span>
            Tambah catatan
        </button>
    </div>
</div>

{{-- ─── Content Wrap ───────────────────────────────────────────────────────── --}}
<div class="sim-lb-wrap">

    {{-- Info bar --}}
    <div class="sim-lb-info-bar">
        <span class="sim-lb-info-bar__icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
        </span>
        <span>
            <strong>Kelompok 01 – SIB 3B</strong>
            Minggu dihitung otomatis dari tanggal paling awal ke paling akhir.
        </span>
    </div>

    {{-- ─── Two-column Layout ─────────────────────────────────────────────── --}}
    <div class="sim-lb-layout">

        {{-- LEFT: Logbook cards list --}}
        <div class="sim-lb-main" id="logbook-list"
             data-earliest="{{ $earliestTimestamp ?? time() }}">

            @forelse($logbooks as $lb)
            @php
                $badge      = $badgeClass($lb['status']);
                $tanggalFmt = $fmtDate($lb['tanggal']);
                $weekLabel  = $mingguLabel($lb['minggu']);
            @endphp
            <div class="sim-lb-card" id="lb-card-{{ $lb['id'] }}"
                 data-tanggal="{{ $lb['tanggal'] }}">

                {{-- Card header --}}
                <div class="sim-lb-card__header">
                    <span class="sim-lb-card__week">{{ $weekLabel }}</span>
                    <span class="sim-badge {{ $badge }}">&#9679; {{ $lb['status'] }}</span>
                    <span class="sim-lb-card__date">{{ $tanggalFmt }}</span>
                    <div class="sim-lb-card__actions">
                        <button class="sim-lb-card__action"
                                onclick="editLogbook('{{ $lb['id'] }}')"
                                title="Edit catatan">Edit</button>
                        <button class="sim-lb-card__action sim-lb-card__action--danger"
                                onclick="hapusLogbook('{{ $lb['id'] }}')"
                                title="Hapus catatan">Hapus</button>
                    </div>
                </div>

                {{-- Title --}}
                <h2 class="sim-lb-card__title">{{ $lb['judul'] }}</h2>

                {{-- Description --}}
                @if($lb['deskripsi'])
                <p class="sim-lb-card__desc">{{ $lb['deskripsi'] }}</p>
                @endif

                {{-- Progress --}}
                <div class="sim-lb-card__progress-section">
                    <div class="sim-lb-card__progress-row">
                        <span class="sim-lb-card__progress-label">Progress</span>
                        <span class="sim-lb-card__progress-pct">{{ $lb['progress'] }} %</span>
                    </div>
                    <div class="sim-lb-card__progress-bar">
                        <div class="sim-lb-card__progress-fill"
                             style="width: {{ $lb['progress'] }}%"></div>
                    </div>
                </div>

                {{-- Hasil & Rencana grid --}}
                @if($lb['hasil'] || $lb['rencana'])
                <div class="sim-lb-card__detail-grid">
                    @if($lb['hasil'])
                    <div>
                        <p class="sim-lb-card__detail-label">Hasil / Pembelajaran</p>
                        <p class="sim-lb-card__detail-text">{{ $lb['hasil'] }}</p>
                    </div>
                    @endif
                    @if($lb['rencana'])
                    <div>
                        <p class="sim-lb-card__detail-label">Langkah Berikutnya</p>
                        <p class="sim-lb-card__detail-text">{{ $lb['rencana'] }}</p>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Kendala --}}
                @if($lb['kendala'])
                <p class="sim-lb-card__kendala">
                    <strong>Kendala:</strong> {{ $lb['kendala'] }}
                </p>
                @endif

                {{-- Respons Pembimbing --}}
                @if($lb['feedback'])
                <div class="sim-lb-card__response">
                    <span class="sim-lb-card__response-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="sim-lb-card__response-label">Respons Pembimbing</p>
                        <p class="sim-lb-card__response-text">{{ $lb['feedback'] }}</p>
                    </div>
                </div>
                @endif

            </div>{{-- /.sim-lb-card --}}
            @empty

            {{-- Empty state --}}
            <div class="sim-lb-empty" id="lb-empty-state">
                <div class="sim-lb-empty__icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.75"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </div>
                <p class="sim-lb-empty__title">Belum ada catatan logbook</p>
                <p class="sim-lb-empty__desc">
                    Mulai catat kegiatan mingguan kelompokmu.<br>
                    Klik tombol <strong>+ Tambah catatan</strong> di atas untuk memulai.
                </p>
                <button class="sim-btn-primary" onclick="openLogbookModal()">
                    <span class="sim-btn-primary__plus">+</span>
                    Tambah catatan pertama
                </button>
            </div>

            @endforelse
        </div>{{-- /.sim-lb-main --}}

        {{-- RIGHT: Sidebar --}}
        <aside class="sim-lb-sidebar">
            <div class="sim-lb-sidebar-card">
                <div class="sim-lb-sidebar-card__top">
                    <div class="sim-lb-sidebar-card__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.75"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div>
                        <p class="sim-lb-sidebar-card__heading">Ritme mingguan</p>
                        <p class="sim-lb-sidebar-card__sub">Kelompok 01 – SIB 3B</p>
                    </div>
                </div>
                <ul class="sim-lb-sidebar-tips">
                    <li class="sim-lb-sidebar-tip">
                        <span class="sim-lb-sidebar-tip__num">01</span>
                        <span class="sim-lb-sidebar-tip__text">
                            Tulis sesegera mungkin setelah kegiatan
                        </span>
                    </li>
                    <li class="sim-lb-sidebar-tip">
                        <span class="sim-lb-sidebar-tip__num">02</span>
                        <span class="sim-lb-sidebar-tip__text">
                            Sebutkan keputusan, bukan hanya aktivitas
                        </span>
                    </li>
                    <li class="sim-lb-sidebar-tip">
                        <span class="sim-lb-sidebar-tip__num">03</span>
                        <span class="sim-lb-sidebar-tip__text">
                            Tutup dengan langkah berikutnya
                        </span>
                    </li>
                </ul>
            </div>
        </aside>

    </div>{{-- /.sim-lb-layout --}}
</div>{{-- /.sim-lb-wrap --}}

{{-- ─── Modal Tambah Logbook ──────────────────────────────────────────────── --}}
<div id="logbook-modal-overlay"
     class="sim-modal-overlay"
     onclick="handleLbOverlayClick(event)"
     role="dialog"
     aria-modal="true"
     aria-labelledby="lb-modal-title">

    <div class="sim-modal">
        <div class="sim-modal__header">
            <div>
                <p class="sim-modal__eyebrow">RUANG KERJA</p>
                <h2 class="sim-modal__title" id="lb-modal-title">Tambah logbook</h2>
            </div>
            <button class="sim-modal__close"
                    onclick="closeLogbookModal()"
                    aria-label="Tutup modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="logbook-form"
              class="sim-modal__form"
              onsubmit="submitLogbook(event)"
              novalidate>

            {{-- 1. Tanggal --}}
            <div class="sim-form-group">
                <label class="sim-form-label" for="lb-tanggal">Tanggal</label>
                <input type="date"
                       id="lb-tanggal"
                       name="tanggal"
                       class="sim-form-input"
                       required>
                <p class="sim-form-error" id="lb-err-tanggal" role="alert"></p>
            </div>

            {{-- 2. Judul kegiatan --}}
            <div class="sim-form-group">
                <label class="sim-form-label" for="lb-judul">Judul kegiatan</label>
                <input type="text"
                       id="lb-judul"
                       name="judul"
                       class="sim-form-input"
                       placeholder="Ringkasan singkat kegiatan"
                       autocomplete="off"
                       required>
                <p class="sim-form-error" id="lb-err-judul" role="alert"></p>
            </div>

            {{-- 3. Deskripsi --}}
            <div class="sim-form-group">
                <label class="sim-form-label" for="lb-deskripsi">Deskripsi</label>
                <textarea id="lb-deskripsi"
                          name="deskripsi"
                          class="sim-form-textarea"
                          placeholder="Ceritakan apa yang dikerjakan"
                          rows="3"></textarea>
            </div>

            {{-- 4. Progress --}}
            <div class="sim-form-group">
                <label class="sim-form-label" for="lb-progress">Progress (%)</label>
                <input type="number"
                       id="lb-progress"
                       name="progress"
                       class="sim-form-input"
                       min="0"
                       max="100"
                       value="0"
                       placeholder="0 – 100"
                       required>
                <p class="sim-form-error" id="lb-err-progress" role="alert"></p>
            </div>

            {{-- 5. Hasil / pembelajaran --}}
            <div class="sim-form-group">
                <label class="sim-form-label" for="lb-hasil">Hasil / pembelajaran</label>
                <textarea id="lb-hasil"
                          name="hasil"
                          class="sim-form-textarea"
                          placeholder="Apa yang berhasil diselesaikan atau dipelajari?"
                          rows="3"></textarea>
            </div>

            {{-- 6. Kendala --}}
            <div class="sim-form-group">
                <label class="sim-form-label" for="lb-kendala">Kendala</label>
                <textarea id="lb-kendala"
                          name="kendala"
                          class="sim-form-textarea"
                          placeholder="Hambatan yang dihadapi selama kegiatan"
                          rows="3"></textarea>
            </div>

            {{-- 7. Rencana berikutnya --}}
            <div class="sim-form-group">
                <label class="sim-form-label" for="lb-rencana">Rencana berikutnya</label>
                <textarea id="lb-rencana"
                          name="rencana"
                          class="sim-form-textarea"
                          placeholder="Langkah lanjutan setelah kegiatan ini"
                          rows="3"></textarea>
            </div>

            {{-- Footer: Batal & Simpan --}}
            <div class="sim-modal__footer">
                <button type="button"
                        class="sim-btn-secondary"
                        id="btn-lb-batal"
                        onclick="handleLbBatal()">Batal</button>
                <button type="submit"
                        class="sim-btn-primary"
                        id="btn-lb-simpan">Simpan</button>
            </div>

        </form>
    </div>
</div>

{{-- Toast --}}
<div id="sim-toast" class="sim-toast" role="alert" aria-live="polite"></div>

@endsection

@push('scripts')
<script>
// ================================================================
// HELPERS
// ================================================================
function lbToast(message, type) {
    var toast = document.getElementById('sim-toast');
    toast.textContent = message;
    toast.className = 'sim-toast sim-toast--' + type + ' sim-toast--visible';
    clearTimeout(window._lbToastTimer);
    window._lbToastTimer = setTimeout(function () {
        toast.classList.remove('sim-toast--visible');
    }, 4000);
}

// ================================================================
// MODAL OPEN / CLOSE
// ================================================================
function openLogbookModal() {
    var overlay = document.getElementById('logbook-modal-overlay');
    overlay.classList.add('sim-modal-overlay--visible');
    document.body.style.overflow = 'hidden';

    // Set today's date as default
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('lb-tanggal').value = today;

    // Reset form & errors
    document.getElementById('logbook-form').reset();
    document.getElementById('lb-tanggal').value = today; // restore after reset
    lbClearAllErrors();
}

function closeLogbookModal() {
    var overlay = document.getElementById('logbook-modal-overlay');
    overlay.classList.remove('sim-modal-overlay--visible');
    document.body.style.overflow = '';
}

function handleLbOverlayClick(event) {
    if (event.target === document.getElementById('logbook-modal-overlay')) {
        handleLbBatal();
    }
}

// Close on ESC
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        var overlay = document.getElementById('logbook-modal-overlay');
        if (overlay.classList.contains('sim-modal-overlay--visible')) {
            handleLbBatal();
        }
    }
});

// ================================================================
// TOMBOL BATAL – cek apakah form sudah diisi
// ================================================================
function lbFormHasContent() {
    var judul    = (document.getElementById('lb-judul').value || '').trim();
    var deskripsi= (document.getElementById('lb-deskripsi').value || '').trim();
    var hasil    = (document.getElementById('lb-hasil').value || '').trim();
    var kendala  = (document.getElementById('lb-kendala').value || '').trim();
    var rencana  = (document.getElementById('lb-rencana').value || '').trim();
    var progress = document.getElementById('lb-progress').value;
    return judul || deskripsi || hasil || kendala || rencana || progress !== '0';
}

function handleLbBatal() {
    if (lbFormHasContent()) {
        if (!confirm('Formulir sudah diisi. Yakin ingin membatalkan? Data yang belum disimpan akan hilang.')) {
            return;
        }
    }
    closeLogbookModal();
}

// ================================================================
// VALIDATION
// ================================================================
var LB_FIELDS = ['tanggal', 'judul', 'progress'];

function lbClearAllErrors() {
    LB_FIELDS.forEach(function (key) {
        var errEl   = document.getElementById('lb-err-' + key);
        var inputEl = document.getElementById('lb-' + key);
        if (errEl)   { errEl.textContent = ''; errEl.style.display = 'none'; }
        if (inputEl) { inputEl.classList.remove('sim-form-input--error', 'sim-form-textarea--error'); }
    });
}

function lbShowError(fieldKey, message) {
    var errEl   = document.getElementById('lb-err-' + fieldKey);
    var inputEl = document.getElementById('lb-' + fieldKey);
    if (errEl)   { errEl.textContent = message; errEl.style.display = 'block'; }
    if (inputEl) {
        inputEl.classList.add(
            inputEl.tagName === 'INPUT' ? 'sim-form-input--error' : 'sim-form-textarea--error'
        );
    }
}

function lbValidate(data) {
    var valid = true;
    lbClearAllErrors();

    if (!data.tanggal) {
        lbShowError('tanggal', 'Tanggal wajib diisi.');
        valid = false;
    }
    if (!data.judul.trim()) {
        lbShowError('judul', 'Judul kegiatan wajib diisi.');
        valid = false;
    }
    var prog = parseInt(data.progress, 10);
    if (isNaN(prog) || prog < 0 || prog > 100) {
        lbShowError('progress', 'Progress harus berupa angka antara 0 sampai 100.');
        valid = false;
    }
    return valid;
}

// ================================================================
// SUBMIT – fetch ke controller, tambahkan card ke DOM
// ================================================================
function submitLogbook(event) {
    event.preventDefault();

    var data = {
        tanggal  : document.getElementById('lb-tanggal').value,
        judul    : document.getElementById('lb-judul').value,
        deskripsi: document.getElementById('lb-deskripsi').value,
        progress : document.getElementById('lb-progress').value,
        hasil    : document.getElementById('lb-hasil').value,
        kendala  : document.getElementById('lb-kendala').value,
        rencana  : document.getElementById('lb-rencana').value,
    };

    if (!lbValidate(data)) return;

    var btn = document.getElementById('btn-lb-simpan');
    btn.disabled    = true;
    btn.textContent = 'Menyimpan…';

    // Baca CSRF token dari meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch('{{ route("mahasiswa.logbook.store") }}', {
        method : 'POST',
        headers: {
            'Content-Type'    : 'application/json',
            'X-CSRF-TOKEN'    : csrfToken,
            'Accept'          : 'application/json',
        },
        body: JSON.stringify({
            tanggal  : data.tanggal,
            judul    : data.judul,
            deskripsi: data.deskripsi,
            progress : parseInt(data.progress, 10),
            hasil    : data.hasil,
            kendala  : data.kendala,
            rencana  : data.rencana,
        }),
    })
    .then(function (res) {
        if (!res.ok) {
            return res.json().then(function (err) { throw err; });
        }
        return res.json();
    })
    .then(function (json) {
        if (json.success) {
            closeLogbookModal();
            lbAddCardToDOM(data);
            lbToast('Catatan logbook berhasil ditambahkan!', 'success');
        } else {
            throw new Error(json.message || 'Gagal menyimpan catatan.');
        }
    })
    .catch(function (err) {
        var msg = (err && err.message) ? err.message : 'Terjadi kesalahan. Silakan coba lagi.';
        lbToast(msg, 'error');
    })
    .finally(function () {
        btn.disabled    = false;
        btn.textContent = 'Simpan';
    });
}

// ================================================================
// DOM MANIPULATION – tambahkan card baru ke daftar
// ================================================================
function lbCalcMinggu(tanggalStr) {
    var listEl    = document.getElementById('logbook-list');
    var earliest  = parseInt(listEl.dataset.earliest, 10) * 1000;
    var newTs     = new Date(tanggalStr).getTime();

    if (!earliest || isNaN(earliest)) {
        // Jadikan tanggal baru sebagai yang pertama
        listEl.dataset.earliest = Math.floor(newTs / 1000);
        return 1;
    }
    // Perbaiki earliest jika tanggal baru lebih awal
    if (newTs < earliest) {
        listEl.dataset.earliest = Math.floor(newTs / 1000);
        earliest = newTs;
    }
    var diffDays = (newTs - earliest) / 86400000;
    return Math.floor(diffDays / 7) + 1;
}

function lbFormatDate(tanggalStr) {
    var parts = tanggalStr.split('-'); // [2026, 04, 28]
    if (parts.length === 3) {
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }
    return tanggalStr;
}

function lbMingguLabel(n) {
    return 'MINGGU ' + String(n).padStart(2, '0');
}

function lbAddCardToDOM(data) {
    // Sembunyikan empty state jika ada
    var emptyEl = document.getElementById('lb-empty-state');
    if (emptyEl) emptyEl.style.display = 'none';

    var minggu    = lbCalcMinggu(data.tanggal);
    var mingguLbl = lbMingguLabel(minggu);
    var dateFmt   = lbFormatDate(data.tanggal);
    var progress  = parseInt(data.progress, 10) || 0;
    var id        = 'lb-new-' + Date.now();

    var hasilRencanaHtml = '';
    if (data.hasil || data.rencana) {
        hasilRencanaHtml = '<div class="sim-lb-card__detail-grid">';
        if (data.hasil) {
            hasilRencanaHtml +=
                '<div>' +
                '<p class="sim-lb-card__detail-label">Hasil / Pembelajaran</p>' +
                '<p class="sim-lb-card__detail-text">' + lbEscape(data.hasil) + '</p>' +
                '</div>';
        }
        if (data.rencana) {
            hasilRencanaHtml +=
                '<div>' +
                '<p class="sim-lb-card__detail-label">Langkah Berikutnya</p>' +
                '<p class="sim-lb-card__detail-text">' + lbEscape(data.rencana) + '</p>' +
                '</div>';
        }
        hasilRencanaHtml += '</div>';
    }

    var kendalaHtml = data.kendala
        ? '<p class="sim-lb-card__kendala"><strong>Kendala:</strong> ' + lbEscape(data.kendala) + '</p>'
        : '';

    var deskripsiHtml = data.deskripsi
        ? '<p class="sim-lb-card__desc">' + lbEscape(data.deskripsi) + '</p>'
        : '';

    var cardHtml =
        '<div class="sim-lb-card" id="' + id + '" data-tanggal="' + lbEscape(data.tanggal) + '" ' +
        'style="animation: lbFadeIn 0.35s ease both;">' +

        '<div class="sim-lb-card__header">' +
        '<span class="sim-lb-card__week">' + mingguLbl + '</span>' +
        '<span class="sim-badge sim-badge--warning">&#9679; Menunggu Review</span>' +
        '<span class="sim-lb-card__date">' + dateFmt + '</span>' +
        '<div class="sim-lb-card__actions">' +
        '<button class="sim-lb-card__action" onclick="editLogbook(\'' + id + '\')" title="Edit catatan">Edit</button>' +
        '<button class="sim-lb-card__action sim-lb-card__action--danger" onclick="hapusLogbook(\'' + id + '\')" title="Hapus catatan">Hapus</button>' +
        '</div></div>' +

        '<h2 class="sim-lb-card__title">' + lbEscape(data.judul) + '</h2>' +
        deskripsiHtml +

        '<div class="sim-lb-card__progress-section">' +
        '<div class="sim-lb-card__progress-row">' +
        '<span class="sim-lb-card__progress-label">Progress</span>' +
        '<span class="sim-lb-card__progress-pct">' + progress + ' %</span>' +
        '</div>' +
        '<div class="sim-lb-card__progress-bar">' +
        '<div class="sim-lb-card__progress-fill" style="width:' + progress + '%"></div>' +
        '</div></div>' +

        hasilRencanaHtml +
        kendalaHtml +
        '</div>';

    var listEl = document.getElementById('logbook-list');
    listEl.insertAdjacentHTML('beforeend', cardHtml);
}

function lbEscape(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Fade-in animation untuk card baru
var lbStyle = document.createElement('style');
lbStyle.textContent = '@keyframes lbFadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }';
document.head.appendChild(lbStyle);

// ================================================================
// EDIT & HAPUS (stub — tampilkan pesan sementara)
// ================================================================
function editLogbook(id) {
    lbToast('Fitur edit akan tersedia setelah integrasi database.', 'error');
}

function hapusLogbook(id) {
    if (!confirm('Yakin ingin menghapus catatan logbook ini?')) return;
    var card = document.getElementById(id) || document.getElementById('lb-card-' + id);
    if (card) {
        card.style.transition = 'opacity 0.3s, transform 0.3s';
        card.style.opacity    = '0';
        card.style.transform  = 'scale(0.97)';
        setTimeout(function () {
            card.remove();
            // Tampilkan empty state jika tidak ada card tersisa
            var remaining = document.querySelectorAll('#logbook-list .sim-lb-card');
            if (remaining.length === 0) {
                var emptyEl = document.getElementById('lb-empty-state');
                if (emptyEl) {
                    emptyEl.style.display = 'flex';
                } else {
                    document.getElementById('logbook-list').insertAdjacentHTML('beforeend',
                        '<div class="sim-lb-card" id="lb-empty-state" style="text-align:center;padding:40px;border-style:dashed;">' +
                        '<p style="color:var(--sim-text-muted);margin:0;">Semua catatan telah dihapus.</p></div>'
                    );
                }
            }
        }, 300);
        lbToast('Catatan berhasil dihapus.', 'success');
    }
}
</script>
@endpush
