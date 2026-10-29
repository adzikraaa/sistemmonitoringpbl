@extends('layouts.mahasiswa')

@section('title', 'Milestone')
@section('meta_description', 'Halaman Peta Perjalanan Milestone Mahasiswa SIM-PBL')

@section('content')

{{-- Header Halaman --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <p class="sim-page-header__eyebrow">PETA PERJALANAN PROYEK</p>
        <h1 class="sim-page-header__title">Milestone</h1>
        <p class="sim-page-header__desc">
            Kelompok 01 &ndash; SIB 3B &middot; Pemantauan capaian target dan pengumpulan berkas milestone proyek.
        </p>
    </div>
</div>

{{-- Content Wrap --}}
<div class="sim-lb-wrap">

    {{-- Context Banner Transparan (Info Bar) --}}
    <div class="sim-lb-info-bar" style="margin-bottom: 1.25rem;">
        <span class="sim-lb-info-bar__icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
        </span>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between; width: 100%;">
            <span>
                <strong>Kelompok 01 &bull; SIB 3B</strong>
                Progres dihitung otomatis dari persentase deliverables yang telah disetujui pembimbing.
            </span>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="sim-badge sim-badge--approved" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">Status: On-Track</span>
                <button onclick="toggleRumusInfo()" class="sim-btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; background: #ffffff;">
                    &#128712; Formula Progres
                </button>
            </div>
        </div>
    </div>

    {{-- Collapsible Box Info Rumus --}}
    <div id="box-rumus-info" style="display: none; margin-top: -0.75rem; margin-bottom: 1.25rem; padding: 0.75rem 1rem; background-color: #fdfbf7; border-radius: 8px; border-left: 3px solid #4a3b32; font-size: 0.82rem; color: #444;">
        <p style="margin: 0; font-weight: 600; color: #222;">Transparansi Perhitungan Progres:</p>
        <p style="margin: 0.2rem 0 0 0;">
            Progres tiap milestone dihitung berdasarkan proporsi: 
            <strong>(Deliverables Terverifikasi / Total Deliverables) &times; 100%</strong>. Validasi dilakukan oleh Dosen Pembimbing.
        </p>
    </div>

    {{-- List Timeline Milestone sesuai Figma --}}
    <div style="display: flex; flex-direction: column; gap: 1.5rem; position: relative;">
        
        {{-- Garis Vertikal Timeline di Sebelah Kiri --}}
        <div style="
            position: absolute;
            top: 20px;
            bottom: 20px;
            left: 17px;
            width: 2px;
            background-color: #e0e0e0;
            z-index: 1;
        "></div>

        @foreach($milestones as $m)
        {{-- Wrapper Item (Flex Horizontal) --}}
        <div style="display: flex; gap: 1.25rem; align-items: flex-start; position: relative; z-index: 2;">
            
            {{-- Lingkaran M1, M2, dst (Di Luar Card) --}}
            <span style="
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background-color: {{ $m['status'] == 'Berjalan' ? '#d97706' : ($m['status'] == 'Selesai' ? '#2e7d32' : '#888888') }};
                color: #ffffff;
                font-weight: 700;
                font-size: 0.8rem;
                flex-shrink: 0;
                margin-top: 0.2rem;
                box-shadow: 0 0 0 4px #fbf9f5;
            ">
                {{ $m['kode'] }}
            </span>

            {{-- Card Utama (Di Sebelah Kanan Lingkaran) --}}
            <div class="sim-proposal-card" style="flex: 1; padding: 1.25rem; border-left: none;">
                
                {{-- Header Card --}}
                <div class="sim-proposal-card__header" style="margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <div class="sim-proposal-card__meta" style="display: flex; align-items: center; gap: 0.6rem;">
                        <h2 class="sim-proposal-card__title" style="margin: 0; font-size: 1.15rem; color: #1a1a1a;">
                            {{ $m['judul'] }}
                        </h2>
                        <span class="sim-badge {{ $m['badge'] }}" style="padding: 0.15rem 0.45rem; font-size: 0.7rem;">
                            &#9679; {{ $m['status'] }}
                        </span>
                    </div>

                    <div style="text-align: right;">
                        <span style="display: block; font-size: 0.65rem; color: #888; text-transform: uppercase; letter-spacing: 0.05em;">TENGGAT</span>
                        <span class="sim-proposal-card__date" style="font-weight: 600; color: #333; font-size: 0.85rem;">{{ $m['tenggat'] }}</span>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <p class="sim-proposal-card__desc" style="margin-bottom: 0.85rem; font-size: 0.88rem; color: #666;">{{ $m['deskripsi'] }}</p>

                {{-- Progress Bar --}}
                <div style="margin-bottom: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #555; margin-bottom: 0.25rem; font-weight: 600;">
                        <span>Progres Capaian</span>
                        <span>{{ $m['progress'] }}%</span>
                    </div>
                    <div style="width: 100%; background-color: #e5e5e5; height: 6px; border-radius: 999px; overflow: hidden;">
                        <div style="
                            width: {{ $m['progress'] }}%; 
                            background-color: {{ $m['status'] == 'Selesai' ? '#2e7d32' : ($m['status'] == 'Berjalan' ? '#d97706' : '#aaaaaa') }}; 
                            height: 100%; 
                            border-radius: 999px;
                            transition: width 0.4s ease;
                        "></div>
                    </div>
                </div>

                {{-- Catatan Dosen Umum --}}
                @if(!empty($m['catatan']))
                <div class="sim-catatan" style="margin-bottom: 0.85rem; padding: 0.6rem 0.85rem; background-color: rgba(0,0,0,0.02); border-radius: 6px; border-left: 2px solid #888;">
                    <p class="sim-catatan__label" style="font-size: 0.65rem; margin-bottom: 0.15rem;">CATATAN PEMBIMBING</p>
                    <p class="sim-catatan__text" style="font-style: italic; font-size: 0.82rem; margin: 0; color: #444;">
                        "{{ $m['catatan'] }}"
                    </p>
                </div>
                @endif

                {{-- List Grid Deliverables --}}
                @if(!empty($m['deliverables']))
                <div style="margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px dashed #e0e0e0;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0.65rem;">
                        @foreach($m['deliverables'] as $item)
                        {{-- Card Deliverable Interaktif --}}
                        <div onclick="openDeliverableModal({{ json_encode($item) }}, '{{ $m['kode'] }}')" 
                             style="
                                background-color: #ffffff; 
                                border: 1px solid #e2e2e2; 
                                border-radius: 8px; 
                                padding: 0.75rem 0.9rem;
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                cursor: pointer;
                                transition: all 0.2s ease;
                                box-shadow: 0 1px 2px rgba(0,0,0,0.02);
                             "
                             onmouseover="this.style.borderColor='#8c7355'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.06)';"
                             onmouseout="this.style.borderColor='#e2e2e2'; this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 2px rgba(0,0,0,0.02)';"
                        >
                            <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden; padding-right: 0.5rem;">
                                <span style="color: #8c7355; font-size: 0.9rem;"></span>
                                <span style="color: #222; font-weight: 500; font-size: 0.83rem; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $item['nama'] }}
                                </span>
                            </div>

                            <span style="
                                font-size: 0.68rem; 
                                font-weight: 600; 
                                padding: 0.2rem 0.55rem; 
                                border-radius: 6px;
                                flex-shrink: 0;
                                background-color: {{ $item['status'] == 'Disetujui' ? '#e8f5e9' : ($item['status'] == 'Perlu Revisi' ? '#fff3e0' : '#f5f5f5') }};
                                color: {{ $item['status'] == 'Disetujui' ? '#2e7d32' : ($item['status'] == 'Perlu Revisi' ? '#e65100' : '#666666') }};
                            ">
                                {{ $item['status'] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endforeach

    </div>
</div>

{{-- MODAL DETAIL & FORM PENGUMPULAN --}}
<div id="modal-deliverable" style="
    display: none; 
    position: fixed; 
    top: 0; left: 0; width: 100%; height: 100%; 
    background-color: rgba(0,0,0,0.5); 
    z-index: 9999; 
    align-items: center; 
    justify-content: center;
">
    <div style="
        background-color: #ffffff; 
        width: 90%; 
        max-width: 560px; 
        border-radius: 12px; 
        padding: 1.5rem; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        position: relative;
    ">
        {{-- Close Button --}}
        <button onclick="closeDeliverableModal()" style="
            position: absolute; top: 1rem; right: 1rem; 
            background: none; border: none; font-size: 1.3rem; 
            color: #888; cursor: pointer;
        ">&times;</button>

        {{-- Header Modal --}}
        <div style="margin-bottom: 1rem; border-bottom: 1px solid #f0f0f0; padding-bottom: 0.75rem;">
            <span id="modal-milestone-tag" style="font-size: 0.7rem; font-weight: 700; color: #8c7355; text-transform: uppercase;"></span>
            <h3 id="modal-item-title" style="margin: 0.2rem 0 0.4rem 0; font-size: 1.15rem; color: #222;"></h3>
            <div>
                <span id="modal-item-badge" class="sim-badge" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;"></span>
            </div>
        </div>

        {{-- Catatan Khusus Dosen --}}
        <div id="modal-catatan-container" style="display: none; margin-bottom: 1rem; padding: 0.75rem 1rem; background-color: #fdfbf7; border-left: 3px solid #d97706; border-radius: 4px;">
            <p style="margin: 0; font-size: 0.7rem; font-weight: 700; color: #d97706;">CATATAN DOSEN UNTUK ITEM INI:</p>
            <p id="modal-item-catatan" style="margin: 0.25rem 0 0 0; font-size: 0.82rem; font-style: italic; color: #444;"></p>
        </div>

        {{-- Berkas Terakhir Diunggah (Histori) --}}
        <div id="modal-file-lama-container" style="display: none; margin-bottom: 1.25rem; padding: 0.6rem 0.8rem; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px;">
            <p style="margin: 0 0 0.2rem 0; font-size: 0.72rem; color: #6c757d; font-weight: 600;">BERKAS TERAKHIR DILAMPIRKAN:</p>
            <a id="modal-item-file-link" href="#" target="_blank" style="font-size: 0.82rem; color: #2b6cb0; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;">
                <span id="modal-item-file-name"></span> 
            </a>
        </div>

        {{-- Form Upload Berkas --}}
        <div id="modal-upload-form-container">
            <form action="{{ route('mahasiswa.milestone.upload', 'item') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="item_id" id="modal-input-item-id">

                <label id="modal-upload-label" style="display: block; font-size: 0.82rem; font-weight: 600; color: #333; margin-bottom: 0.4rem;">
                    Unggah Berkas Baru (Revisi / Kelengkapan):
                </label>

                {{-- Dropzone Area Interaktif Dinamis --}}
                <div id="drop-area-modal" onclick="document.getElementById('modal-file-input').click()" style="
                    border: 2px dashed #d0c8b8;
                    border-radius: 8px;
                    padding: 1.5rem 1rem;
                    text-align: center;
                    background-color: #fdfbf7;
                    cursor: pointer;
                    margin-bottom: 1.25rem;
                    transition: all 0.2s ease;
                ">
                    <input type="file" id="modal-file-input" name="berkas" accept=".pdf,.zip" style="display: none;" onchange="handleModalFileSelect(this)">
                    
                    {{-- STATE 1: BELUM PILIH FILE --}}
                    <div id="dropzone-empty-state">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f3efe6; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.5rem; color: #8c7355;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <polyline points="9 15 12 12 15 15"></polyline>
                            </svg>
                        </div>
                        <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: #333;">Tarik berkas ke sini atau klik pilih</p>
                        <p style="margin: 0.2rem 0 0 0; font-size: 0.72rem; color: #888;">PDF atau ZIP &bull; Maksimal 20 MB</p>
                    </div>

                    {{-- STATE 2: DITAMPILKAN SAAT BERKAS TERPILIH --}}
                    <div id="dropzone-selected-state" style="display: none;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e8f5e9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.4rem; color: #2e7d32;">
                            ✓
                        </div>
                        <p style="margin: 0; font-size: 0.75rem; color: #2e7d32; font-weight: 700; text-transform: uppercase;">BERKAS SIAP DIKIRIM:</p>
                        <p id="selected-file-name" style="margin: 0.2rem 0; font-size: 0.88rem; font-weight: 700; color: #1a1a1a; word-break: break-all;"></p>
                        <p id="selected-file-size" style="margin: 0 0 0.6rem 0; font-size: 0.72rem; color: #666;"></p>
                        <button type="button" onclick="event.stopPropagation(); document.getElementById('modal-file-input').click();" style="
                            padding: 0.25rem 0.65rem; 
                            font-size: 0.72rem; 
                            background-color: #efebe4; 
                            border: 1px solid #dcd6cd; 
                            color: #4a3b32; 
                            border-radius: 4px; 
                            cursor: pointer;
                            font-weight: 600;
                        ">
                            Pilih berkas lain
                        </button>
                    </div>
                </div>

                {{-- Footer Submit Button --}}
                <div style="text-align: right;">
                    <button type="submit" id="modal-btn-submit" disabled style="
                        padding: 0.5rem 1.25rem; 
                        font-size: 0.82rem; 
                        background-color: #4a3b32; 
                        color: #fff; 
                        border: none; 
                        border-radius: 6px; 
                        opacity: 0.5; 
                        cursor: not-allowed;
                        font-weight: 600;
                    ">Kirim Berkas</button>
                </div>
            </form>
        </div>

        {{-- Pesan Terkunci jika Status Disetujui --}}
        <div id="modal-locked-notice" style="display: none; padding: 0.75rem; background-color: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 6px; text-align: center; color: #2e7d32; font-size: 0.82rem; font-weight: 600;">
            Item deliverable ini telah disetujui Dosen Pembimbing. Berkas telah terkunci.
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="sim-toast" class="sim-toast" role="alert" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
function toggleRumusInfo() {
    const box = document.getElementById('box-rumus-info');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

function openDeliverableModal(item, milestoneKode) {
    document.getElementById('modal-milestone-tag').textContent = 'MILESTONE ' + milestoneKode;
    document.getElementById('modal-item-title').textContent = item.nama;
    document.getElementById('modal-input-item-id').value = item.id || 'item_id';

    const badge = document.getElementById('modal-item-badge');
    badge.textContent = '● ' + item.status;
    if (item.status === 'Disetujui') {
        badge.className = 'sim-badge sim-badge--approved';
    } else if (item.status === 'Perlu Revisi') {
        badge.className = 'sim-badge sim-badge--pending';
    } else {
        badge.className = 'sim-badge sim-badge--draft';
    }

    // Catatan Dosen
    const catatanContainer = document.getElementById('modal-catatan-container');
    if (item.catatan) {
        document.getElementById('modal-item-catatan').textContent = '"' + item.catatan + '"';
        catatanContainer.style.display = 'block';
    } else {
        catatanContainer.style.display = 'none';
    }

    // Berkas Lama
    const fileContainer = document.getElementById('modal-file-lama-container');
    if (item.file_name) {
        document.getElementById('modal-item-file-name').textContent = item.file_name;
        document.getElementById('modal-item-file-link').href = item.file_url || '#';
        fileContainer.style.display = 'block';
        document.getElementById('modal-upload-label').textContent = 'Unggah Revisi / Berkas Baru:';
    } else {
        fileContainer.style.display = 'none';
        document.getElementById('modal-upload-label').textContent = 'Unggah Berkas (PDF / ZIP):';
    }

    // Form Lock Control
    const uploadForm = document.getElementById('modal-upload-form-container');
    const lockedNotice = document.getElementById('modal-locked-notice');

    if (item.status === 'Disetujui') {
        uploadForm.style.display = 'none';
        lockedNotice.style.display = 'block';
    } else {
        uploadForm.style.display = 'block';
        lockedNotice.style.display = 'none';
    }

    // Reset Input File
    resetModalDropzone();

    document.getElementById('modal-deliverable').style.display = 'flex';
}

function closeDeliverableModal() {
    document.getElementById('modal-deliverable').style.display = 'none';
}

function resetModalDropzone() {
    document.getElementById('modal-file-input').value = '';
    document.getElementById('dropzone-empty-state').style.display = 'block';
    document.getElementById('dropzone-selected-state').style.display = 'none';
    
    const dropArea = document.getElementById('drop-area-modal');
    dropArea.style.borderColor = '#d0c8b8';
    dropArea.style.backgroundColor = '#fdfbf7';

    const submitBtn = document.getElementById('modal-btn-submit');
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.5';
    submitBtn.style.cursor = 'not-allowed';
}

function handleModalFileSelect(input) {
    const file = input.files[0];
    const emptyState = document.getElementById('dropzone-empty-state');
    const selectedState = document.getElementById('dropzone-selected-state');
    const fileName = document.getElementById('selected-file-name');
    const fileSize = document.getElementById('selected-file-size');
    const dropArea = document.getElementById('drop-area-modal');
    const submitBtn = document.getElementById('modal-btn-submit');

    if (file) {
        if (file.size > 20 * 1024 * 1024) {
            alert('Ukuran berkas melebihi batas maksimal 20 MB!');
            resetModalDropzone();
            return;
        }

        emptyState.style.display = 'none';
        selectedState.style.display = 'block';
        fileName.textContent = file.name;
        fileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

        dropArea.style.borderColor = '#2e7d32';
        dropArea.style.backgroundColor = '#f4fbf7';

        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
    } else {
        resetModalDropzone();
    }
}
</script>
@endpush