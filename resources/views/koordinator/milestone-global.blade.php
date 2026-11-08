@extends('layouts.koordinator')

@section('title', 'Milestone Global — SIM-PBL')
@section('breadcrumb_title', 'Milestone Global')

@push('styles')
<style>
    .sim-milestone-page {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
    }

    .sim-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 8px;
    }

    .sim-page-label {
        margin: 0 0 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #9B8E82;
    }

    .sim-page-title {
        margin: 0;
        font-family: 'DM Serif Display', Georgia, serif;
        font-size: clamp(2rem, 3vw, 3.2rem);
        color: #2B221D;
        font-weight: 400;
        line-height: 1.1;
    }

    .sim-page-subtitle {
        margin: 0 0 22px;
        color: #7A6E63;
        font-size: 14px;
    }

    .sim-primary-btn,
    .sim-secondary-btn,
    .sim-edit-btn {
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .sim-primary-btn {
        background: #5C4436;
        color: #fff;
        border: 1px solid #5C4436;
        padding: 10px 18px;
        font-size: 13px;
        text-decoration: none;
    }

    .sim-primary-btn:hover {
        background: #483428;
    }

    .sim-secondary-btn {
        background: #fff;
        border: 1px solid #DDD6CE;
        color: #2B221D;
        padding: 10px 18px;
        font-size: 13px;
    }

    .sim-secondary-btn:hover {
        background: #F8F5F0;
    }

    .sim-page-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(280px, 0.85fr);
        gap: 24px;
        align-items: start;
    }

    .sim-list-panel {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sim-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 0 6px;
        margin-bottom: 4px;
    }

    .sim-list-header h2 {
        margin: 0;
        font-size: 14px;
        color: #2B221D;
    }

    .sim-count-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #7A6E63;
        background: rgba(94, 74, 57, 0.04);
        border: 1px solid #E6DFD5;
        border-radius: 999px;
        padding: 5px 10px;
    }

    .sim-list-item {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid #E6DFD5;
        border-radius: 14px;
        padding: 18px 18px 16px;
        box-shadow: 0 1px 3px rgba(44, 26, 14, 0.02);
        display: grid;
        grid-template-columns: 56px minmax(0, 1fr);
        gap: 16px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .sim-list-item:hover {
        border-color: #D9CFC0;
        box-shadow: 0 5px 18px rgba(44, 26, 14, 0.04);
    }

    .sim-list-item--active {
        border-color: #D9C5A8;
        background: rgba(255, 255, 255, 0.95);
    }

    .sim-step-badge {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F2ECE6;
        color: #2B221D;
        border: 1px solid #E9E0D6;
        font-weight: 700;
        font-size: 18px;
        margin-top: 4px;
    }

    .sim-item-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .sim-item-title {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #2B221D;
    }

    .sim-edit-btn {
        background: #fff;
        border: 1px solid #DDD6CE;
        color: #2B221D;
        padding: 7px 12px;
        font-size: 12px;
    }

    .sim-edit-btn:hover {
        background: #F8F5F0;
    }

    .sim-item-desc {
        margin: 0 0 10px;
        color: #574B40;
        font-size: 13px;
        line-height: 1.6;
    }

    .sim-item-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
        font-size: 12px;
        color: #7A6E63;
    }

    .sim-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    .sim-badge--warning {
        background: #F9F2DF;
        color: #8B5D1A;
    }

    .sim-badge--success {
        background: #EDF4EA;
        color: #2A5A3B;
    }

    .sim-badge--danger {
        background: #FDECEC;
        color: #A13B3B;
    }

    .sim-progress-outer {
        width: 100%;
        height: 8px;
        background: #EEE7E0;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 2px;
    }

    .sim-progress-inner {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, rgba(92,68,54,0.9), rgba(92,68,54,0.65));
    }

    .sim-item-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 8px;
        color: #7A6E63;
        font-size: 11px;
    }

    .sim-form-panel {
        background: rgba(255,255,255,0.7);
        border: 1px solid #E6DFD5;
        border-radius: 16px;
        padding: 20px 20px 18px;
        box-shadow: 0 1px 3px rgba(44, 26, 14, 0.02);
        position: sticky;
        top: 18px;
    }

    .sim-form-title {
        margin: 0 0 8px;
        font-size: 18px;
        color: #2B221D;
        font-weight: 600;
    }

    .sim-form-grid {
        display: grid;
        gap: 12px;
        margin-top: 16px;
    }

    .sim-form-group {
        display: grid;
        gap: 6px;
    }

    .sim-form-label {
        font-size: 12px;
        color: #574B40;
        font-weight: 600;
    }

    .sim-form-input,
    .sim-form-select,
    .sim-form-textarea {
        width: 100%;
        border: 1px solid #DDD6CE;
        background: #fff;
        border-radius: 8px;
        color: #2B221D;
        font-size: 13px;
        padding: 10px 12px;
        font-family: inherit;
        box-sizing: border-box;
    }

    .sim-form-textarea {
        min-height: 88px;
        resize: vertical;
    }

    .sim-form-input:focus,
    .sim-form-select:focus,
    .sim-form-textarea:focus {
        outline: none;
        border-color: #2B221D;
    }

    .sim-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .sim-semester-box {
        background: #F3EDE4;
        border: 1px solid #E7DCCC;
        border-radius: 12px;
        padding: 12px 12px 10px;
        margin-top: 6px;
    }

    .sim-semester-box h4 {
        margin: 0 0 4px;
        font-size: 13px;
        color: #2B221D;
    }

    .sim-semester-box p {
        margin: 0;
        font-size: 12px;
        color: #7A6E63;
    }

    .sim-alert {
        margin-bottom: 14px;
        padding: 10px 12px;
        background: #EDF4EA;
        color: #2A5A3B;
        border: 1px solid #D4E6D2;
        border-radius: 10px;
        font-size: 12px;
    }

    .sim-alert--error {
        background: #FDECEC;
        color: #A13B3B;
        border-color: #F1C4C4;
    }

    .sim-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
    }

    @media (max-width: 980px) {
        .sim-page-grid {
            grid-template-columns: 1fr;
        }

        .sim-form-panel {
            position: relative;
            top: 0;
        }
    }

    @media (max-width: 640px) {
        .sim-page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .sim-form-row {
            grid-template-columns: 1fr;
        }

        .sim-list-item {
            grid-template-columns: 42px minmax(0, 1fr);
        }

        .sim-step-badge {
            width: 42px;
            height: 42px;
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="sim-milestone-page">
    <div class="sim-page-header">
        <div>
            <p class="sim-page-label">SIM-PBL / ADMINISTRASI PROGRAM</p>
            <h1 class="sim-page-title">Milestone Global</h1>
        </div>
        <button type="button" class="sim-primary-btn" id="btnAddMilestone">+ Tambah Milestone</button>
    </div>

    <p class="sim-page-subtitle">Tetapkan satu jadwal pelaksanaan yang bisa diikuti seluruh kelompok PBL.</p>

    <div class="sim-page-grid">
        <section class="sim-list-panel">
            <div class="sim-list-header">
                <h2>{{ $milestones->count() }} tahapan semester</h2>
                <span class="sim-count-chip">* {{ $milestones->count() }} kelompok • {{ optional($periodeAktif)->nama_periode ?? 'Semua periode' }}</span>
            </div>

            @if ($milestones->isEmpty())
                <div class="sim-list-item" style="display:block;">
                    <p class="sim-item-desc" style="margin:0;">Belum ada milestone untuk periode ini. Klik tombol tambah milestone untuk membuat tahapan awal.</p>
                </div>
            @else
                @foreach ($milestones as $milestone)
                    @php
                        $selected = $selectedMilestone && $selectedMilestone->id === $milestone->id;
                        $deadline = $milestone->deadline ? \Illuminate\Support\Facades\Date::parse($milestone->deadline)->format('d M Y') : 'Belum ditetapkan';
                        $statusClass = 'sim-badge--warning';
                        $statusText = 'Belum';
                        if ($milestone->deadline && \Carbon\Carbon::parse($milestone->deadline)->isPast()) {
                            $statusClass = 'sim-badge--danger';
                            $statusText = 'Terlambat';
                        } elseif ($milestone->deadline) {
                            $statusClass = 'sim-badge--success';
                            $statusText = 'Berjalan';
                        }
                    @endphp
                    <article class="sim-list-item {{ $selected ? 'sim-list-item--active' : '' }}" data-id="{{ $milestone->id }}">
                        <div class="sim-step-badge">{{ str_pad((string) $milestone->urutan, 2, '0', STR_PAD_LEFT) }}</div>
                        <div>
                            <div class="sim-item-top">
                                <h3 class="sim-item-title">{{ $milestone->judul }}</h3>
                                <button type="button" class="sim-edit-btn"
                                    data-id="{{ $milestone->id }}"
                                    data-judul="{{ e($milestone->judul) }}"
                                    data-deskripsi="{{ e($milestone->deskripsi ?? '') }}"
                                    data-deadline="{{ $milestone->deadline?->format('Y-m-d') ?? '' }}"
                                    data-urutan="{{ $milestone->urutan }}"
                                    data-periode-id="{{ $milestone->periode_id }}"
                                    data-update-url="{{ route('koordinator.milestone-global.update', $milestone->id) }}">Edit</button>
                            </div>
                            <p class="sim-item-desc">{{ $milestone->deskripsi ?: 'Tidak ada deskripsi untuk milestone ini.' }}</p>
                            <div class="sim-item-meta">
                                <span>{{ $deadline }}</span>
                                <span class="sim-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                            <div class="sim-progress-outer"><span class="sim-progress-inner" style="width: {{ min(100, 18 * $milestone->urutan) }}%"></span></div>
                            <div class="sim-item-footer">
                                <span>{{ $milestone->kode ?? 'M' . str_pad((string) $milestone->urutan, 2, '0', STR_PAD_LEFT) }}</span>
                                <span>{{ $milestone->periode?->nama_periode ?? 'Semester aktif' }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            @endif
        </section>

        <aside class="sim-form-panel">
            <h2 class="sim-form-title" id="formTitle">{{ $selectedMilestone ? 'Edit milestone' : 'Tambah milestone' }}</h2>

            @if (session('success'))
                <div class="sim-alert">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="sim-alert sim-alert--error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form id="milestoneForm" method="POST" action="{{ route('koordinator.milestone-global.store') }}">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">

                <div class="sim-form-grid">
                    <div class="sim-form-group">
                        <label class="sim-form-label" for="judul">Nama milestone</label>
                        <input id="judul" class="sim-form-input" type="text" name="judul" value="{{ old('judul', $selectedMilestone?->judul ?? '') }}" placeholder="Prototype & ujicoba" required>
                    </div>

                    <div class="sim-form-group">
                        <label class="sim-form-label" for="deskripsi">Deskripsi / deliverable</label>
                        <textarea id="deskripsi" class="sim-form-textarea" name="deskripsi" placeholder="Tautan prototipe dan laporan pengujian awal.">{{ old('deskripsi', $selectedMilestone?->deskripsi ?? '') }}</textarea>
                    </div>

                    <div class="sim-form-row">
                        <div class="sim-form-group">
                            <label class="sim-form-label" for="deadline">Tanggal tenggat</label>
                            <input id="deadline" class="sim-form-input" type="date" name="deadline" value="{{ old('deadline', $selectedMilestone?->deadline?->format('Y-m-d') ?? '') }}">
                        </div>

                        <div class="sim-form-group">
                            <label class="sim-form-label" for="urutan">Urutan</label>
                            <input id="urutan" class="sim-form-input" type="number" name="urutan" min="1" value="{{ old('urutan', $selectedMilestone?->urutan ?? ($milestones->count() + 1)) }}" required>
                        </div>
                    </div>

                    <div class="sim-form-group">
                        <label class="sim-form-label" for="periode_id">Semester</label>
                        <select id="periode_id" class="sim-form-select" name="periode_id">
                            @foreach ($periodeOptions as $periode)
                                <option value="{{ $periode->id }}" {{ old('periode_id', $selectedMilestone?->periode_id ?? optional($periodeAktif)->id ?? $periodeOptions->first()?->id) == $periode->id ? 'selected' : '' }}>
                                    {{ $periode->nama_periode }} / {{ $periode->tahun_akademik }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sim-semester-box">
                        <h4>{{ $periodeAktif?->nama_periode ?? 'Semester aktif' }}</h4>
                        <p>{{ $periodeAktif ? $periodeAktif->tanggal_mulai->format('d M Y') . ' — ' . $periodeAktif->tanggal_selesai->format('d M Y') : 'Semester belum tersedia' }}</p>
                    </div>
                </div>

                <div class="sim-form-actions">
                    <button type="button" class="sim-secondary-btn" id="btnCancelForm">Batal</button>
                    <button type="submit" class="sim-primary-btn">Simpan perubahan</button>
                </div>
            </form>
        </aside>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('milestoneForm');
        const formTitle = document.getElementById('formTitle');
        const methodField = document.getElementById('methodField');
        const addBtn = document.getElementById('btnAddMilestone');
        const cancelBtn = document.getElementById('btnCancelForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const createUrl = '{{ route('koordinator.milestone-global.store') }}';

        function resetToCreateMode() {
            form.action = createUrl;
            methodField.value = 'POST';
            formTitle.textContent = 'Tambah milestone';
            form.reset();
            document.getElementById('periode_id').value = '{{ optional($periodeAktif)->id ?? optional($periodeOptions->first())->id ?? '' }}';
        }

        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        });

        addBtn.addEventListener('click', resetToCreateMode);
        cancelBtn.addEventListener('click', resetToCreateMode);

        document.querySelectorAll('.sim-edit-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const { id, judul, deskripsi, deadline, urutan, periodeId, updateUrl } = button.dataset;
                form.action = updateUrl;
                methodField.value = 'PUT';
                formTitle.textContent = 'Edit milestone';

                document.getElementById('judul').value = judul || '';
                document.getElementById('deskripsi').value = deskripsi || '';
                document.getElementById('deadline').value = deadline || '';
                document.getElementById('urutan').value = urutan || 1;
                document.getElementById('periode_id').value = periodeId || '{{ optional($periodeAktif)->id ?? optional($periodeOptions->first())->id ?? '' }}';
            });
        });
    });
</script>
@endpush
@endsection
