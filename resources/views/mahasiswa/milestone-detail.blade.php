@extends('layouts.mahasiswa')

@section('title', 'Detail Milestone - ' . $namaMatkul)
@section('meta_description', 'Detail capaian dan pengumpulan milestone mata kuliah ' . $namaMatkul)

@section('content')

{{-- ─── Page Header (Standar SIM-PBL) ────────────────────────────────────── --}}
<div class="sim-page-header">
    <div class="sim-page-header__left">
        <!-- Back Link -->
        <a href="{{ route('mahasiswa.milestone') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7A5C4A] hover:text-[#2B221D] mb-2 transition-colors">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali ke laporan milestone
        </a>
        <h1 class="sim-page-header__title">Milestone : {{ $namaMatkul }}</h1>
        <p class="sim-page-header__desc">
            Pengembangan Sistem Monitoring PBL &bull; SIB 3B
        </p>
    </div>
    
    <div class="sim-page-header__right">
        <button type="button" onclick="openModalForm()" class="sim-btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload Laporan
        </button>
    </div>
</div>

{{-- ─── Main Content Wrap ────────────────────────────────────────────────── --}}
<div style="padding: 0 32px 40px;">

    {{-- Alert Sukses Upload (Pertemuan 6) --}}
    @if(session('success'))
        <div class="mb-5 bg-[#EAF3EC] border border-[#C6DFC8] text-[#2F5C4A] px-4 py-3 rounded-xl text-xs font-medium flex items-center gap-2.5">
            <span>✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Alert Error Validasi Input (Pertemuan 6) --}}
    @if($errors->any())
        <div class="mb-5 bg-[#FDF2F2] border border-[#F8B4B4] text-[#9B1C1C] px-4 py-3 rounded-xl text-xs space-y-1">
            <p class="font-bold">Gagal mengumpulkan milestone, periksa kembali isian berikut:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Main Section: Status Berkas -->
        <div class="lg:col-span-2 space-y-6">
            @if(!$berkasTerkumpul || empty($milestones))
                <!-- Empty State (Saat Belum Ada Berkas) -->
                <div class="bg-white border border-[#CFC7BC] rounded-2xl p-8 text-center shadow-sm">
                    <h2 class="text-xl font-serif font-bold text-[#1F1F1F] mb-1">Belum ada berkas yang dikumpulkan..</h2>
                    <p class="text-xs text-[#7D746D] mb-6">Unggah hasil implementasi ide project untuk ditinjau dosen pembimbing.</p>

                    <div class="bg-[#FFF7ED] border border-[#FED7AA] rounded-xl p-4 text-left flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#FFEDD5] text-[#C2410C] flex items-center justify-center text-xs font-bold flex-shrink-0">ⓘ</span>
                        <div class="text-xs">
                            <h4 class="font-bold text-[#1F1F1F]">Belum Ada Berkas Laporan yang Dikumpulkan</h4>
                            <p class="text-[#62564C] mt-0.5 leading-relaxed">Kelompok kamu belum mengunggah laporan capaian untuk mata kuliah ini. Silakan unggah dokumen laporan (.pdf atau .zip) agar dapat ditinjau oleh dosen pembimbing.</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Filled State -->
                @foreach($milestones as $ms)
                    <div class="bg-white border border-[#CFC7BC] rounded-2xl p-6 shadow-sm space-y-5">
                        <!-- Card Header & Badge Status -->
                        <div class="pb-3 border-b border-[#E4DED5]">
                            <h3 class="font-serif font-bold text-[#1F1F1F] text-xl mb-2">{{ $ms['judul'] }}</h3>
                            
                            @if(($ms['status'] ?? '') == 'Disetujui')
                                <span class="bg-[#EAF3EC] text-[#2F5C4A] border border-[#D8E6D8] text-[11px] font-medium px-3 py-1 rounded-md inline-block">
                                    Disetujui
                                </span>
                            @elseif(($ms['status'] ?? '') == 'Menunggu Review')
                                <span class="bg-[#FFF7ED] text-[#C2410C] border border-[#FED7AA] text-[11px] font-medium px-3 py-1 rounded-md inline-block">
                                    Menunggu Review
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-600 text-[11px] font-medium px-3 py-1 rounded-md inline-block">
                                    {{ $ms['status'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Ringkasan Capaian -->
                        <div>
                            <span class="text-[10px] font-bold text-[#7D746D] uppercase tracking-wider block mb-2">RINGKASAN CAPAIAN MILESTONE</span>
                            <div class="bg-[#FBF9F5] rounded-xl p-4 text-xs text-[#62564C] leading-relaxed border border-[#E4DED5]">
                                {{ $ms['ringkasan'] }}
                            </div>
                        </div>

                        <!-- Waktu & Pengunggah -->
                        <div class="text-xs text-[#7D746D]">
                            Waktu pengunggahan: <strong class="text-[#1F1F1F] font-semibold">{{ $ms['waktu'] }}</strong> oleh {{ $ms['pengunggah'] }}
                        </div>

                        <!-- Dokumen & Tautan Pendukung (Grid 2 Kolom) -->
                        @if(!empty($ms['dokumen']))
                            <div>
                                <span class="text-[10px] font-bold text-[#7D746D] uppercase tracking-wider block mb-2.5">DOKUMEN & TAUTAN PENDUKUNG</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($ms['dokumen'] as $doc)
                                        <div class="flex items-center justify-between p-3 bg-white border border-[#E4DED5] rounded-xl text-xs {{ isset($doc['full_width']) && $doc['full_width'] ? 'sm:col-span-2' : '' }}">
                                            <div class="flex items-center gap-3 min-w-0 pr-2">
                                                @if(($doc['tipe'] ?? '') == 'pdf')
                                                    <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center font-bold flex-shrink-0">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                    </div>
                                                @elseif(($doc['tipe'] ?? '') == 'image')
                                                    <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold flex-shrink-0">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                    </div>
                                                @elseif(($doc['tipe'] ?? '') == 'github')
                                                    <div class="w-9 h-9 rounded-lg bg-neutral-900 text-white flex items-center justify-center font-bold flex-shrink-0">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                                                    </div>
                                                @else
                                                    <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold flex-shrink-0">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                    </div>
                                                @endif

                                                <div class="min-w-0">
                                                    <span class="font-bold text-[#1F1F1F] block truncate">{{ $doc['nama'] }}</span>
                                                    <span class="text-[10px] text-[#7D746D] block truncate">{{ $doc['sub'] }}</span>
                                                </div>
                                            </div>

                                            @if(($doc['action'] ?? '') == 'download')
                                                <a href="{{ $doc['url'] ?? '#' }}" download class="text-[#7D746D] hover:text-[#1F1F1F] p-1 flex-shrink-0" title="Unduh">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                                </a>
                                            @else
                                                <a href="{{ $doc['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1 bg-sky-50 text-sky-600 hover:bg-sky-100 font-semibold rounded-md text-[11px] flex-shrink-0">Buka</a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            @endif
        </div>

        <!-- Sidebar: Anggota Kelompok 02 -->
        <div>
            <div class="bg-white border border-[#CFC7BC] rounded-2xl p-5 shadow-sm">
                <p class="text-[10px] font-bold text-[#7D746D] uppercase tracking-wider mb-4">ANGGOTA KELOMPOK 02</p>
                <div class="space-y-3.5">
                    @foreach($anggotaKelompok as $member)
                        <div class="flex items-center justify-between text-xs pb-2.5 {{ !$loop->last ? 'border-b border-[#E4DED5]' : '' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#EBE1CC] text-[#6E5B3E] flex items-center justify-center font-bold text-xs">
                                    {{ $member['avatar'] }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#1F1F1F]">{{ $member['nama'] }}</h4>
                                    <span class="text-[#7D746D] text-[11px]">{{ $member['role'] }}</span>
                                </div>
                            </div>
                            <span class="text-[#7D746D] font-mono text-[11px]">{{ $member['nim'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL FORMULIR PENGUMPULAN -->
<div id="modalPengumpulan" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/45 backdrop-blur-xs p-4">
    <div class="bg-white w-full max-w-xl rounded-2xl p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeModalForm()" class="absolute top-4 right-4 text-[#7D746D] hover:text-[#1F1F1F] text-xl font-bold p-1">&times;</button>

        <span class="text-[10px] font-bold text-[#7D746D] uppercase tracking-wider block">RUANG KERJA • MAHASISWA</span>
        <h2 class="text-xl font-serif font-bold text-[#1F1F1F] mt-0.5">Formulir Pengumpulan Milestone</h2>
        <p class="text-xs text-[#7D746D] mb-5">Lengkapi ringkasan capaian dan unggah dokumen pendukung untuk ditinjau oleh dosen pembimbing.</p>

        <form id="formPengumpulanMilestone" action="{{ route('mahasiswa.milestone.upload', $slug) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 text-xs">
                <div>
                    <label class="block font-bold text-[#1F1F1F] mb-1.5">Mata Kuliah <span class="text-rose-500">*</span></label>
                    <select name="mata_kuliah" class="w-full bg-white border border-[#CFC7BC] rounded-xl p-2.5 focus:border-[#7A5C4A] outline-none">
                        <option value="{{ $slug }}">{{ $namaMatkul }}</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-[#1F1F1F] mb-1.5">Tahapan Milestone <span class="text-rose-500">*</span></label>
                    <select name="milestone_id" required class="w-full bg-white border border-[#CFC7BC] rounded-xl p-2.5 focus:border-[#7A5C4A] outline-none">
                        <option value="">Pilih tahapan milestone...</option>
                        <option value="1">Milestone 1 - Laporan Awalan</option>
                        <option value="2">Milestone 2 - Implementasi Utama</option>
                        <option value="3">Milestone 3 - Finalisasi & Pengujian</option>
                    </select>
                </div>
            </div>

            <div class="mb-4 text-xs">
                <label class="block font-bold text-[#1F1F1F] mb-1.5">Ringkasan Capaian Milestone <span class="text-rose-500">*</span></label>
                <textarea name="catatan" rows="3" required placeholder="Jelaskan ringkasan pekerjaan, hasil progres, dan kendala yang telah diselesaikan..." class="w-full bg-white border border-[#CFC7BC] rounded-xl p-2.5 focus:border-[#7A5C4A] outline-none">{{ old('catatan') }}</textarea>
            </div>

            <!-- Dropzone Upload -->
            <div class="mb-4 text-xs">
                <label class="block font-bold text-[#1F1F1F] mb-1.5">Pengumpulan Berkas atau Laporan <span class="text-rose-500">*</span> <span class="text-[#7D746D] font-normal">(PDF/ZIP maks 20 MB)</span></label>
                <div onclick="document.getElementById('fileInputReal').click()" class="border-2 border-dashed border-[#CFC7BC] rounded-xl p-6 text-center bg-[#F7F6F0]/60 hover:bg-[#F7F6F0] transition-colors cursor-pointer">
                    <input type="file" name="berkas" id="fileInputReal" class="hidden" accept=".pdf,.zip" required onchange="previewFileSelected(this)">
                    
                    <div id="dropzoneEmpty">
                        <svg class="w-8 h-8 text-[#7D746D] mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span class="font-bold text-[#1F1F1F] block">Tarik berkas atau <span class="text-[#7A5C4A] underline">klik untuk memilih</span></span>
                        <span class="text-[#7D746D] text-[11px]">Format file didukung: PDF, ZIP (Maks. 20 MB)</span>
                    </div>

                    <div id="dropzoneFilled" class="hidden text-[#2F5C4A]">
                        <span class="font-bold text-sm block" id="fileNameText"></span>
                        <span class="text-[11px] text-[#7D746D]" id="fileSizeText"></span>
                    </div>
                </div>
            </div>

            <div class="mb-6 text-xs">
                <label class="block font-bold text-[#1F1F1F] mb-1.5">Tautan Repositori / Demo <span class="text-[#7D746D] font-normal">(Opsional)</span></label>
                <input type="url" name="link_bukti" value="{{ old('link_bukti') }}" placeholder="https://github.com/organisasi/proyek-pbl" class="w-full bg-white border border-[#CFC7BC] rounded-xl p-2.5 focus:border-[#7A5C4A] outline-none">
            </div>

            <div class="flex justify-end gap-3 text-xs">
                <button type="button" onclick="closeModalForm()" class="sim-btn-secondary">Batal</button>
                <button type="submit" class="sim-btn-primary">Kumpulkan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalForm() {
    const modal = document.getElementById('modalPengumpulan');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModalForm() {
    const modal = document.getElementById('modalPengumpulan');
    const form = document.getElementById('formPengumpulanMilestone');

    if (form) {
        form.reset();
    }
    document.getElementById('dropzoneEmpty').classList.remove('hidden');
    document.getElementById('dropzoneFilled').classList.add('hidden');

    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function previewFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (file.size > 20 * 1024 * 1024) {
            alert('Ukuran berkas melebihi batas maksimal 20 MB!');
            input.value = '';
            return;
        }

        document.getElementById('dropzoneEmpty').classList.add('hidden');
        document.getElementById('dropzoneFilled').classList.remove('hidden');
        document.getElementById('fileNameText').textContent = '📄 ' + file.name;
        document.getElementById('fileSizeText').textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
    }
}
</script>
@endsection