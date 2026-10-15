@extends('layouts.mahasiswa')

@section('title', 'Detail Pengumpulan Milestone')
@section('meta_description', 'Halaman Pengumpulan Milestone Mahasiswa SIM-PBL')

@section('content')
<div style="max-width: 920px; margin: 0 auto;">

    {{-- Tombol Kembali --}}
    <div style="margin-bottom: 1.25rem;">
        <a href="{{ route('mahasiswa.milestone') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; color: #666; font-size: 0.85rem; font-weight: 500; text-decoration: none;">
            &#8592; Kembali ke Milestone
        </a>
    </div>

    {{-- Header Milestone --}}
    <div style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.4rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 0.8rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.05em;">
                    MILESTONE {{ $milestone['kode'] }}
                </span>
            </div>
            <div style="text-align: right;">
                <span style="display: block; font-size: 0.65rem; color: #888; text-transform: uppercase; letter-spacing: 0.05em;">TENGGAT</span>
                <span style="font-weight: 600; color: #333; font-size: 0.85rem;">{{ $milestone['tenggat'] }}</span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <h1 style="font-size: 1.8rem; font-weight: 700; color: #1a1a1a; margin: 0;">{{ $milestone['judul'] }}</h1>
            <span class="sim-badge {{ $milestone['badge'] }}" style="padding: 0.2rem 0.55rem; font-size: 0.75rem;">&#9679; {{ $milestone['status'] }}</span>
        </div>

        <p style="font-size: 0.9rem; color: #666; margin: 0;">{{ $milestone['deskripsi'] }}</p>
    </div>

    {{-- Box Form Pengumpulan --}}
    <div class="sim-proposal-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.2rem; font-weight: 600; color: #222; margin-top: 0; margin-bottom: 0.3rem;">Pengumpulan</h2>
        <p style="font-size: 0.85rem; color: #666; margin-bottom: 1.25rem;">
            Unggah hasil implementasi modul inti untuk ditinjau dosen pembimbing.
        </p>

        <form action="{{ route('mahasiswa.milestone.upload', $milestone['id']) }}" method="POST" enctype="multipart/form-data" id="form-upload-milestone">
            @csrf

        <div style="margin-bottom: 1.25rem;">
        <label for="deliverable-item" style="display: block; font-size: 0.85rem; font-weight: 600; color: #333; margin-bottom: 0.4rem;">
            Pilih Item Deliverable yang Diajukan / Diperbaiki:
        </label>
        <select name="deliverable_item" id="deliverable-item" style="
            width: 100%;
            padding: 0.6rem 0.8rem;
            font-size: 0.85rem;
            color: #333;
            background-color: #fff;
            border: 1px solid #dcd6cd;
            border-radius: 6px;
            outline: none;
            cursor: pointer;
        ">
            <option value="all">Semua Item (Bundel Milestone {{ $milestone['kode'] }})</option>
            <option value="erd">ERD & Kamus Data Fisik (Perlu Revisi)</option>
            <option value="srs">Dokumen Spesifikasi Kebutuhan / SRS (Progres 60%)</option>
        </select>
    </div>
            
            {{-- Dropzone / Upload Area --}}
            <div id="drop-area" onclick="document.getElementById('file-input').click()" style="
                border: 2px dashed #d0c8b8;
                border-radius: 8px;
                padding: 2.5rem 1.5rem;
                text-align: center;
                background-color: #fdfbf7;
                cursor: pointer;
                transition: all 0.2s ease;
            ">
                <input type="file" id="file-input" name="berkas" accept=".pdf,.zip" style="display: none;" onchange="handleFileSelect(this)">
                
                {{-- Container Ikon SVG --}}
                <div style="width: 44px; height: 44px; border-radius: 50%; background-color: #f3efe6; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; color: #8c7355;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="12" y1="18" x2="12" y2="12"></line>
                        <polyline points="9 15 12 12 15 15"></polyline>
                    </svg>
                </div>

                <p id="file-label-main" style="margin: 0; font-size: 0.9rem; font-weight: 600; color: #333;">
                    Tarik berkas ke sini atau pilih dari perangkat
                </p>
                <p style="margin: 0.25rem 0 0.85rem 0; font-size: 0.75rem; color: #888;">
                    PDF atau ZIP &bull; Maksimal 20 MB
                </p>

                <button type="button" class="sim-btn-secondary" style="padding: 0.4rem 0.9rem; font-size: 0.8rem; background-color: #efebe4; border: 1px solid #dcd6cd; color: #4a3b32;">
                    <span id="btn-file-text">Pilih berkas</span>
                </button>
            </div>

            {{-- Footer Upload Status & Submit Button --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.25rem; pt-3; border-top: 1px solid #f0f0f0;">
                <span id="file-selected-status" style="font-size: 0.82rem; color: #777;">
                    Belum ada berkas yang dipilih.
                </span>

                <button type="submit" id="btn-submit-tugas" class="sim-btn-primary" disabled style="
                    padding: 0.5rem 1.2rem;
                    font-size: 0.85rem;
                    opacity: 0.5;
                    cursor: not-allowed;
                ">
                    Kumpulkan tugas
                </button>
            </div>
        </form>
    </div>

    {{-- Catatan Dosen --}}
    @if(!empty($milestone['catatan']))
    <div style="margin-top: 1.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #222; margin-bottom: 0.75rem;">Catatan Dosen</h3>
        <div style="
            padding: 1rem 1.25rem;
            background-color: #faf9f6;
            border-left: 3px solid #8c7355;
            border-radius: 4px;
        ">
            <p style="font-style: italic; font-size: 0.9rem; color: #444; margin: 0 0 0.5rem 0;">
                "{{ $milestone['catatan'] }}"
            </p>
            <span style="font-size: 0.75rem; color: #777; font-weight: 500;">
                Dr. Ahmad Fikri, S.Kom, M.T. &bull; Dosen Pembimbing
            </span>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function handleFileSelect(input) {
    const file = input.files[0];
    const statusText = document.getElementById('file-selected-status');
    const submitBtn = document.getElementById('btn-submit-tugas');
    const btnFileText = document.getElementById('btn-btn-file-text');

    if (file) {
        // Cek ukuran file (Maks 20MB)
        if (file.size > 20 * 1024 * 1024) {
            alert('Ukuran berkas melebihi batas maksimal 20 MB!');
            input.value = '';
            statusText.textContent = 'Belum ada berkas yang dipilih.';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
            return;
        }

        statusText.innerHTML = `<strong style="color: #2e7d32;">Berkas dipilih:</strong> ${file.name} (${(file.size / (1024*1024)).toFixed(2)} MB)`;
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
    } else {
        statusText.textContent = 'Belum ada berkas yang dipilih.';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor = 'not-allowed';
    }
}
</script>
@endpush