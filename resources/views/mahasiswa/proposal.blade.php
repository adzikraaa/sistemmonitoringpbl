@extends('layouts.mahasiswa')

@section('title', 'Proposal')
@section('meta_description', 'Halaman Proposal Mahasiswa SIM-PBL')

@php
    $proposalData = $proposal ?? [];
    $proposalStatus = $proposalData['status'] ?? 'Diajukan';
    $statusLower = strtolower($proposalStatus);
    $proposalStatusClass = $statusLower === 'disetujui' ? 'sim-badge--approved' : (($statusLower === 'ditolak' || $statusLower === 'revisi') ? 'sim-badge--danger' : 'sim-badge--warning');
@endphp

@section('content')
@if(session('success'))
    <div style="margin: 0 32px 20px; padding: 14px 20px; background: #EBF7EE; border: 1px solid #B8E4C4; border-radius: 12px; color: #1E6B37; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if($errors->any())
    <div style="margin: 0 32px 20px; padding: 14px 20px; background: #FDE8E8; border: 1px solid #F8B4B4; border-radius: 12px; color: #9B1C1C; font-size: 13px; font-weight: 500;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
{{-- Page Header --}}
<div class="sim-page-header prop-page-header">
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
<div class="sim-proposal-grid prop-page">

    {{-- LEFT: Proposal Card --}}
    <div class="sim-proposal-main">
        <div class="sim-proposal-card">
            {{-- Card header --}}
            <div class="sim-proposal-card__header">
                <div class="sim-proposal-card__meta">
                    <span class="sim-proposal-card__version">{{ $proposalData['versi'] ?? 'VERSI 01' }}</span>
                    <span class="sim-badge {{ $proposalStatusClass }}">&#9679; {{ $proposalStatus }}</span>
                </div>
                <span class="sim-proposal-card__date">{{ $proposalData['tanggal'] ?? '26 September 2026' }}</span>
            </div>

            <div class="sim-status-box" style="margin-top: 1rem; margin-bottom: 1rem;">
                <p class="sim-status-box__label" style="margin: 0 0 0.5rem; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.12em; color: #64748b;">Status Proposal</p>
                <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                    <span class="sim-badge {{ $proposalStatusClass }}">{{ $proposalStatus }}</span>
                    <span style="font-size: 0.82rem; color: #64748b;">{{ $proposalData['tanggal'] ?? '26 September 2026' }}</span>
                </div>
            </div>

            {{-- Card title & desc --}}
            <h2 class="sim-proposal-card__title">{{ $proposalData['judul'] ?? 'Pengembangan Sistem Monitoring PBL' }}</h2>
            <p class="sim-proposal-card__desc">{{ $proposalData['deskripsi'] ?? 'Platform web untuk membantu mahasiswa, dosen, dan koordinator memantau progres Project Based Learning secara terstruktur.' }}</p>

            {{-- Three-column detail --}}
            <div class="sim-proposal-detail">
                <div class="sim-proposal-detail__col">
                    <p class="sim-proposal-detail__label">LATAR BELAKANG</p>
                    <p class="sim-proposal-detail__text">{{ $proposalData['latar_belakang'] ?? 'Proses monitoring saat ini masih tersebar di berbagai kanal sehingga histori dan progres sulit dipantau secara utuh.' }}</p>
                </div>
                <div class="sim-proposal-detail__col">
                    <p class="sim-proposal-detail__label">TUJUAN</p>
                    <p class="sim-proposal-detail__text">{{ $proposalData['tujuan'] ?? 'Membangun satu sistem terpusat untuk proposal, logbook, milestone, dan monitoring progres project.' }}</p>
                </div>
                <div class="sim-proposal-detail__col">
                    <p class="sim-proposal-detail__label">METODE</p>
                    <p class="sim-proposal-detail__text">{{ $proposalData['metode'] ?? 'Agile dengan iterasi sprint dua mingguan dan validasi bersama dosen pembimbing.' }}</p>
                </div>
            </div>

            {{-- Attachment --}}
            @if(!empty($proposalData['file_url']))
                <a href="{{ $proposalData['file_url'] }}" target="_blank" class="sim-proposal-attachment" style="text-decoration: none; cursor: pointer;" title="Klik untuk mengunduh/melihat berkas proposal PDF">
                    <div class="sim-proposal-attachment__icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <span class="sim-proposal-attachment__name">{{ $proposalData['file_name'] ?? 'proposal-kelompok.pdf' }}</span>
                    <span class="sim-proposal-attachment__type" style="display: inline-flex; align-items: center; gap: 4px;">
                        PDF
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </span>
                </a>
            @else
                <div class="sim-proposal-attachment">
                    <div class="sim-proposal-attachment__icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <span class="sim-proposal-attachment__name">{{ $proposalData['file_name'] ?? 'proposal-kelompok-01.pdf' }}</span>
                    <span class="sim-proposal-attachment__type">PDF</span>
                </div>
            @endif

            {{-- Feedback Pembimbing --}}
            <div class="sim-catatan">
                <p class="sim-catatan__label">Feedback Pembimbing</p>
                <p class="sim-catatan__text">{{ $proposalData['feedback'] ?? 'Struktur proposal sudah baik. Lanjutkan ke implementasi modul logbook.' }}</p>
            </div>
        </div>
    </div>

    {{-- RIGHT: Timeline --}}
    <div class="sim-proposal-sidebar">
        <div class="sim-timeline-card">
            <p class="sim-timeline-card__title">PERJALANAN PROPOSAL</p>
            <ul class="sim-timeline">
                @foreach($proposalData['timeline'] ?? [] as $step)
                    @php
                        $isDone = ($step['state'] ?? '') === 'done';
                        $isCurrent = ($step['state'] ?? '') === 'current';
                    @endphp
                    <li class="sim-timeline__item {{ $isDone ? 'sim-timeline__item--done' : ($isCurrent ? 'sim-timeline__item--current sim-timeline__item--last' : '') }}">
                        <div class="sim-timeline__dot {{ $isDone ? 'sim-timeline__dot--done' : ($isCurrent ? 'sim-timeline__dot--current' : '') }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="sim-timeline__content">
                            <p class="sim-timeline__event">{{ $step['event'] }}</p>
                            <p class="sim-timeline__date {{ $isCurrent ? 'sim-timeline__date--active' : '' }}">{{ $step['date'] }}</p>
                        </div>
                    </li>
                @endforeach
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

        <form id="proposal-form" class="sim-modal__form" action="{{ route('mahasiswa.proposal.store') }}" method="POST" enctype="multipart/form-data" onsubmit="submitProposal(event)" novalidate>
            @csrf

            <div class="sim-form-group">
                <label class="sim-form-label" for="field-judul">Judul proyek <span style="color: #e11d48;">*</span></label>
                <input
                    type="text"
                    id="field-judul"
                    name="judul"
                    class="sim-form-input"
                    placeholder="Masukkan judul proyek"
                    value="{{ old('judul') }}"
                    required
                    autocomplete="off"
                >
                <p class="sim-form-error" id="err-judul" role="alert"></p>
            </div>

            <div class="sim-form-group">
                <label class="sim-form-label" for="field-deskripsi">Deskripsi singkat <span style="color: #e11d48;">*</span></label>
                <textarea
                    id="field-deskripsi"
                    name="deskripsi"
                    class="sim-form-textarea"
                    placeholder="Apa yang ingin kelompokmu kerjakan?"
                    rows="3"
                    required
                >{{ old('deskripsi') }}</textarea>
                <p class="sim-form-error" id="err-deskripsi" role="alert"></p>
            </div>

            {{-- Dropzone Upload Berkas Proposal PDF (mengadopsi UI /mahasiswa/milestone/pemrograman-web-lanjut) --}}
            <div class="sim-form-group">
                <label class="sim-form-label" for="fileProposalInput">
                    Pengumpulan Berkas Proposal <span style="color: #e11d48;">*</span> 
                    <span style="font-weight: 400; color: #7D746D; font-size: 11px;">(PDF maks 20 MB)</span>
                </label>
                <div onclick="document.getElementById('fileProposalInput').click()" 
                     id="proposalDropzone"
                     style="border: 2px dashed #CFC7BC; border-radius: 12px; padding: 22px 16px; text-align: center; background: rgba(247, 246, 240, 0.6); cursor: pointer; transition: all 0.2s ease;">
                    <input type="file" name="file_proposal" id="fileProposalInput" class="hidden" accept=".pdf" required onchange="previewProposalFile(this)" style="display: none;">
                    
                    <div id="proposalDropzoneEmpty">
                        <svg style="width: 32px; height: 32px; color: #7D746D; margin: 0 auto 8px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span style="font-weight: 700; color: #1F1F1F; display: block; font-size: 13px;">Tarik berkas atau <span style="color: #7A5C4A; text-decoration: underline;">klik untuk memilih</span></span>
                        <span style="color: #7D746D; font-size: 11px; display: block; margin-top: 4px;">Format file didukung: PDF (Maks. 20 MB)</span>
                    </div>

                    <div id="proposalDropzoneFilled" style="display: none; color: #2F5C4A;">
                        <svg style="width: 32px; height: 32px; margin: 0 auto 8px; display: block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span style="font-weight: 700; font-size: 13px; display: block;" id="proposalFileName"></span>
                        <span style="font-size: 11px; color: #7D746D; display: block; margin-top: 2px;" id="proposalFileSize"></span>
                        <span style="font-size: 11px; color: #7A5C4A; text-decoration: underline; margin-top: 6px; display: inline-block;">Ganti berkas</span>
                    </div>
                </div>
                <p class="sim-form-error" id="err-file_proposal" role="alert" style="display: none; color: #e11d48; font-size: 11.5px; margin-top: 4px;"></p>
            </div>

            <div class="sim-form-group">
                <label class="sim-form-label" for="field-latar">Latar belakang <span style="color: #e11d48;">*</span></label>
                <textarea
                    id="field-latar"
                    name="latar_belakang"
                    class="sim-form-textarea"
                    placeholder="Mengapa masalah ini penting?"
                    rows="3"
                    required
                >{{ old('latar_belakang') }}</textarea>
                <p class="sim-form-error" id="err-latar" role="alert"></p>
            </div>

            <div class="sim-form-group">
                <label class="sim-form-label" for="field-tujuan">Tujuan <span style="color: #e11d48;">*</span></label>
                <textarea
                    id="field-tujuan"
                    name="tujuan"
                    class="sim-form-textarea"
                    placeholder="Perubahan apa yang ingin dicapai?"
                    rows="3"
                    required
                >{{ old('tujuan') }}</textarea>
                <p class="sim-form-error" id="err-tujuan" role="alert"></p>
            </div>

            <div class="sim-form-group">
                <label class="sim-form-label" for="field-metode">Metode kerja <span style="color: #e11d48;">*</span></label>
                <textarea
                    id="field-metode"
                    name="metode"
                    class="sim-form-textarea"
                    placeholder="Bagaimana kelompokmu akan mengerjakannya?"
                    rows="3"
                    required
                >{{ old('metode') }}</textarea>
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

@push('styles')
<style>
/* ── Proposal — responsive reinforcement (page-scoped, overrides app.css) ── */
.prop-page-header { flex-wrap: wrap; }
.prop-page-header .sim-page-header__desc { overflow-wrap: anywhere; }

.prop-page,
.prop-page .sim-proposal-main,
.prop-page .sim-proposal-sidebar,
.prop-page .sim-proposal-card { min-width: 0; }

.prop-page .sim-proposal-card__title,
.prop-page .sim-proposal-card__desc,
.prop-page .sim-proposal-detail__text,
.prop-page .sim-catatan__text,
.prop-page .sim-timeline__event,
.prop-page .sim-timeline__date,
.prop-page .sim-proposal-attachment__name { overflow-wrap: anywhere; }

.prop-page .sim-proposal-card__header { flex-wrap: wrap; row-gap: 8px; }
.prop-page .sim-proposal-card__meta { flex-wrap: wrap; }
.prop-page .sim-status-box > div { flex-wrap: wrap; gap: 0.5rem; }
.prop-page .sim-proposal-attachment__name { min-width: 0; }
.prop-page .sim-badge { white-space: nowrap; flex-shrink: 0; max-width: 100%; }

@media (max-width: 1024px) {
    .prop-page { grid-template-columns: minmax(0, 1fr) 240px; }
}

@media (max-width: 768px) {
    .prop-page { grid-template-columns: minmax(0, 1fr); }
    .prop-page .sim-proposal-detail { grid-template-columns: minmax(0, 1fr); }
    #btn-ajukan-proposal,
    .prop-page .sim-btn-primary,
    .prop-page .sim-btn-secondary { min-height: 44px; }
    .prop-page .sim-proposal-detail__label,
    .prop-page .sim-catatan__label,
    .prop-page .sim-timeline-card__title { font-size: 11px; }
    #proposal-modal-overlay .sim-modal__eyebrow { font-size: 11px; }
    #proposal-modal-overlay .sim-modal__close { width: 44px; height: 44px; }
}

@media (max-width: 480px) {
    #proposal-modal-overlay { padding: 16px; }
    #proposal-modal-overlay .sim-modal {
        width: 100%;
        max-width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        border-radius: 16px;
    }
    #proposal-modal-overlay .sim-modal__header { padding: 20px 16px 0; }
    #proposal-modal-overlay .sim-modal__form { padding: 16px; }
    #proposal-modal-overlay .sim-modal__form .sim-form-input,
    #proposal-modal-overlay .sim-modal__form .sim-form-textarea {
        min-width: 0;
        max-width: 100%;
    }
    #proposal-modal-overlay .sim-modal__footer {
        flex-direction: column-reverse;
        align-items: stretch;
        gap: 8px;
    }
    #proposal-modal-overlay .sim-modal__footer .sim-btn-primary,
    #proposal-modal-overlay .sim-modal__footer .sim-btn-secondary {
        width: 100%;
        min-height: 44px;
        justify-content: center;
    }
    #sim-toast { left: 16px; right: 16px; max-width: none; }
}
</style>
@endpush

@push('scripts')
<script>
// ============================================================
// MODAL CONTROL & DROPZONE
// ============================================================
function openProposalModal() {
    const overlay = document.getElementById('proposal-modal-overlay');
    overlay.classList.add('sim-modal-overlay--visible');
    document.body.style.overflow = 'hidden';
    // Reset form
    document.getElementById('proposal-form').reset();
    resetProposalDropzone();
    clearAllErrors();
}

function closeProposalModal() {
    const overlay = document.getElementById('proposal-modal-overlay');
    overlay.classList.remove('sim-modal-overlay--visible');
    document.body.style.overflow = '';
    const form = document.getElementById('proposal-form');
    if (form) form.reset();
    resetProposalDropzone();
    clearAllErrors();
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

// Dropzone file handling (Referensi dari milestone-detail)
function previewProposalFile(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
            alert('Format berkas harus berupa PDF!');
            input.value = '';
            resetProposalDropzone();
            return;
        }

        if (file.size > 20 * 1024 * 1024) {
            alert('Ukuran berkas melebihi batas maksimal 20 MB!');
            input.value = '';
            resetProposalDropzone();
            return;
        }

        const emptyEl = document.getElementById('proposalDropzoneEmpty');
        const filledEl = document.getElementById('proposalDropzoneFilled');
        const nameEl = document.getElementById('proposalFileName');
        const sizeEl = document.getElementById('proposalFileSize');
        const errEl = document.getElementById('err-file_proposal');

        if (emptyEl) emptyEl.style.display = 'none';
        if (filledEl) filledEl.style.display = 'block';
        if (nameEl) nameEl.textContent = '📄 ' + file.name;
        if (sizeEl) sizeEl.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
        if (errEl) { errEl.textContent = ''; errEl.style.display = 'none'; }
    }
}

function resetProposalDropzone() {
    const emptyEl = document.getElementById('proposalDropzoneEmpty');
    const filledEl = document.getElementById('proposalDropzoneFilled');
    if (emptyEl) emptyEl.style.display = 'block';
    if (filledEl) filledEl.style.display = 'none';
}

// Drag & drop support
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('proposalDropzone');
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = '#7A5C4A';
                dropzone.style.background = '#EDE8DE';
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = '#CFC7BC';
                dropzone.style.background = 'rgba(247, 246, 240, 0.6)';
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                const input = document.getElementById('fileProposalInput');
                input.files = files;
                previewProposalFile(input);
            }
        });
    }
});

// ============================================================
// VALIDATION
// ============================================================
function clearAllErrors() {
    ['judul', 'deskripsi', 'latar', 'tujuan', 'metode', 'file_proposal'].forEach(function(key) {
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

    const fileInput = document.getElementById('fileProposalInput');
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        showError('file_proposal', 'Berkas proposal (PDF) wajib diunggah.');
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
    btn.textContent = 'Mengunggah proposal...';

    // Submit form secara langsung ke backend Laravel
    document.getElementById('proposal-form').submit();
}

// ============================================================
// TOAST
// ============================================================
function showToast(message, type) {
    const toast = document.getElementById('sim-toast');
    if (!toast) return;
    toast.textContent = message;
    toast.className = 'sim-toast sim-toast--' + type + ' sim-toast--visible';
    setTimeout(function() {
        toast.classList.remove('sim-toast--visible');
    }, 4000);
}
</script>
@endpush
