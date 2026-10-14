@extends('layouts.mahasiswa')

@section('title', 'Proposal')
@section('meta_description', 'Halaman Proposal Mahasiswa SIM-PBL')

@section('content')
{{-- Page Header --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">DOKUMEN PROYEK</p>
        <h1 class="sim-page-header__title">Proposal</h1>
        <p class="sim-page-header__desc">Satu dokumen yang menyatukan alasan, arah, dan cara kerja kelompok.</p>
    </div>
    <div class="sim-page-header__right">
        <button id="btn-ajukan-proposal" class="sim-btn-primary" onclick="openProposalModal()">
            <span class="sim-btn-primary__plus">+</span>
            Ajukan proposal
        </button>
    </div>
</div>

{{-- Main Content Grid --}}
<div class="sim-proposal-grid">

    {{-- LEFT: Proposal Card --}}
    <div class="sim-proposal-main">
        <div class="sim-proposal-card">
            {{-- Card header --}}
            <div class="sim-proposal-card__header">
                <div class="sim-proposal-card__meta">
                    <span class="sim-proposal-card__version">VERSI 02</span>
                    <span class="sim-badge sim-badge--approved">&#9679; Disetujui</span>
                </div>
                <span class="sim-proposal-card__date">26 September 2026</span>
            </div>

            {{-- Card title & desc --}}
            <h2 class="sim-proposal-card__title">Pengembangan Sistem Monitoring PBL</h2>
            <p class="sim-proposal-card__desc">Platform web untuk membantu mahasiswa, dosen, dan koordinator memantau progres Project Based Learning secara terstruktur.</p>

            {{-- Three-column detail --}}
            <div class="sim-proposal-detail">
                <div class="sim-proposal-detail__col">
                    <p class="sim-proposal-detail__label">LATAR BELAKANG</p>
                    <p class="sim-proposal-detail__text">Proses monitoring saat ini masih tersebar di berbagai kanal sehingga histori dan progres sulit dipantau secara utuh.</p>
                </div>
                <div class="sim-proposal-detail__col">
                    <p class="sim-proposal-detail__label">TUJUAN</p>
                    <p class="sim-proposal-detail__text">Membangun satu sistem terpusat untuk proposal, logbook, milestone, dan monitoring progres project.</p>
                </div>
                <div class="sim-proposal-detail__col">
                    <p class="sim-proposal-detail__label">METODE</p>
                    <p class="sim-proposal-detail__text">Agile dengan iterasi sprint dua mingguan dan validasi bersama dosen pembimbing.</p>
                </div>
            </div>

            {{-- Attachment --}}
            <div class="sim-proposal-attachment">
                <div class="sim-proposal-attachment__icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <span class="sim-proposal-attachment__name">proposal-kelompok-01.pdf</span>
                <span class="sim-proposal-attachment__type">PDF</span>
            </div>

            {{-- Catatan Pembimbing --}}
            <div class="sim-catatan">
                <p class="sim-catatan__label">CATATAN PEMBIMBING</p>
                <p class="sim-catatan__text">Struktur proposal sudah baik. Lanjutkan ke implementasi modul logbook.</p>
            </div>
        </div>
    </div>

    {{-- RIGHT: Timeline --}}
    <div class="sim-proposal-sidebar">
        <div class="sim-timeline-card">
            <p class="sim-timeline-card__title">PERJALANAN PROPOSAL</p>
            <ul class="sim-timeline">
                <li class="sim-timeline__item sim-timeline__item--done">
                    <div class="sim-timeline__dot sim-timeline__dot--done">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="sim-timeline__content">
                        <p class="sim-timeline__event">Draf disusun</p>
                        <p class="sim-timeline__date">02 Mei 2024</p>
                    </div>
                </li>
                <li class="sim-timeline__item sim-timeline__item--done">
                    <div class="sim-timeline__dot sim-timeline__dot--done">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="sim-timeline__content">
                        <p class="sim-timeline__event">Dikirim untuk ditinjau</p>
                        <p class="sim-timeline__date">02 Mei 2024</p>
                    </div>
                </li>
                <li class="sim-timeline__item sim-timeline__item--done">
                    <div class="sim-timeline__dot sim-timeline__dot--done">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="sim-timeline__content">
                        <p class="sim-timeline__event">Disetujui pembimbing</p>
                        <p class="sim-timeline__date">06 Mei 2024</p>
                    </div>
                </li>
                <li class="sim-timeline__item sim-timeline__item--current sim-timeline__item--last">
                    <div class="sim-timeline__dot sim-timeline__dot--current">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="sim-timeline__content">
                        <p class="sim-timeline__event">Mulai pengerjaan</p>
                        <p class="sim-timeline__date sim-timeline__date--active">Berjalan</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- MODAL: Ajukan Proposal --}}
<div id="proposal-modal-overlay" class="sim-modal-overlay" onclick="handleOverlayClick(event)">
    <div class="sim-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="sim-modal__header">
            <div>
                <p class="sim-modal__eyebrow">RUANG KERJA</p>
                <h2 class="sim-modal__title" id="modal-title">Ajukan proposal baru</h2>
            </div>
            <button class="sim-modal__close" onclick="closeProposalModal()" aria-label="Tutup modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form id="proposal-form" class="sim-modal__form" onsubmit="submitProposal(event)" novalidate>
            <div class="sim-form-group">
                <label class="sim-form-label" for="field-judul">Judul proyek</label>
                <input
                    type="text"
                    id="field-judul"
                    name="judul"
                    class="sim-form-input"
                    placeholder="Masukkan judul proyek"
                    autocomplete="off"
                >
                <p class="sim-form-error" id="err-judul" role="alert"></p>
            </div>

            <div class="sim-form-group">
                <label class="sim-form-label" for="field-deskripsi">Deskripsi singkat</label>
                <textarea
                    id="field-deskripsi"
                    name="deskripsi"
                    class="sim-form-textarea"
                    placeholder="Apa yang ingin kelompokmu kerjakan?"
                    rows="3"
                ></textarea>
                <p class="sim-form-error" id="err-deskripsi" role="alert"></p>
            </div>

            <div class="sim-form-group">
                <label class="sim-form-label" for="field-latar">Latar belakang</label>
                <textarea
                    id="field-latar"
                    name="latar_belakang"
                    class="sim-form-textarea"
                    placeholder="Mengapa masalah ini penting?"
                    rows="3"
                ></textarea>
                <p class="sim-form-error" id="err-latar" role="alert"></p>
            </div>

            <div class="sim-form-group">
                <label class="sim-form-label" for="field-tujuan">Tujuan</label>
                <textarea
                    id="field-tujuan"
                    name="tujuan"
                    class="sim-form-textarea"
                    placeholder="Perubahan apa yang ingin dicapai?"
                    rows="3"
                ></textarea>
                <p class="sim-form-error" id="err-tujuan" role="alert"></p>
            </div>

            <div class="sim-form-group">
                <label class="sim-form-label" for="field-metode">Metode kerja</label>
                <textarea
                    id="field-metode"
                    name="metode"
                    class="sim-form-textarea"
                    placeholder="Bagaimana kelompokmu akan mengerjakannya?"
                    rows="3"
                ></textarea>
                <p class="sim-form-error" id="err-metode" role="alert"></p>
            </div>

            <div class="sim-modal__footer">
                <button type="button" class="sim-btn-secondary" onclick="closeProposalModal()">Batal</button>
                <button type="submit" class="sim-btn-primary" id="btn-submit-proposal">Kirim proposal</button>
            </div>
        </form>
    </div>
</div>

{{-- Toast Notification --}}
<div id="sim-toast" class="sim-toast" role="alert" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
// ============================================================
// MODAL CONTROL
// ============================================================
function openProposalModal() {
    const overlay = document.getElementById('proposal-modal-overlay');
    overlay.classList.add('sim-modal-overlay--visible');
    document.body.style.overflow = 'hidden';
    // Reset form
    document.getElementById('proposal-form').reset();
    clearAllErrors();
}

function closeProposalModal() {
    const overlay = document.getElementById('proposal-modal-overlay');
    overlay.classList.remove('sim-modal-overlay--visible');
    document.body.style.overflow = '';
}

function handleOverlayClick(event) {
    if (event.target === document.getElementById('proposal-modal-overlay')) {
        closeProposalModal();
    }
}

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeProposalModal();
});

// ============================================================
// VALIDATION
// ============================================================
function clearAllErrors() {
    ['judul', 'deskripsi', 'latar', 'tujuan', 'metode'].forEach(function(key) {
        const errEl = document.getElementById('err-' + key);
        if (errEl) { errEl.textContent = ''; errEl.style.display = 'none'; }
        const inputEl = document.getElementById('field-' + key);
        if (inputEl) inputEl.classList.remove('sim-form-input--error', 'sim-form-textarea--error');
    });
}

function showError(fieldKey, message) {
    const errEl = document.getElementById('err-' + fieldKey);
    if (errEl) { errEl.textContent = message; errEl.style.display = 'block'; }
    const inputEl = document.getElementById('field-' + fieldKey);
    if (inputEl) {
        inputEl.classList.add(
            inputEl.tagName === 'INPUT' ? 'sim-form-input--error' : 'sim-form-textarea--error'
        );
    }
}

function validateForm(data) {
    let valid = true;
    clearAllErrors();

    if (!data.judul.trim()) {
        showError('judul', 'Judul proyek wajib diisi.');
        valid = false;
    }
    if (!data.deskripsi.trim()) {
        showError('deskripsi', 'Deskripsi singkat wajib diisi.');
        valid = false;
    }
    if (!data.latar.trim()) {
        showError('latar', 'Latar belakang wajib diisi.');
        valid = false;
    }
    if (!data.tujuan.trim()) {
        showError('tujuan', 'Tujuan wajib diisi.');
        valid = false;
    }
    if (!data.metode.trim()) {
        showError('metode', 'Metode kerja wajib diisi.');
        valid = false;
    }
    return valid;
}

// ============================================================
// SUBMIT
// ============================================================
function submitProposal(event) {
    event.preventDefault();

    const data = {
        judul: document.getElementById('field-judul').value,
        deskripsi: document.getElementById('field-deskripsi').value,
        latar: document.getElementById('field-latar').value,
        tujuan: document.getElementById('field-tujuan').value,
        metode: document.getElementById('field-metode').value,
    };

    if (!validateForm(data)) return;

    const btn = document.getElementById('btn-submit-proposal');
    btn.disabled = true;
    btn.textContent = 'Mengirim...';

    // Simulate submit (backend belum tersedia — local state)
    setTimeout(function() {
        btn.disabled = false;
        btn.textContent = 'Kirim proposal';
        closeProposalModal();
        showToast('Proposal berhasil diajukan! Status: Diajukan.', 'success');
    }, 800);
}

// ============================================================
// TOAST
// ============================================================
function showToast(message, type) {
    const toast = document.getElementById('sim-toast');
    toast.textContent = message;
    toast.className = 'sim-toast sim-toast--' + type + ' sim-toast--visible';
    setTimeout(function() {
        toast.classList.remove('sim-toast--visible');
    }, 4000);
}
</script>
@endpush
