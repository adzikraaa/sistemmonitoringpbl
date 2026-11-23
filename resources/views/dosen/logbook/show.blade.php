@extends('layouts.dosen')

@section('title', 'Review Logbook - ' . ($logbook->judul ?: 'Catatan Kegiatan'))
@section('breadcrumb', 'Laporan aktivitas')
@section('meta_description', 'Detail evaluasi logbook kegiatan kelompok PBL oleh dosen pembimbing.')

@push('styles')
<style>
.lb-show-shell {
    padding: 0 32px 40px;
    box-sizing: border-box;
}

.lb-show-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr;
    gap: 28px;
    align-items: start;
}

.lb-card {
    background: #FFFFFF;
    border: 1px solid var(--sim-border);
    border-radius: 16px;
    padding: 24px;
    box-sizing: border-box;
}

.lb-card-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: var(--sim-text-muted);
    text-transform: uppercase;
    margin: 0 0 14px;
}

@media (max-width: 1024px) {
    .lb-show-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .lb-show-shell {
        padding: 0 16px 24px;
    }
    .lb-card {
        padding: 18px;
    }
}
</style>
@endpush

@section('content')
<div class="lb-show-shell">

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
        $rawStatus = strtolower($logbook->status ?? 'menunggu-review');
        $isApproved = in_array($rawStatus, ['disetujui', 'approved'], true);
        $isRevision = in_array($rawStatus, ['revisi', 'perlu-revisi', 'perlu revisi'], true);
    @endphp

    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 280px;">
            <p style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-muted); text-transform: uppercase; margin: 0 0 6px;">
                LOGBOOK &bull; {{ strtoupper($kelompok?->nama_kelompok ?? 'KELOMPOK') }}
            </p>
            <h1 style="font-family: 'DM Serif Display', serif; font-size: 28px; font-weight: 400; color: var(--sim-text-dark); margin: 0 0 8px; line-height: 1.25;">
                Logbook : {{ $logbook->judul ?: 'Catatan Kegiatan' }}
            </h1>
            <p style="font-size: 13.5px; color: var(--sim-text-body); margin: 0;">
                {{ $kelompok?->judul_proyek ?: 'Proyek PBL' }} &bull; {{ $kelompok?->kelas?->nama_kelas ?? 'Kelas' }}
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
                Diajukan {{ $logbook->created_at ? $logbook->created_at->diffForHumans() : ($logbook->tanggal ? $logbook->tanggal->diffForHumans() : 'baru saja') }}
            </span>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="lb-show-grid">

        {{-- LEFT COLUMN: Details --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- 01. RINGKASAN AKTIVITAS --}}
            <div class="lb-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                    <span class="lb-card-label" style="margin: 0;">01. RINGKASAN AKTIVITAS</span>
                    <span style="font-size: 12px; color: var(--sim-text-muted); font-weight: 500;">
                        {{ $logbook->tanggal ? $logbook->tanggal->translatedFormat('d M Y') : '-' }}
                    </span>
                </div>

                <p style="font-size: 14px; line-height: 1.7; color: var(--sim-text-dark); margin: 0; white-space: pre-line;">{{ $logbook->deskripsi ?: ($logbook->kegiatan ?: 'Tidak ada ringkasan aktivitas.') }}</p>

                @if($logbook->progress !== null)
                    <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--sim-border-light);">
                        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px;">
                            <span style="color: var(--sim-text-muted); font-weight: 500;">Progress kegiatan (self-report)</span>
                            <span style="font-weight: 700; color: var(--sim-text-dark);">{{ $logbook->progress }}%</span>
                        </div>
                        <div style="height: 6px; background: #EFEAE4; border-radius: 10px; overflow: hidden;">
                            <div style="width: {{ min(100, max(0, (int)$logbook->progress)) }}%; height: 100%; background: #2E6B42; border-radius: 10px;"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 02. CAPAIAN & PEMBELAJARAN TEKNIS --}}
            <div class="lb-card">
                <p class="lb-card-label">02. CAPAIAN &amp; PEMBELAJARAN TEKNIS</p>
                @if(!empty(trim((string)$logbook->hasil)))
                    @php
                        $lines = array_filter(array_map('trim', explode("\n", (string)$logbook->hasil)));
                    @endphp
                    <ul style="margin: 0; padding-left: 20px; display: flex; flex-direction: column; gap: 8px;">
                        @foreach($lines as $line)
                            <li style="font-size: 13.5px; line-height: 1.6; color: var(--sim-text-dark);">
                                {{ ltrim($line, '-•* ') }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: var(--sim-text-muted); font-size: 13px; font-style: italic; margin: 0;">
                        Belum ada catatan capaian &amp; pembelajaran teknis.
                    </p>
                @endif
            </div>

            {{-- 03. KENDALA & HAMBATAN IMPLEMENTASI --}}
            <div class="lb-card">
                <p class="lb-card-label">03. KENDALA &amp; HAMBATAN IMPLEMENTASI</p>
                @if(!empty(trim((string)$logbook->kendala)))
                    <div style="background: #FAF7F3; border: 1px solid #EFEAE4; border-radius: 12px; padding: 18px;">
                        <p style="font-size: 13.5px; line-height: 1.6; color: var(--sim-text-dark); margin: 0; white-space: pre-line;">{{ $logbook->kendala }}</p>
                    </div>
                @else
                    <p style="color: var(--sim-text-muted); font-size: 13px; font-style: italic; margin: 0;">
                        Tidak ada kendala yang dilaporkan.
                    </p>
                @endif
            </div>

            {{-- 04. RENCANA SPRINT MENDATANG --}}
            <div class="lb-card">
                <p class="lb-card-label">04. RENCANA SPRINT MENDATANG</p>
                @if(!empty(trim((string)$logbook->rencana)))
                    @php
                        $sprintLines = array_filter(array_map('trim', explode("\n", (string)$logbook->rencana)));
                        $stepNum = 1;
                    @endphp
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($sprintLines as $sLine)
                            <div style="display: flex; align-items: flex-start; gap: 12px;">
                                <span style="width: 22px; height: 22px; border-radius: 6px; background: #FAF7F3; border: 1px solid #EFEAE4; color: var(--sim-text-muted); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;">
                                    {{ $stepNum++ }}
                                </span>
                                <span style="font-size: 13.5px; line-height: 1.6; color: var(--sim-text-dark);">
                                    {{ preg_replace('/^\d+[\.\)]\s*/', '', ltrim($sLine, '-•* ')) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: var(--sim-text-muted); font-size: 13px; font-style: italic; margin: 0;">
                        Tidak ada rencana sprint mendatang yang dicatat.
                    </p>
                @endif
            </div>

            {{-- 05. BERKAS LAMPIRAN & BUKTI PENGERJAAN --}}
            <div class="lb-card">
                <p class="lb-card-label">05. BERKAS LAMPIRAN &amp; BUKTI PENGERJAAN</p>
                @if(!empty($logbook->file_bukti) || !empty($logbook->link_bukti))
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @if(!empty($logbook->file_bukti))
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #FAF7F3; border: 1px solid #EFEAE4; border-radius: 12px; gap: 16px;">
                                <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                    <div style="width: 34px; height: 34px; border-radius: 8px; background: #FDF6EE; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <div style="min-width: 0;">
                                        <p style="font-size: 13px; font-weight: 600; color: var(--sim-text-dark); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ basename($logbook->file_bukti) }}
                                        </p>
                                        <span style="font-size: 11px; color: var(--sim-text-muted);">Berkas Lampiran</span>
                                    </div>
                                </div>
                                <a href="{{ route('dosen.laporan-aktivitas.logbook.download', $logbook->id) }}" download style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #FFFFFF; border: 1px solid var(--sim-border); color: var(--sim-text-dark); text-decoration: none;" title="Unduh berkas">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                </a>
                            </div>
                        @endif
                        @if(!empty($logbook->link_bukti))
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #FAF7F3; border: 1px solid #EFEAE4; border-radius: 12px; gap: 16px;">
                                <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                    <div style="width: 34px; height: 34px; border-radius: 8px; background: #EAF4EE; color: #2E6B42; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                    </div>
                                    <div style="min-width: 0;">
                                        <p style="font-size: 13px; font-weight: 600; color: var(--sim-text-dark); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $logbook->link_bukti }}
                                        </p>
                                        <span style="font-size: 11px; color: var(--sim-text-muted);">Tautan Eksternal</span>
                                    </div>
                                </div>
                                <a href="{{ $logbook->link_bukti }}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: #FFFFFF; border: 1px solid var(--sim-border); color: var(--sim-text-dark); text-decoration: none;" title="Buka tautan">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <p style="color: var(--sim-text-muted); font-size: 13px; font-style: italic; margin: 0;">
                        Tidak ada berkas lampiran atau tautan bukti pengerjaan yang diunggah.
                    </p>
                @endif
            </div>

        </div>

        {{-- RIGHT COLUMN: Catatan Pembimbing & Anggota Kelompok --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- Card: Catatan Pembimbing --}}
            <div class="lb-card">
                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 16px;">
                    <h2 style="font-family: 'DM Serif Display', serif; font-size: 20px; font-weight: 400; color: var(--sim-text-dark); margin: 0;">Catatan Pembimbing</h2>
                    <span style="font-size: 11px; color: var(--sim-text-muted);">Format Form Evaluasi</span>
                </div>

                <form method="POST" action="{{ route('dosen.laporan-aktivitas.logbook.review', $logbook->id) }}">
                    @csrf
                    <div style="margin-bottom: 12px;">
                        <label for="feedbackTextarea" style="display: block; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; color: var(--sim-text-dark); text-transform: uppercase; margin-bottom: 8px;">Umpan Balik &amp; Masukan Teknis</label>
                        <textarea id="feedbackTextarea"
                                  name="feedback"
                                  rows="6"
                                  placeholder="Tuliskan arahan perbaikan atau apresiasi terhadap hasil progres kelompok..."
                                  style="width: 100%; padding: 12px 14px; border: 1px solid var(--sim-border); border-radius: 10px; font-size: 13px; color: var(--sim-text-dark); box-sizing: border-box; outline: none; font-family: inherit; resize: vertical; line-height: 1.5;">{{ old('feedback', $logbook->feedback) }}</textarea>
                        @error('feedback')
                            <p style="color: #DC2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <p style="font-size: 11.5px; color: var(--sim-text-muted); line-height: 1.5; margin: 0 0 16px;">
                        Catatan ini akan langsung tampil pada riwayat logbook mahasiswa.
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" name="action" value="setujui" style="width: 100%; padding: 12px; background: #2C1A0E; color: #FAF7F3; border: none; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: opacity 0.15s;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Simpan &amp; Setujui Logbook
                        </button>

                        <button type="submit" name="action" value="revisi" style="width: 100%; padding: 12px; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.2"/></svg>
                            Minta Revisi Logbook
                        </button>
                    </div>
                </form>
            </div>

            {{-- Card: Anggota Kelompok --}}
            <div class="lb-card">
                <p class="lb-card-label">
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
