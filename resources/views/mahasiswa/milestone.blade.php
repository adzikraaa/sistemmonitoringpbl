@extends('layouts.mahasiswa')

@section('title', 'Milestone')
@section('meta_description', 'Halaman Peta Perjalanan Milestone Mahasiswa SIM-PBL')

@section('content')
<div style="max-width: 920px; margin: 0 auto;">

    {{-- Header Halaman --}}
    <div class="sim-page-header" style="margin-bottom: 1.25rem;">
        <div class="sim-page-header__left">
            <p class="sim-page-header__eyebrow">PETA PERJALANAN PROYEK</p>
            <h1 class="sim-page-header__title">Milestone</h1>
        </div>
    </div>

    {{-- Context Banner Ringkas (Presisi Sejajar dengan Card Milestone) --}}
    <div class="sim-proposal-card" style="margin-bottom: 1rem; margin-left: 3.0rem; padding: 0.75rem 1rem;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between; font-size: 0.85rem; color: #555;">
            <div>
                <strong style="color: #222; font-weight: 600;">Kelompok 01 &bull; SIB 3B</strong>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="sim-badge sim-badge--approved" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">Status: On-Track</span>
                <button onclick="toggleRumusInfo()" class="sim-btn-secondary" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                    &#128712; Formula Progres
                </button>
            </div>
        </div>

        {{-- Collapsible Box Info Rumus --}}
        <div id="box-rumus-info" style="display: none; margin-top: 0.6rem; padding: 0.6rem; background-color: #f4f6f8; border-radius: 6px; border-left: 3px solid #4a3b32; font-size: 0.8rem; color: #444;">
            <p style="margin: 0; font-weight: 600; color: #222;">Transparansi Perhitungan Progres:</p>
            <p style="margin: 0.2rem 0 0 0;">
                Progres tiap milestone dihitung berdasarkan proporsi: 
                <strong>(Deliverables Terverifikasi / Total Deliverables) &times; 100%</strong>. Validasi dilakukan oleh Dosen Pembimbing.
            </p>
        </div>
    </div>

    {{-- List Timeline Milestone --}}
    <div style="display: flex; flex-direction: column; gap: 1rem; position: relative;">
        
        @foreach($milestones as $m)
        <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
            
            {{-- Indicator Circle --}}
            <div style="
                flex-shrink: 0;
                width: 34px;
                height: 34px;
                border-radius: 50%;
                background-color: {{ $m['status'] == 'Berjalan' ? '#d97706' : ($m['status'] == 'Selesai' ? '#2e7d32' : '#888888') }};
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 0.8rem;
                margin-top: 0.25rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.06);
                z-index: 2;
            ">
                {{ $m['kode'] }}
            </div>

            {{-- Card Item --}}
            <div class="sim-proposal-card" style="flex: 1; padding: 1rem 1.25rem; border-left: 4px solid {{ $m['status'] == 'Berjalan' ? '#d97706' : ($m['status'] == 'Selesai' ? '#2e7d32' : '#cccccc') }};">
                
                {{-- Header Card --}}
                <div class="sim-proposal-card__header" style="margin-bottom: 0.4rem;">
                    <div class="sim-proposal-card__meta" style="align-items: center; gap: 0.5rem;">
                        <h2 class="sim-proposal-card__title" style="margin-bottom: 0; font-size: 1.15rem;">{{ $m['judul'] }}</h2>
                        <span class="sim-badge {{ $m['badge'] }}" style="padding: 0.15rem 0.45rem; font-size: 0.7rem;">&#9679; {{ $m['status'] }}</span>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 0.65rem; color: #888; text-transform: uppercase; letter-spacing: 0.05em;">TENGGAT</span>
                        <span class="sim-proposal-card__date" style="font-weight: 600; color: #333; font-size: 0.85rem;">{{ $m['tenggat'] }}</span>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <p class="sim-proposal-card__desc" style="margin-bottom: 0.75rem; font-size: 0.85rem; color: #666;">{{ $m['deskripsi'] }}</p>

                {{-- Progress Bar --}}
                <div style="margin-bottom: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #555; margin-bottom: 0.2rem; font-weight: 600;">
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

                {{-- Catatan Dosen --}}
                @if(!empty($m['catatan']))
                <div class="sim-catatan" style="margin-bottom: 0.75rem; padding: 0.5rem 0.75rem; background-color: rgba(0,0,0,0.02); border-radius: 6px; border-left: 2px solid #888;">
                    <p class="sim-catatan__label" style="font-size: 0.65rem; margin-bottom: 0.15rem;">CATATAN PEMBIMBING</p>
                    <p class="sim-catatan__text" style="font-style: italic; font-size: 0.8rem; margin: 0; color: #444;">
                        "{{ $m['catatan'] }}"
                    </p>
                </div>
                @endif

                {{-- Action & Dropdown Rincian Deliverable --}}
                @if(!empty($m['deliverables']))
                <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f0f0f0; margin-top: 0.4rem; padding-top: 0.4rem;">
                    <button onclick="toggleDeliverables('{{ $m['id'] }}')" style="background: none; border: none; color: #4a3b32; font-size: 0.78rem; font-weight: 600; cursor: pointer; padding: 0; display: flex; align-items: center; gap: 0.3rem;">
                        <span id="icon-toggle-{{ $m['id'] }}">&#9656;</span> Lihat Rincian Deliverable ({{ count($m['deliverables']) }} Item)
                    </button>

                    @if($m['status'] == 'Berjalan')
                    <button onclick="showToast('Fitur ajukan pengujian siap digunakan saat terhubung DB.', 'info')" class="sim-btn-primary" style="padding: 0.25rem 0.6rem; font-size: 0.72rem;">
                        + Ajukan Verifikasi
                    </button>
                    @endif
                </div>

                {{-- Container Accordion Items --}}
                <div id="details-{{ $m['id'] }}" style="display: none; margin-top: 0.6rem; padding-top: 0.4rem; border-top: 1px dashed #e0e0e0;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.4rem;">
                        @foreach($m['deliverables'] as $item)
                        <div style="padding: 0.4rem 0.6rem; background-color: #fafafa; border: 1px solid #eee; border-radius: 6px; display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem;">
                            <span style="color: #333; font-weight: 500;">{{ $item['nama'] }}</span>
                            <span style="
                                font-size: 0.68rem; 
                                font-weight: 600; 
                                padding: 0.1rem 0.35rem; 
                                border-radius: 4px;
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

{{-- Toast Notification --}}
<div id="sim-toast" class="sim-toast" role="alert" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
function toggleRumusInfo() {
    const box = document.getElementById('box-rumus-info');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

function toggleDeliverables(id) {
    const details = document.getElementById('details-' + id);
    const icon = document.getElementById('icon-toggle-' + id);
    if (details.style.display === 'none') {
        details.style.display = 'block';
        icon.innerHTML = '&#9662;';
    } else {
        details.style.display = 'none';
        icon.innerHTML = '&#9656;';
    }
}

function showToast(message, type) {
    const toast = document.getElementById('sim-toast');
    toast.textContent = message;
    toast.className = 'sim-toast sim-toast--' + type + ' sim-toast--visible';
    setTimeout(function() {
        toast.classList.remove('sim-toast--visible');
    }, 3500);
}
</script>
@endpush