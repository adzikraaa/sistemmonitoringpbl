@extends('layouts.dosen')

@section('title', 'Review Milestone - ' . ($milestone->judul ?: 'Laporan Capaian'))
@section('breadcrumb', 'Laporan aktivitas')
@section('meta_description', 'Evaluasi laporan capaian milestone terpadu tiga mata kuliah kelompok PBL.')

@push('styles')
<style>
.ms-show-shell {
    padding: 0 32px 40px;
    box-sizing: border-box;
}

.ms-show-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 28px;
    align-items: start;
}

.ms-card {
    background: #FFFFFF;
    border: 1px solid var(--sim-border);
    border-radius: 16px;
    padding: 24px;
    box-sizing: border-box;
}

.ms-card-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: var(--sim-text-muted);
    text-transform: uppercase;
    margin: 0 0 14px;
}

.ms-badge-disetujui {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
    color: #065F46;
}

.ms-badge-revisi {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: #FEF2F2;
    border: 1px solid #FECACA;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
    color: #991B1B;
}

.ms-badge-menunggu {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: #FEF3C7;
    border: 1px solid #FDE68A;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
    color: #92400E;
}

.ms-badge-belum {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: #F3F4F6;
    border: 1px solid #E5E7EB;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 500;
    color: #4B5563;
}

@media (max-width: 1024px) {
    .ms-show-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .ms-show-shell {
        padding: 0 16px 24px;
    }
    .ms-card {
        padding: 18px;
    }
}
</style>
@endpush

@section('content')
<div class="ms-show-shell">

    {{-- Back Link --}}
    <div style="margin-bottom: 20px; padding-top: 4px;">
        <a href="{{ route('dosen.laporan-aktivitas') }}" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #2E6B42; text-decoration: none; transition: opacity 0.15s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali ke laporan aktivitas
        </a>
    </div>

    {{-- Flash Alert --}}
    @if(session('success'))
        <div style="margin-bottom: 22px; padding: 14px 18px; background: #EAF4EE; border: 1px solid #B8DCBF; border-radius: 12px; color: #2E6B42; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="margin-bottom: 22px; padding: 14px 18px; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 12px; color: #991B1B; font-size: 13.5px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Page Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 280px;">
            <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin: 0 0 6px;">
                MILESTONE {{ $milestone->urutan ?? 1 }} &bull; {{ strtoupper($kelompok?->nama_kelompok ?? 'KELOMPOK') }}
            </p>
            <h1 style="font-family: 'DM Serif Display', serif; font-size: 28px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 8px; line-height: 1.25;">
                Milestone {{ $milestone->urutan ?? 1 }} : {{ $milestone->judul ?: 'Laporan Capaian Milestone' }}
            </h1>
            <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0;">
                {{ $kelompok?->judul_proyek ?: 'Pengembangan Sistem PBL' }} &bull; {{ $kelompok?->kelas?->nama_kelas ?? 'Kelas' }}
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            @if($overallStatus === 'disetujui')
                <span class="ms-badge-disetujui">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                    Disetujui
                </span>
            @elseif($overallStatus === 'perlu-revisi')
                <span class="ms-badge-revisi">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #DC2626;"></span>
                    Perlu revisi
                </span>
            @elseif($overallStatus === 'belum-dikumpulkan')
                <span class="ms-badge-belum">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #9CA3AF;"></span>
                    Belum dikumpulkan
                </span>
            @else
                <span class="ms-badge-menunggu">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                    Menunggu review
                </span>
            @endif

            <span style="font-size: 12px; color: var(--sim-text-muted);">
                Diajukan {{ $latestSubmissionDate ? $latestSubmissionDate->translatedFormat('d M Y') : ($milestone->deadline ? 'Deadline ' . $milestone->deadline->translatedFormat('d M Y') : 'Semester ini') }}
            </span>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="ms-show-grid">

        {{-- LEFT COLUMN: 3 Cards for 3 Mata Kuliah --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">

            @foreach($laporanMatkul as $item)
                @php
                    $mk = $item['mata_kuliah'];
                    $sub = $item['pengumpulan'];
                    $peny = $item['penyelesaian'];
                    $status = $item['status_raw'];
                @endphp

                <div class="ms-card">
                    <p class="ms-card-label" style="margin-bottom: 6px;">MATA KULIAH</p>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; gap: 12px; flex-wrap: wrap;">
                        <h3 style="font-family: 'DM Serif Display', serif; font-size: 21px; font-weight: 400; color: var(--sim-text-dark); margin: 0; line-height: 1.3;">
                            Laporan Milestone {{ $milestone->urutan ?? 1 }} &mdash; {{ $mk->nama_matkul }}
                        </h3>
                        <div>
                            @if(in_array($status, ['disetujui', 'selesai'], true))
                                <span class="ms-badge-disetujui">{{ $item['status_label'] }}</span>
                            @elseif($status === 'revisi')
                                <span class="ms-badge-revisi">{{ $item['status_label'] }}</span>
                            @elseif(in_array($status, ['diajukan', 'menunggu review', 'menunggu'], true))
                                <span class="ms-badge-menunggu">{{ $item['status_label'] }}</span>
                            @else
                                <span class="ms-badge-belum">{{ $item['status_label'] }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Ringkasan Capaian Milestone --}}
                    <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: var(--sim-text-muted); text-transform: uppercase; margin: 0 0 8px;">
                        RINGKASAN CAPAIAN MILESTONE
                    </p>
                    <div style="background: #FAF7F3; border: 1px solid #EFEAE4; border-radius: 12px; padding: 16px 18px; margin-bottom: 12px;">
                        <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0; line-height: 1.6;">
                            {{ $sub?->catatan ?: 'Kelompok belum mengunggah ringkasan capaian untuk mata kuliah ini.' }}
                        </p>
                    </div>

                    {{-- Waktu pengunggahan & Pengunggah --}}
                    <p style="font-size: 12px; color: var(--sim-text-muted); margin: 0 0 18px;">
                        @if($sub)
                            Waktu pengunggahan: <strong style="color: var(--sim-text-dark);">{{ $sub->created_at ? $sub->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</strong>
                            @if($sub->pengunggah)
                                oleh <strong style="color: var(--sim-text-dark);">{{ $sub->pengunggah->nama }}</strong>
                            @endif
                        @else
                            Status pengumpulan: <em style="color: #92400E;">Belum ada berkas yang dikumpulkan oleh kelompok</em>
                        @endif
                    </p>

                    {{-- Dokumen & Tautan Pendukung --}}
                    <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: var(--sim-text-muted); text-transform: uppercase; margin: 0 0 10px;">
                        DOKUMEN &amp; TAUTAN PENDUKUNG
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 12px;">
                        @if(!empty($sub?->file_path))
                            {{-- Dokumen Card --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 12px; gap: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #FEF2F2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 15px; font-weight: 700;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <div style="min-width: 0;">
                                        <p style="font-size: 13px; font-weight: 600; color: var(--sim-text-dark); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $sub->file_name ?: 'Laporan-Milestone.pdf' }}
                                        </p>
                                        <span style="font-size: 11px; color: var(--sim-text-muted);">
                                            Dokumen {{ $item['file_ext'] }} &bull; {{ $item['file_size'] }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('dosen.penyelesaian-milestone.download', [$kelompok->id, $milestone->id, $mk->id]) }}" download style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: #FFFFFF; border: 1px solid var(--sim-border); color: var(--sim-text-dark); text-decoration: none;" title="Unduh berkas laporan">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                </a>
                            </div>
                        @endif

                        @if(!empty($sub?->link_bukti))
                            {{-- Tautan Card --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #FFFFFF; border: 1px solid var(--sim-border); border-radius: 12px; gap: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                    <div style="width: 38px; height: 38px; border-radius: 10px; background: #F3F4F6; color: #1F2937; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                                    </div>
                                    <div style="min-width: 0;">
                                        <p style="font-size: 13px; font-weight: 600; color: var(--sim-text-dark); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ str_contains(strtolower($sub->link_bukti), 'github') ? 'GitHub Repository' : 'Tautan Demo / Bukti' }}
                                        </p>
                                        <span style="font-size: 11px; color: var(--sim-text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;">
                                            {{ $sub->link_bukti }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ $sub->link_bukti }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; justify-content: center; padding: 5px 14px; border-radius: 6px; background: #FFFFFF; border: 1px solid var(--sim-border); color: #2563EB; font-size: 12px; font-weight: 600; text-decoration: none;">
                                    Buka
                                </a>
                            </div>
                        @endif

                        @if(empty($sub?->file_path) && empty($sub?->link_bukti))
                            <p style="font-size: 12.5px; color: var(--sim-text-muted); font-style: italic; margin: 0;">
                                Tidak ada dokumen atau tautan pendukung yang dilampirkan.
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>

        {{-- RIGHT COLUMN: Catatan Pembimbing & Anggota Kelompok --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">

            {{-- 1. Catatan Pembimbing Form --}}
            <div class="ms-card">
                <h3 style="font-family: 'DM Serif Display', serif; font-size: 20px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 16px;">
                    Catatan Pembimbing
                </h3>

                <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.08em; color: var(--sim-text-muted); text-transform: uppercase; margin: 0 0 6px;">
                    SARAN &amp; MASUKAN MILESTONE {{ $milestone->urutan ?? 1 }}
                </p>
                <p style="font-size: 12.5px; color: var(--sim-text-muted); margin: 0 0 14px; line-height: 1.5;">
                    Catatan evaluasi ini mencakup penilaian terpadu terhadap ketiga mata kuliah yang dikumpulkan kelompok untuk Milestone {{ $milestone->urutan ?? 1 }}.
                </p>

                <form action="{{ route('dosen.laporan-aktivitas.milestone.review', [$kelompok->id, $milestone->id]) }}" method="POST">
                    @csrf

                    <textarea
                        name="catatan"
                        rows="4"
                        placeholder="Tuliskan arahan perbaikan, kepatuhan teknis, atau catatan evaluasi untuk Milestone {{ $milestone->urutan ?? 1 }} {{ $kelompok->nama_kelompok }}..."
                        style="width: 100%; border: 1px solid var(--sim-border); border-radius: 10px; padding: 12px 14px; font-size: 13.5px; color: var(--sim-text-dark); font-family: inherit; resize: vertical; box-sizing: border-box; outline: none; background: #FFFFFF; line-height: 1.5;"
                    >{{ old('catatan', $catatanEvaluasi) }}</textarea>

                    <p style="font-size: 11.5px; color: var(--sim-text-muted); margin: 8px 0 20px; font-style: italic; line-height: 1.4;">
                        * Catatan ini akan otomatis muncul pada dashboard riwayat logbook dan milestone mahasiswa.
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" name="action" value="setujui" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 11px 18px; background: #2E6B42; color: #FFFFFF; border: none; border-radius: 10px; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: background 0.15s;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Setujui Laporan
                        </button>

                        <button type="submit" name="action" value="revisi" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 11px 18px; background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; border-radius: 10px; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: background 0.15s;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                            Revisi Laporan
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2. Anggota Kelompok List --}}
            <div class="ms-card">
                <p class="ms-card-label" style="margin-bottom: 16px;">ANGGOTA {{ strtoupper($kelompok->nama_kelompok) }}</p>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @forelse($kelompok->anggotaTerurut() as $anggota)
                        @php
                            $mhs = $anggota->mahasiswa;
                            $parts = preg_split('/\s+/', trim($mhs?->nama ?? 'Mahasiswa')) ?: [];
                            $awal = $parts[0] ?? 'M';
                            $inisial = strtoupper(substr($awal, 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : substr($awal, 1, 1)));
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                <div style="width: 34px; height: 34px; border-radius: 50%; background: #F2ECE4; color: #4A3E33; display: flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 700; flex-shrink: 0;">
                                    {{ $inisial }}
                                </div>
                                <div style="min-width: 0;">
                                    <p style="font-size: 13px; font-weight: 600; color: var(--sim-text-dark); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $mhs?->nama ?? 'Nama Mahasiswa' }}
                                    </p>
                                    <span style="font-size: 11px; color: var(--sim-text-muted);">
                                        {{ $anggota->is_ketua ? 'Ketua' : 'Anggota' }}
                                    </span>
                                </div>
                            </div>
                            <span style="font-size: 12px; font-family: monospace; color: var(--sim-text-muted); flex-shrink: 0;">
                                {{ $mhs?->nim ?? '-' }}
                            </span>
                        </div>
                    @empty
                        <p style="font-size: 12px; color: var(--sim-text-muted); font-style: italic; margin: 0;">
                            Belum ada anggota terdaftar di kelompok ini.
                        </p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
