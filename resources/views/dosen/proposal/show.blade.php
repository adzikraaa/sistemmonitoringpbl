@extends('layouts.dosen')

@php
    /** @var \App\Models\Proposal $proposal */
    /** @var \App\Models\Kelompok|null $kelompok */
@endphp

@section('title', 'Review Proposal - ' . ($proposal->judul ?: 'Pengajuan Proposal'))
@section('breadcrumb', 'Laporan aktivitas')
@section('meta_description', 'Detail evaluasi proposal proyek kelompok PBL oleh dosen pembimbing.')

@push('styles')
<style>
.prop-show-shell {
    padding: 0 32px 40px;
    box-sizing: border-box;
}

.prop-show-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 28px;
    align-items: start;
}

.prop-card {
    background: #FFFFFF;
    border: 1px solid var(--sim-border);
    border-radius: 16px;
    padding: 24px;
    box-sizing: border-box;
}

.prop-card-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: var(--sim-text-muted);
    text-transform: uppercase;
    margin: 0 0 14px;
}

.prop-field-label {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--sim-text-dark);
    margin: 0 0 6px;
    display: block;
}

.prop-field-desc {
    font-size: 13.5px;
    line-height: 1.65;
    color: var(--sim-text-body);
    margin: 0 0 18px;
    white-space: pre-line;
}

.prop-info-box {
    background: #FAF7F3;
    border: 1px solid #EFEAE4;
    border-radius: 12px;
    padding: 14px 16px;
}

@media (max-width: 1024px) {
    .prop-show-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .prop-show-shell {
        padding: 0 16px 24px;
    }
    .prop-card {
        padding: 18px;
    }
}
</style>
@endpush

@section('content')
<div class="prop-show-shell">

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
    @php
        $rawStatus = strtolower($proposal->status ?? 'menunggu-review');
        $isApproved = in_array($rawStatus, ['disetujui', 'approved'], true);
        $isRevision = in_array($rawStatus, ['revisi', 'perlu-revisi', 'perlu revisi', 'ditolak'], true);
    @endphp

    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 280px;">
            <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin: 0 0 6px;">
                PROPOSAL &bull; {{ strtoupper($kelompok?->nama_kelompok ?? 'KELOMPOK') }}
            </p>
            <h1 style="font-family: 'DM Serif Display', serif; font-size: 28px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 8px; line-height: 1.25;">
                Proposal : {{ $proposal->judul ?: 'Pengajuan Proposal' }}
            </h1>
            <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0;">
                {{ $proposal->judul }} &bull; {{ $kelompok?->kelas?->nama_kelas ?? 'Kelas' }}
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            @if($isApproved)
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 20px; font-size: 12px; font-weight: 600; color: #065F46;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                    Disetujui
                </span>
            @elseif($isRevision)
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 20px; font-size: 12px; font-weight: 600; color: #991B1B;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #DC2626;"></span>
                    Perlu revisi
                </span>
            @else
                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 20px; font-size: 12px; font-weight: 600; color: #92400E;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                    Menunggu review
                </span>
            @endif

            <span style="font-size: 12px; color: var(--sim-text-muted);">
                Diajukan {{ $proposal->tanggal_pengajuan ? $proposal->tanggal_pengajuan->translatedFormat('d M Y') : ($proposal->created_at ? $proposal->created_at->translatedFormat('d M Y') : 'baru saja') }}
            </span>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="prop-show-grid">

        {{-- LEFT COLUMN: Proposal Details --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- 01. INFORMASI PROPOSAL --}}
            <div class="prop-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span class="prop-card-label" style="margin: 0;">01. INFORMASI PROPOSAL</span>
                    <span style="font-size: 11.5px; font-weight: 600; color: #8C7A6B; background: #FAF7F3; padding: 3px 10px; border-radius: 20px; border: 1px solid #EFEAE4;">
                        @if($isApproved)
                            Proposal Disetujui
                        @elseif($isRevision)
                            Tahap Revisi
                        @else
                            Tahap Pengajuan Awal
                        @endif
                    </span>
                </div>

                {{-- Judul Proposal Box --}}
                <span class="prop-field-label">Judul Proposal</span>
                <div style="background: #FAF7F3; border: 1px solid #EFEAE4; border-radius: 12px; padding: 14px 18px; font-weight: 600; font-size: 14px; color: var(--sim-text-dark); margin-bottom: 18px;">
                    {{ $proposal->judul ?: 'Belum mengisi judul proposal' }}
                </div>

                {{-- Latar Belakang --}}
                <span class="prop-field-label">Latar Belakang &amp; Urgensi Masalah</span>
                <p class="prop-field-desc">
                    {{ $proposal->latar_belakang && $proposal->latar_belakang !== '-' ? $proposal->latar_belakang : ($proposal->deskripsi ?: 'Belum ada rincian latar belakang yang dicatat.') }}
                </p>

                {{-- Tujuan Proyek --}}
                <span class="prop-field-label">Tujuan Proyek</span>
                <p class="prop-field-desc">
                    {{ $proposal->tujuan && $proposal->tujuan !== '-' ? $proposal->tujuan : 'Belum ada rincian tujuan proyek yang dicatat.' }}
                </p>

                {{-- Ruang Lingkup & Metode --}}
                <span class="prop-field-label">Ruang Lingkup &amp; Arsitektur Solusi</span>
                <p class="prop-field-desc" style="margin-bottom: 0;">
                    {{ $proposal->metode && $proposal->metode !== '-' ? $proposal->metode : ($proposal->deskripsi ?: 'Belum ada rincian ruang lingkup & metodologi yang dicatat.') }}
                </p>
            </div>

            {{-- 02. INFORMASI PENGAJUAN --}}
            <div class="prop-card">
                <p class="prop-card-label">02. INFORMASI PENGAJUAN</p>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px;">
                    {{-- Box 1 --}}
                    <div class="prop-info-box">
                        <span style="font-size: 9.5px; font-weight: 700; letter-spacing: 0.08em; color: var(--sim-text-muted); text-transform: uppercase; display: block; margin-bottom: 4px;">
                            NAMA KELOMPOK &amp; KELAS
                        </span>
                        <p style="font-size: 13.5px; font-weight: 700; color: var(--sim-text-dark); margin: 0;">
                            {{ $kelompok?->nama_kelompok ?? 'Kelompok' }} &bull; {{ $kelompok?->kelas?->nama_kelas ?? '-' }}
                        </p>
                    </div>

                    {{-- Box 2 --}}
                    <div class="prop-info-box">
                        <span style="font-size: 9.5px; font-weight: 700; letter-spacing: 0.08em; color: var(--sim-text-muted); text-transform: uppercase; display: block; margin-bottom: 4px;">
                            DOSEN PENGAMPU / PEMBIMBING
                        </span>
                        <p style="font-size: 13.5px; font-weight: 700; color: var(--sim-text-dark); margin: 0;">
                            {{ $kelompok?->dosen?->nama ?? auth('dosen')->user()?->nama ?? 'Dosen Pembimbing' }}
                        </p>
                    </div>

                    {{-- Box 3 --}}
                    <div class="prop-info-box">
                        <span style="font-size: 9.5px; font-weight: 700; letter-spacing: 0.08em; color: var(--sim-text-muted); text-transform: uppercase; display: block; margin-bottom: 4px;">
                            TANGGAL &amp; WAKTU PENGAJUAN
                        </span>
                        <p style="font-size: 13.5px; font-weight: 700; color: var(--sim-text-dark); margin: 0;">
                            {{ $proposal->tanggal_pengajuan ? $proposal->tanggal_pengajuan->translatedFormat('d F Y') : ($proposal->created_at ? $proposal->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-') }}
                        </p>
                    </div>

                    {{-- Box 4 --}}
                    <div class="prop-info-box">
                        <span style="font-size: 9.5px; font-weight: 700; letter-spacing: 0.08em; color: var(--sim-text-muted); text-transform: uppercase; display: block; margin-bottom: 4px;">
                            TARGET PENYELESAIAN
                        </span>
                        <p style="font-size: 13.5px; font-weight: 700; color: var(--sim-text-dark); margin: 0;">
                            {{ $kelompok?->periode?->nama_periode ?? 'Semester Ganjil 2026/2027' }} (16 Minggu)
                        </p>
                    </div>
                </div>
            </div>

            {{-- 03. BERKAS LAMPIRAN & TAUTAN PENDUKUNG --}}
            <div class="prop-card">
                <p class="prop-card-label">03. BERKAS LAMPIRAN &amp; TAUTAN PENDUKUNG</p>
                <p style="font-size: 12.5px; color: var(--sim-text-muted); margin: 0 0 16px;">
                    Tinjau berkas dokumen resmi atau buka repositori &amp; prototype desain interaktif yang dilampirkan kelompok.
                </p>

                @if(!empty($proposal->file_proposal))
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: #FAF7F3; border: 1px solid #EFEAE4; border-radius: 12px; gap: 14px;">
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                <div style="width: 38px; height: 38px; border-radius: 10px; background: #FDF6EE; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                </div>
                                <div style="min-width: 0;">
                                    <p style="font-size: 13px; font-weight: 600; color: var(--sim-text-dark); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $proposal->nama_berkas }}
                                    </p>
                                    <span style="font-size: 11px; color: var(--sim-text-muted);">Dokumen PDF Proposal</span>
                                </div>
                            </div>
                            <a href="{{ route('dosen.laporan-aktivitas.proposal.download', $proposal->id) }}" download style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: #FFFFFF; border: 1px solid var(--sim-border); color: var(--sim-text-dark); text-decoration: none;" title="Unduh berkas proposal">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </a>
                        </div>
                    </div>
                @else
                    <p style="color: var(--sim-text-muted); font-size: 13px; font-style: italic; margin: 0;">
                        Tidak ada berkas lampiran yang diunggah.
                    </p>
                @endif
            </div>

        </div>

        {{-- RIGHT COLUMN: Catatan Pembimbing & Anggota Kelompok --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- Card: Catatan Pembimbing --}}
            <div class="prop-card">
                <div style="margin-bottom: 16px;">
                    <h2 style="font-family: 'DM Serif Display', serif; font-size: 20px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 4px;">Catatan Pembimbing</h2>
                </div>

                <form method="POST" action="{{ route('dosen.laporan-aktivitas.proposal.review', $proposal->id) }}">
                    @csrf
                    <div style="margin-bottom: 12px;">
                        <label for="catatanRevisiTextarea" style="display: block; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-dark); text-transform: uppercase; margin-bottom: 8px;">
                            SARAN &amp; MASUKAN PROPOSAL
                        </label>
                        <textarea id="catatanRevisiTextarea"
                                  name="catatan_revisi"
                                  rows="6"
                                  placeholder="Tuliskan arahan perbaikan, catatan metodologi, atau apresiasi terhadap proposal proyek kelompok ini..."
                                  style="width: 100%; padding: 12px 14px; border: 1px solid var(--sim-border); border-radius: 10px; font-size: 13px; color: var(--sim-text-dark); box-sizing: border-box; outline: none; font-family: inherit; resize: vertical; line-height: 1.5;">{{ old('catatan_revisi', $proposal->catatan_revisi) }}</textarea>
                        @error('catatan_revisi')
                            <p style="color: #DC2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <p style="font-size: 11.5px; color: var(--sim-text-muted); line-height: 1.5; margin: 0 0 16px;">
                        Catatan ini akan langsung tampil pada riwayat pengajuan proposal mahasiswa dan menjadi rujukan revisi.
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" name="action" value="setujui" style="width: 100%; padding: 12px; background: #2F5C4A; color: #FAF7F3; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.15s;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Setujui Laporan
                        </button>

                        <button type="submit" name="action" value="revisi" style="width: 100%; padding: 12px; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.2"/></svg>
                            Revisi Laporan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Card: Anggota Kelompok --}}
            <div class="prop-card">
                <p class="prop-card-label">
                    ANGGOTA {{ strtoupper($kelompok?->nama_kelompok ?? 'KELOMPOK') }}
                </p>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @forelse($kelompok?->anggotaKelompok ?? [] as $anggota)
                        @php
                            $namaMhs = $anggota->mahasiswa?->nama ?? 'Mahasiswa';
                            $nimMhs = $anggota->mahasiswa?->nim ?? '-';
                            $isKetua = $anggota->is_ketua;
                            $parts = explode(' ', trim($namaMhs));
                            $inisial = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : (strlen($parts[0]) > 1 ? substr($parts[0], 1, 1) : '')));
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #FAF7F3; border: 1px solid #EFEAE4; color: #5C3317; font-size: 11.5px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    {{ $inisial }}
                                </div>
                                <div style="min-width: 0;">
                                    <p style="font-size: 13px; font-weight: 600; color: var(--sim-text-dark); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $namaMhs }}
                                    </p>
                                    <span style="font-size: 11px; color: var(--sim-text-muted);">
                                        {{ $isKetua ? 'Ketua' : 'Anggota' }}
                                    </span>
                                </div>
                            </div>
                            <span style="font-size: 12px; color: var(--sim-text-muted); font-family: monospace; flex-shrink: 0;">
                                {{ $nimMhs }}
                            </span>
                        </div>
                    @empty
                        <p style="font-size: 13px; color: var(--sim-text-muted); font-style: italic; margin: 0;">
                            Belum ada anggota terdaftar.
                        </p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
