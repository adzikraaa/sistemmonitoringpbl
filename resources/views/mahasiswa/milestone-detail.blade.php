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
            {{ $judulProyek ?? ($kelompok?->judul_proyek ?? 'Belum ada proyek') }} - {{ $kelompok?->kelas?->nama_kelas ?? 'Kelas belum tersedia' }}
        </p>
        <p class="text-xs text-[#7D746D] mt-1">
            Status penyelesaian milestone:
            <strong class="text-[#1F1F1F]">{{ $statusMilestone }}</strong>
            @if($progresMatkul['punya_data'])
                ({{ $progresMatkul['selesai'] }}/{{ $progresMatkul['total'] }} selesai{{ ($jumlahTerkumpul ?? 0) > 0 && $progresMatkul['selesai'] < $progresMatkul['total'] ? ', '.$jumlahTerkumpul.' terkumpul' : '' }})
            @elseif(($jumlahTerkumpul ?? 0) > 0)
                ({{ $jumlahTerkumpul }} terkumpul)
            @else
                ({{ $progresMatkul['selesai'] }}/{{ $progresMatkul['total'] }} selesai)
            @endif
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

    {{-- Alert Sukses Upload --}}
    @if(session('success'))
        <div class="mb-5 bg-[#EAF3EC] border border-[#C6DFC8] text-[#2F5C4A] px-4 py-3 rounded-xl text-xs font-medium flex items-center gap-2.5">
            <span>✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Alert Peringatan Ukuran File (muncul via JS, disembunyikan awal) --}}
    <div id="alertFileTerlalubesar" class="hidden mb-5 bg-[#FFF7ED] border border-[#FED7AA] text-[#C2410C] px-4 py-3 rounded-xl text-xs font-medium flex items-start gap-2.5">
        <span class="text-base mt-0.5">⚠</span>
        <span>Ukuran file melebihi batas maksimal <strong>2 MB</strong>. Harap pilih file yang lebih kecil (PDF atau ZIP).</span>
        <button type="button" onclick="document.getElementById('alertFileTerlalubesar').classList.add('hidden')" class="ml-auto text-[#C2410C] hover:text-[#9A3412] font-bold text-sm leading-none flex-shrink-0">&times;</button>
    </div>

    {{-- Alert Error Validasi Input --}}
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
            @if(empty($milestones))
                <div class="bg-white border border-[#CFC7BC] rounded-2xl p-8 text-center shadow-sm">
                    <h2 class="text-xl font-serif font-bold text-[#1F1F1F] mb-1">Belum ada tahapan atau berkas milestone..</h2>
                    <p class="text-xs text-[#7D746D] mb-6">Belum ada rincian tahapan milestone yang terdaftar untuk mata kuliah ini pada periode aktif.</p>

                    <div class="bg-[#FFF7ED] border border-[#FED7AA] rounded-xl p-4 text-left flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#FFEDD5] text-[#C2410C] flex items-center justify-center text-xs font-bold flex-shrink-0">ⓘ</span>
                        <div class="text-xs">
                            <h4 class="font-bold text-[#1F1F1F]">Rincian Tahapan Belum Tersedia</h4>
                            <p class="text-[#62564C] mt-0.5 leading-relaxed">Pastikan mapping milestone untuk mata kuliah ini sudah diatur pada periode berjalan atau unggah dokumen laporan (.pdf atau .zip) bila tahapan sudah tersedia.</p>
                        </div>
                    </div>
                </div>
            @else
                @if(!$berkasTerkumpul)
                    <div class="bg-[#FFF7ED] border border-[#FED7AA] rounded-xl p-3.5 text-xs text-[#62564C] flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-[#FFEDD5] text-[#C2410C] flex items-center justify-center font-bold flex-shrink-0">ⓘ</span>
                        <span>Belum ada berkas laporan yang dikumpulkan pada mata kuliah ini. Berikut rincian tahapan milestone yang tersedia:</span>
                    </div>
                @endif

                @foreach($milestones as $ms)
                    @php
                        $statusBadgeClass = match ($ms['status'] ?? '') {
                            'Disetujui' => 'sim-badge--approved',
                            'Perlu Revisi' => 'sim-badge--danger',
                            'Menunggu Review' => 'sim-badge--pending',
                            default => 'sim-badge--warning',
                        };
                    @endphp
                    <div class="bg-white border border-[#CFC7BC] rounded-2xl p-6 shadow-sm space-y-4">
                        {{-- Card Header: Judul & Tenggat di Kiri, Badge Status di Kanan Atas --}}
                        <div class="pb-4 border-b border-[#E4DED5] flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="min-w-0 space-y-1.5">
                                <h3 class="font-serif font-normal text-[#1F1F1F] text-xl leading-snug m-0">
                                    {{ $ms['judul'] }}
                                </h3>

                                @if(!empty($ms['tanggal_mulai']) || !empty($ms['deadline']) || !empty($ms['status_verifikasi']))
                                    <div class="pt-0.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-[#7D746D]">
                                        @if(!empty($ms['tanggal_mulai']) || !empty($ms['deadline']))
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                @if(!empty($ms['tanggal_mulai']) && !empty($ms['deadline']))
                                                    <span>Tanggal mulai: <strong class="text-[#1F1F1F] font-semibold">{{ $ms['tanggal_mulai'] }}</strong> &bull; Tenggat waktu: <strong class="text-[#1F1F1F] font-semibold">{{ $ms['deadline'] }}</strong></span>
                                                @elseif(!empty($ms['deadline']))
                                                    <span>Tenggat waktu: <strong class="text-[#1F1F1F] font-semibold">{{ $ms['deadline'] }}</strong></span>
                                                @else
                                                    <span>Tanggal mulai: <strong class="text-[#1F1F1F] font-semibold">{{ $ms['tanggal_mulai'] }}</strong></span>
                                                @endif
                                            </span>
                                        @endif

                                        @if(!empty($ms['status_verifikasi']))
                                            <span>
                                                &bull; Verifikasi Dosen Pengampu: <strong class="text-[#1F1F1F] font-semibold">{{ $ms['status_verifikasi'] }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <span class="sim-badge {{ $statusBadgeClass }} shrink-0 self-start">
                                &#9679; {{ $ms['status'] }}
                            </span>
                        </div>

                        {{-- Isi Utama (Jika Sudah Dikumpulkan) --}}
                        @if(!empty($ms['sudah_dikumpulkan']))
                            <div>
                                <span class="text-[10px] font-bold text-[#7D746D] uppercase tracking-wider block mb-2">RINGKASAN CAPAIAN MILESTONE</span>
                                <div class="bg-[#FBF9F5] rounded-xl p-3.5 text-xs text-[#62564C] leading-relaxed border border-[#E4DED5]">
                                    {{ $ms['ringkasan'] }}
                                </div>
                            </div>

                            @if(!empty($ms['dokumen']))
                                <div>
                                    <span class="text-[10px] font-bold text-[#7D746D] uppercase tracking-wider block mb-2">DOKUMEN & TAUTAN PENDUKUNG</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($ms['dokumen'] as $doc)
                                            <div class="flex items-center justify-between p-3 bg-white border border-[#E4DED5] rounded-xl text-xs {{ isset($doc['full_width']) && $doc['full_width'] ? 'sm:col-span-2' : '' }}">
                                                <div class="flex items-center gap-3 min-w-0 pr-2">
                                                    @if(($doc['tipe'] ?? '') == 'pdf')
                                                        <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center font-bold shrink-0">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                        </div>
                                                    @elseif(($doc['tipe'] ?? '') == 'image')
                                                        <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                        </div>
                                                    @elseif(($doc['tipe'] ?? '') == 'github')
                                                        <div class="w-9 h-9 rounded-lg bg-neutral-900 text-white flex items-center justify-center font-bold shrink-0">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                                                        </div>
                                                    @else
                                                        <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                        </div>
                                                    @endif

                                                    <div class="min-w-0">
                                                        <span class="font-bold text-[#1F1F1F] block truncate">{{ $doc['nama'] }}</span>
                                                        <span class="text-[10px] text-[#7D746D] block truncate">{{ $doc['sub'] }}</span>
                                                    </div>
                                                </div>

                                                @if(($doc['action'] ?? '') == 'download')
                                                    <a href="{{ $doc['url'] ?? '#' }}" download class="text-[#7D746D] hover:text-[#1F1F1F] p-1 shrink-0" title="Unduh">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                                    </a>
                                                @else
                                                    <a href="{{ $doc['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1 bg-sky-50 text-sky-600 hover:bg-sky-100 font-semibold rounded-md text-[11px] shrink-0">Buka</a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Catatan Dosen (Jika Ada) --}}
                        @if(!empty($ms['catatan_dosen']))
                            <div class="rounded-xl p-3.5 text-xs leading-relaxed border {{ ($ms['status'] ?? '') === 'Perlu Revisi' ? 'bg-[#FDF2F2] border-[#F8B4B4] text-[#9B1C1C]' : 'bg-[#F6F4F1] border-[#E4DED5] text-[#62564C]' }}">
                                <span class="text-[10px] font-bold uppercase tracking-wider block mb-1 {{ ($ms['status'] ?? '') === 'Perlu Revisi' ? 'text-[#9B1C1C]' : 'text-[#7D746D]' }}">
                                    CATATAN DOSEN{{ !empty($ms['verifikator']) ? ' • '.$ms['verifikator'] : '' }}
                                </span>
                                <p class="m-0">{{ $ms['catatan_dosen'] }}</p>
                                @if(!empty($ms['waktu_verifikasi']))
                                    <span class="text-[10px] opacity-80 block mt-1">Diverifikasi pada {{ $ms['waktu_verifikasi'] }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Card Footer --}}
                        @if(!empty($ms['sudah_dikumpulkan']))
                            <div class="pt-3 border-t border-[#E4DED5] flex flex-wrap items-center justify-between gap-3 text-xs">
                                <span class="text-[#7D746D]">
                                    Diunggah <strong class="text-[#1F1F1F] font-semibold">{{ $ms['waktu'] }}</strong> oleh {{ $ms['pengunggah'] }}
                                </span>

                                @if($kelompok && !empty($ms['id']) && ($ms['status'] ?? '') !== 'Disetujui')
                                    <button type="button"
                                            onclick="openModalForm('{{ $ms['id'] }}')"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-[#CFC7BC] bg-white hover:bg-[#F5EFE6] hover:border-[#7A5C4A] text-[#2B221D] text-xs font-semibold transition-colors cursor-pointer shrink-0">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        Perbarui Berkas
                                    </button>
                                @endif
                            </div>
                        @else
                            <p class="{{ !empty($ms['catatan_dosen']) ? 'pt-3 border-t border-[#E4DED5]' : '' }} text-xs text-[#7D746D] m-0">
                                Belum ada berkas laporan yang dikumpulkan pada tahapan ini.
                            </p>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Sidebar: Anggota Kelompok -->
        <div>
            <div class="bg-white border border-[#CFC7BC] rounded-2xl p-5 shadow-sm">
                <p class="text-[10px] font-bold text-[#7D746D] uppercase tracking-wider mb-4">
                    ANGGOTA {{ $kelompok?->nama_kelompok ?? 'KELOMPOK' }}
                </p>
                <div class="space-y-3.5">
                    @forelse($anggotaKelompok as $member)
                        <div class="flex items-center justify-between text-xs pb-2.5 {{ !$loop->last ? 'border-b border-[#E4DED5]' : '' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#EBE1CC] text-[#6E5B3E] flex items-center justify-center font-bold text-xs">
                                    {{ $member['avatar'] }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-[#1F1F1F]">Anggota {{ $loop->iteration }} &mdash; {{ $member['nama'] }}</h4>
                                    <span class="text-[#7D746D] text-[11px]">{{ $member['role'] }}</span>
                                </div>
                            </div>
                            <span class="text-[#7D746D] font-mono text-[11px]">{{ $member['nim'] }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-[#7D746D]">Belum ada anggota kelompok.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL FORMULIR PENGUMPULAN -->
@php
    $bisaMengumpulkan = (bool) ($kelompok && $milestoneOptions->isNotEmpty());
@endphp
<div id="modalPengumpulan" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/45 backdrop-blur-xs p-4">
    <div id="modalPengumpulanBox" class="bg-white w-full max-w-xl rounded-2xl p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeModalForm()" class="absolute top-4 right-4 text-[#7D746D] hover:text-[#1F1F1F] text-xl font-bold p-1">&times;</button>

        <h2 class="text-xl font-serif font-bold text-[#1F1F1F] pr-8">Formulir Pengumpulan Milestone</h2>
        <p class="text-xs text-[#7D746D] mt-1 mb-4">Lengkapi ringkasan capaian dan unggah dokumen pendukung untuk ditinjau oleh dosen pembimbing.</p>

        {{-- Info Ketersediaan Kelompok / Tahapan Milestone --}}
        @if(! $kelompok)
            <div class="mb-4 bg-[#FFF7ED] border border-[#FED7AA] text-[#9A3412] px-3.5 py-3 rounded-xl text-xs leading-relaxed">
                <strong class="font-bold block text-[#7C2D12] mb-0.5">Belum Tergabung dalam Kelompok</strong>
                Akun Anda belum tergabung dalam kelompok pada periode aktif, sehingga pengumpulan laporan belum dapat dilakukan.
            </div>
        @elseif(!empty($semuaMilestoneDisetujui))
            <div class="mb-4 bg-[#EAF3EC] border border-[#C6DFC8] text-[#2F5C4A] px-3.5 py-3 rounded-xl text-xs leading-relaxed">
                <strong class="font-bold block mb-0.5">Seluruh Tahapan Selesai</strong>
                Seluruh tahapan milestone pada mata kuliah ini sudah disetujui, sehingga tidak ada lagi berkas yang perlu dikumpulkan.
            </div>
        @elseif($milestoneOptions->isEmpty())
            <div class="mb-4 bg-[#FFF7ED] border border-[#FED7AA] text-[#9A3412] px-3.5 py-3 rounded-xl text-xs leading-relaxed">
                <strong class="font-bold block text-[#7C2D12] mb-0.5">Tahapan Milestone Belum Tersedia</strong>
                Tahapan milestone untuk mata kuliah ini belum tersedia pada periode berjalan. Silakan hubungi koordinator atau dosen pengampu.
            </div>
        @endif

        {{-- Alert Error Validasi Server di Dalam Modal --}}
        @if($errors->any())
            <div id="modalServerErrors" class="mb-4 bg-[#FDF2F2] border border-[#F8B4B4] text-[#9B1C1C] px-3.5 py-3 rounded-xl text-xs space-y-1">
                <p class="font-bold m-0">Pengumpulan belum dapat disimpan, periksa isian berikut:</p>
                <ul class="list-disc list-inside m-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Alert Validasi Instan di Dalam Modal --}}
        <div id="modalValidationAlert" class="hidden mb-4 bg-[#FDF2F2] border border-[#F8B4B4] text-[#9B1C1C] px-3.5 py-3 rounded-xl text-xs space-y-1" role="alert">
            <div class="flex items-start justify-between gap-2">
                <p class="font-bold m-0">Mohon lengkapi data pengumpulan berikut:</p>
                <button type="button" onclick="hideModalValidationAlert()" class="text-[#9B1C1C] hover:text-[#7F1D1D] font-bold text-sm leading-none shrink-0">&times;</button>
            </div>
            <ul id="modalValidationList" class="list-disc list-inside m-0"></ul>
        </div>

        <form id="formPengumpulanMilestone" action="{{ route('mahasiswa.milestone.upload', $slug) }}" method="POST" enctype="multipart/form-data" novalidate onsubmit="return validateMilestoneForm(event)">
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
                    <select id="selectMilestoneId" name="milestone_id" required onchange="updateMilestoneInfoBox(); hideModalValidationAlert();" class="w-full bg-white border border-[#CFC7BC] rounded-xl p-2.5 focus:border-[#7A5C4A] outline-none">
                        <option value="">Pilih tahapan milestone...</option>
                        @forelse($milestoneOptions as $option)
                            <option value="{{ $option->id }}"
                                    data-deskripsi="{{ trim((string) ($option->deskripsi ?? '')) }}"
                                    data-deadline="{{ $option->deadline ? $option->deadline->locale('id')->translatedFormat('d F Y') : '' }}"
                                    @selected((string) old('milestone_id') === (string) $option->id)>{{ $option->kode }} - {{ $option->judul }}</option>
                        @empty
                            <option value="" disabled>
                                {{ !empty($semuaMilestoneDisetujui) ? 'Semua tahapan milestone sudah disetujui' : 'Belum ada milestone untuk mata kuliah ini' }}
                            </option>
                        @endforelse
                    </select>
                </div>
            </div>

            {{-- Kotak Informasi Deskripsi & Tenggat Tahapan yang Dipilih --}}
            <div id="modalMilestoneInfo" class="hidden mb-4 rounded-xl bg-[#FBF9F5] border border-[#E4DED5] px-3.5 py-3 text-xs">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                    <span class="text-[10px] font-bold text-[#7A5C4A] uppercase tracking-wider">Keterangan Tahapan</span>
                    <span id="modalMilestoneDeadline" class="hidden text-[11px] text-[#7D746D]"></span>
                </div>
                <p id="modalMilestoneDeskripsi" class="text-[#62564C] leading-relaxed m-0"></p>
            </div>

            <div class="mb-4 text-xs">
                <label class="block font-bold text-[#1F1F1F] mb-1.5">Ringkasan Capaian Milestone <span class="text-rose-500">*</span></label>
                <textarea id="inputCatatanMilestone" name="catatan" rows="3" required oninput="hideModalValidationAlert()" placeholder="Jelaskan ringkasan pekerjaan, hasil progres, dan kendala yang telah diselesaikan..." class="w-full bg-white border border-[#CFC7BC] rounded-xl p-2.5 focus:border-[#7A5C4A] outline-none">{{ old('catatan') }}</textarea>
            </div>

            <!-- Dropzone Upload -->
            <div class="mb-4 text-xs">
                <label class="block font-bold text-[#1F1F1F] mb-1.5">Pengumpulan Berkas atau Laporan <span class="text-rose-500">*</span></label>

                {{-- Alert Peringatan Ukuran File di dalam Modal --}}
                <div id="modalAlertFileSize" class="hidden mb-2.5 bg-[#FFF7ED] border border-[#FED7AA] text-[#C2410C] px-3.5 py-2.5 rounded-xl text-xs font-medium flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-base font-bold leading-none">⚠</span>
                        <span>Ukuran file melebihi kapasitas yang ditentukan (maksimal <strong>2 MB</strong>). Silakan pilih file yang lebih kecil.</span>
                    </div>
                    <button type="button" onclick="document.getElementById('modalAlertFileSize').classList.add('hidden')" class="text-[#C2410C] hover:text-[#9A3412] font-bold text-sm leading-none ml-2">&times;</button>
                </div>

                <div onclick="document.getElementById('fileInputReal').click()" class="border-2 border-dashed border-[#CFC7BC] rounded-xl p-6 text-center bg-[#F7F6F0]/60 hover:bg-[#F7F6F0] transition-colors cursor-pointer">
                    <input type="file" name="berkas" id="fileInputReal" class="hidden" accept=".pdf,.zip" required onchange="previewFileSelected(this)">
                    
                    <div id="dropzoneEmpty">
                        <svg class="w-8 h-8 text-[#7D746D] mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span class="font-bold text-[#1F1F1F] block">Tarik berkas atau <span class="text-[#7A5C4A] underline">klik untuk memilih</span></span>
                        <span class="text-[#7D746D] text-[11px]">Format file didukung: PDF, ZIP (Maks. 2 MB)</span>
                    </div>

                    <div id="dropzoneFilled" class="hidden text-[#2F5C4A]">
                        <span class="font-bold text-sm block" id="fileNameText"></span>
                        <span class="text-[11px] text-[#7D746D]" id="fileSizeText"></span>
                    </div>
                </div>
            </div>

            <div class="mb-6 text-xs">
                <label class="block font-bold text-[#1F1F1F] mb-1.5">Tautan Repositori / Demo <span class="text-[#7D746D] font-normal">(Opsional)</span></label>
                <input id="inputLinkBuktiMilestone" type="url" name="link_bukti" value="{{ old('link_bukti') }}" oninput="hideModalValidationAlert()" placeholder="https://github.com/organisasi/proyek-pbl" class="w-full bg-white border border-[#CFC7BC] rounded-xl p-2.5 focus:border-[#7A5C4A] outline-none">
            </div>

            <div class="flex justify-end gap-3 text-xs">
                <button type="button" onclick="closeModalForm()" class="sim-btn-secondary">Batal</button>
                <button id="btnSubmitMilestone" type="submit" @disabled(! $bisaMengumpulkan) class="sim-btn-primary {{ ! $bisaMengumpulkan ? 'opacity-50 cursor-not-allowed' : '' }}">Kumpulkan</button>
            </div>
        </form>
    </div>
</div>

<script>
function showModalValidationAlert(messages) {
    const alertBox = document.getElementById('modalValidationAlert');
    const listEl = document.getElementById('modalValidationList');
    const modalBox = document.getElementById('modalPengumpulanBox');

    if (!alertBox || !listEl) {
        return;
    }

    listEl.innerHTML = '';
    messages.forEach(function (msg) {
        const li = document.createElement('li');
        li.textContent = msg;
        listEl.appendChild(li);
    });

    alertBox.classList.remove('hidden');

    if (modalBox) {
        modalBox.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function hideModalValidationAlert() {
    const alertBox = document.getElementById('modalValidationAlert');
    if (alertBox) {
        alertBox.classList.add('hidden');
    }
}

function updateMilestoneInfoBox() {
    const selectMilestone = document.getElementById('selectMilestoneId');
    const infoBox = document.getElementById('modalMilestoneInfo');
    const descEl = document.getElementById('modalMilestoneDeskripsi');
    const deadlineEl = document.getElementById('modalMilestoneDeadline');

    if (!selectMilestone || !infoBox || !descEl || !deadlineEl) {
        return;
    }

    const selectedOption = selectMilestone.options[selectMilestone.selectedIndex];
    const deskripsi = selectedOption ? (selectedOption.getAttribute('data-deskripsi') || '').trim() : '';
    const deadline = selectedOption ? (selectedOption.getAttribute('data-deadline') || '').trim() : '';

    if (!selectMilestone.value || (!deskripsi && !deadline)) {
        infoBox.classList.add('hidden');
        descEl.textContent = '';
        deadlineEl.textContent = '';
        deadlineEl.classList.add('hidden');
        return;
    }

    descEl.textContent = deskripsi || 'Tidak ada keterangan tambahan untuk tahapan ini.';

    if (deadline) {
        deadlineEl.innerHTML = 'Tenggat waktu: <strong class="text-[#1F1F1F] font-semibold">' + deadline + '</strong>';
        deadlineEl.classList.remove('hidden');
    } else {
        deadlineEl.textContent = '';
        deadlineEl.classList.add('hidden');
    }

    infoBox.classList.remove('hidden');
}

function openModalForm(milestoneId = null) {
    const modal = document.getElementById('modalPengumpulan');
    const selectMilestone = document.getElementById('selectMilestoneId');

    hideModalValidationAlert();

    if (selectMilestone && milestoneId) {
        selectMilestone.value = String(milestoneId);
    }

    updateMilestoneInfoBox();

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModalForm() {
    const modal = document.getElementById('modalPengumpulan');
    const form = document.getElementById('formPengumpulanMilestone');
    const modalAlert = document.getElementById('modalAlertFileSize');
    const serverErrors = document.getElementById('modalServerErrors');

    if (form) {
        form.reset();
    }
    if (modalAlert) {
        modalAlert.classList.add('hidden');
    }
    if (serverErrors) {
        serverErrors.classList.add('hidden');
    }
    hideModalValidationAlert();
    updateMilestoneInfoBox();
    document.getElementById('dropzoneEmpty').classList.remove('hidden');
    document.getElementById('dropzoneFilled').classList.add('hidden');

    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.addEventListener('DOMContentLoaded', function () {
    updateMilestoneInfoBox();

    @if($errors->any())
        openModalForm();
    @endif
});

function previewFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const modalAlert = document.getElementById('modalAlertFileSize');
        const alertEl = document.getElementById('alertFileTerlalubesar');

        hideModalValidationAlert();

        if (file.size > 2 * 1024 * 1024) {
            // Tampilkan notifikasi peringatan file melebihi batas 2MB
            if (modalAlert) {
                modalAlert.classList.remove('hidden');
            }
            if (alertEl) {
                alertEl.classList.remove('hidden');
            }
            input.value = '';
            // Reset dropzone ke kondisi kosong
            document.getElementById('dropzoneEmpty').classList.remove('hidden');
            document.getElementById('dropzoneFilled').classList.add('hidden');
            return;
        }

        // Sembunyikan alert jika sebelumnya tampil
        if (modalAlert) {
            modalAlert.classList.add('hidden');
        }
        if (alertEl) {
            alertEl.classList.add('hidden');
        }

        document.getElementById('dropzoneEmpty').classList.add('hidden');
        document.getElementById('dropzoneFilled').classList.remove('hidden');
        document.getElementById('fileNameText').textContent = '📄 ' + file.name;
        document.getElementById('fileSizeText').textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
    }
}

function validateMilestoneForm(event) {
    const selectMilestone = document.getElementById('selectMilestoneId');
    const catatanInput = document.getElementById('inputCatatanMilestone');
    const fileInput = document.getElementById('fileInputReal');
    const linkInput = document.getElementById('inputLinkBuktiMilestone');
    const modalAlert = document.getElementById('modalAlertFileSize');
    const messages = [];

    if (!selectMilestone || !selectMilestone.value) {
        const hasSelectableOption = selectMilestone
            && Array.from(selectMilestone.options).some(function (opt) { return opt.value !== '' && !opt.disabled; });
        messages.push(
            hasSelectableOption
                ? 'Tahapan milestone wajib dipilih.'
                : 'Tahapan milestone belum tersedia untuk dikumpulkan.'
        );
    }

    if (!catatanInput || !catatanInput.value.trim()) {
        messages.push('Ringkasan capaian milestone wajib diisi.');
    }

    if (!fileInput || !fileInput.files || !fileInput.files[0]) {
        messages.push('Berkas laporan (PDF atau ZIP) wajib dipilih.');
    } else if (fileInput.files[0].size > 2 * 1024 * 1024) {
        if (modalAlert) {
            modalAlert.classList.remove('hidden');
        }
        messages.push('Ukuran file melebihi batas maksimal 2 MB.');
    }

    if (linkInput && linkInput.value.trim() !== '' && !/^https?:\/\/.+/i.test(linkInput.value.trim())) {
        messages.push('Format tautan repositori/demo tidak valid (harus diawali http:// atau https://).');
    }

    if (messages.length > 0) {
        event.preventDefault();
        showModalValidationAlert(messages);
        return false;
    }

    return true;
}
</script>
@endsection