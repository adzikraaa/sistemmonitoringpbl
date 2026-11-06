@extends('layouts.koordinator')

@section('title', 'Penugasan Dosen Penilai PBL - Ruang Koordinator SIM-PBL')
@section('breadcrumb', 'Penugasan Dosen')

@section('content')
<div style="padding: 28px 32px 48px; background-color: #FAF8F5; min-height: calc(100vh - 120px);">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background: #EBF5EE; border: 1px solid #A8D5B5; color: #2B6E48; padding: 12px 18px; border-radius: 10px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; font-size: 13.5px; font-weight: 500;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #2B6E48; cursor: pointer; font-size: 18px; line-height: 1;">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div style="background: #FDF2F2; border: 1px solid #F5B7B7; color: #9B2C2C; padding: 12px 18px; border-radius: 10px; margin-bottom: 22px; font-size: 13.5px;">
            <strong style="display: block; margin-bottom: 4px;">Terjadi kesalahan saat memproses:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header Page --}}
    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; gap: 20px;">
        <div>
            <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; color: #A3978A; text-transform: uppercase;">
                SIM-PBL / ADMINISTRASI PROGRAM
            </span>
            <h1 style="font-family: 'DM Serif Display', Georgia, serif; font-size: 28px; color: #2D2621; margin: 4px 0 6px; font-weight: 400;">
                Penugasan Dosen Penilai PBL
            </h1>
            <p style="font-size: 13.5px; color: #786F65; margin: 0; line-height: 1.5; max-width: 720px;">
                Atur dosen penilai PBL per kelas dan mata kuliah wajib. Setiap kelas memiliki 3 mata kuliah penilai PBL yang menilai seluruh kelompok di kelas tersebut.
            </p>
        </div>

        <button type="button"
                onclick="openDrawer()"
                style="display: inline-flex; align-items: center; gap: 8px; background: #5C3E21; color: #FFFFFF; font-size: 13.5px; font-weight: 600; padding: 10px 18px; border-radius: 8px; border: none; cursor: pointer; transition: background 0.15s; white-space: nowrap; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Tugaskan Penilai</span>
        </button>
    </div>

    {{-- 4 Stat Cards --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 26px;">
        {{-- Card 1: Dosen Penilai Tersedia --}}
        <div style="background: #FFFFFF; border: 1px solid #E8E2D8; border-radius: 12px; padding: 20px 22px;">
            <div style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #786F65; text-transform: uppercase;">
                DOSEN PENILAI TERSEDIA
            </div>
            <div style="font-family: 'DM Serif Display', Georgia, serif; font-size: 34px; color: #2D2621; margin-top: 6px; line-height: 1;">
                {{ $metrik['dosen_tersedia'] }}
            </div>
        </div>

        {{-- Card 2: Kelas Terliput --}}
        <div style="background: #FFFFFF; border: 1px solid #E8E2D8; border-radius: 12px; padding: 20px 22px;">
            <div style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #786F65; text-transform: uppercase;">
                KELAS TERLIPUT
            </div>
            <div style="font-family: 'DM Serif Display', Georgia, serif; font-size: 34px; color: #2D2621; margin-top: 6px; line-height: 1;">
                {{ $metrik['kelas_terliput_ratio'] }}
            </div>
        </div>

        {{-- Card 3: Mata Kuliah Terliput --}}
        <div style="background: #FFFFFF; border: 1px solid #E8E2D8; border-radius: 12px; padding: 20px 22px;">
            <div style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #786F65; text-transform: uppercase;">
                MATA KULIAH TERLIPUT
            </div>
            <div style="font-family: 'DM Serif Display', Georgia, serif; font-size: 34px; color: #2D2621; margin-top: 6px; line-height: 1;">
                {{ $metrik['mk_terliput_ratio'] }}
            </div>
        </div>

        {{-- Card 4: Sisa Mata Kuliah --}}
        <div style="background: #FFFFFF; border: 1px solid #E8E2D8; border-radius: 12px; padding: 20px 22px;">
            <div style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #786F65; text-transform: uppercase;">
                SISA MATA KULIAH
            </div>
            <div style="font-family: 'DM Serif Display', Georgia, serif; font-size: 34px; color: #2D2621; margin-top: 6px; line-height: 1;">
                {{ $metrik['sisa_mk'] }}
            </div>
        </div>
    </div>

    {{-- Main Content 2 Columns: Left (Filters + Table + Callout) & Right (Direktori Dosen) --}}
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">

        {{-- Left Column: Filter Kelas, Chips, Table, Callout --}}
        <div>
            {{-- Filter Bar --}}
            <div style="margin-bottom: 14px;">
                <form method="GET" action="{{ route('koordinator.penugasan-dosen.index') }}" id="filterKelasForm" style="margin: 0; display: inline-block;">
                    <div style="position: relative; display: inline-block;">
                        <select name="kelas_id"
                                onchange="document.getElementById('filterKelasForm').submit()"
                                style="appearance: none; background: #FFFFFF; border: 1px solid #D6CEC3; border-radius: 8px; padding: 7px 32px 7px 14px; font-size: 13.5px; font-weight: 600; color: #2D2621; cursor: pointer; outline: none;">
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}" {{ ($selectedKelas && $selectedKelas->id === $k->id) ? 'selected' : '' }}>
                                    Kelas: {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #786F65; font-size: 11px;">▼</span>
                    </div>
                </form>
            </div>

            {{-- Main Table --}}
            <div style="background: #FFFFFF; border: 1px solid #E8E2D8; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: #FAF7F2; border-bottom: 1px solid #E8E2D8;">
                            <th style="padding: 12px 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #786F65; text-transform: uppercase;">
                                MATA KULIAH PENILAI
                            </th>
                            <th style="padding: 12px 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #786F65; text-transform: uppercase;">
                                DOSEN PENILAI
                            </th>
                            <th style="padding: 12px 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #786F65; text-transform: uppercase;">
                                KELAS
                            </th>
                            <th style="padding: 12px 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; color: #786F65; text-transform: uppercase; text-align: right;">
                                AKSI
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tableRows as $row)
                            <tr style="border-bottom: 1px solid #F0ECE4; transition: background 0.1s;">
                                {{-- Mata Kuliah Penilai --}}
                                <td style="padding: 16px 20px; vertical-align: middle;">
                                    <div style="font-size: 14px; font-weight: 700; color: #2D2621;">
                                        {{ $row['nama_matkul'] }}
                                    </div>
                                    <div style="font-size: 12px; color: #A3978A; margin-top: 2px;">
                                        Semester 5 · {{ $row['kelas_terliput'] }} kelas terliput
                                    </div>
                                </td>

                                {{-- Dosen Penilai --}}
                                <td style="padding: 16px 20px; vertical-align: middle;">
                                    @if($row['is_assigned'])
                                        <div style="font-size: 13.5px; color: #2D2621; font-weight: 600;">
                                            {{ $row['dosen']->nama }}
                                        </div>
                                        <div style="font-size: 11.5px; color: #A3978A; margin-top: 1px;">
                                            {{ $row['dosen']->nidn }}
                                        </div>
                                    @else
                                        <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: #B87C2B; font-weight: 500;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #B87C2B;"></span>
                                            <span>Belum ditugaskan</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Kelas Badge --}}
                                <td style="padding: 16px 20px; vertical-align: middle;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #F0ECE4; color: #4A423A;">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: #4A423A;"></span>
                                        <span>{{ $row['kelas_nama'] }}</span>
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td style="padding: 16px 20px; vertical-align: middle; text-align: right;">
                                    @if($row['is_assigned'])
                                        <button type="button"
                                                onclick="openDrawerForEdit({{ $row['pengampu_id'] }}, {{ $row['dosen']->id }}, {{ $row['kelas_id'] }}, {{ $row['mata_kuliah_id'] }})"
                                                style="background: none; border: none; color: #4A423A; font-size: 13px; font-weight: 600; cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: color 0.15s;">
                                            Ubah
                                        </button>
                                    @else
                                        <button type="button"
                                                onclick="openDrawerForAssign({{ $row['kelas_id'] }}, {{ $row['mata_kuliah_id'] }})"
                                                style="background: #F5EFE6; border: 1px solid #D6CEC3; color: #5C3E21; font-size: 12.5px; font-weight: 600; cursor: pointer; padding: 4px 12px; border-radius: 6px; transition: all 0.15s;">
                                            Tugaskan
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 32px 20px; text-align: center; color: #786F65; font-size: 13.5px;">
                                    Tidak ada mata kuliah penilai yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Table Pagination / Count --}}
                <div style="padding: 14px 20px; background: #FAF7F2; border-top: 1px solid #E8E2D8; display: flex; align-items: center; justify-content: flex-end; gap: 8px; font-size: 12px; color: #786F65;">
                    <span style="color: #A3978A; cursor: default;">&lt;</span>
                    <span style="width: 22px; height: 22px; border-radius: 4px; background: #5C3E21; color: #FFFFFF; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px;">1</span>
                    <span>dari 1</span>
                    <span style="color: #A3978A; cursor: default;">&gt;</span>
                </div>
            </div>

            {{-- Bottom Callout Card: Penugasan per kelas --}}
            <div style="background: #FAF7F2; border: 1px solid #EDE8E1; border-radius: 12px; padding: 18px 22px; display: flex; align-items: flex-start; gap: 14px; margin-top: 20px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #F0ECE4; color: #875529; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 13.5px; font-weight: 700; color: #2D2621;">
                        Penugasan per kelas
                    </div>
                    <div style="font-size: 13px; color: #786F65; line-height: 1.5; margin-top: 2px;">
                        Setiap kelas memiliki 3 penilai PBL untuk mata kuliah yang sama. Penugasan ini mencakup seluruh kelompok di kelas tersebut secara otomatis tanpa perlu memilih kelompok satu per satu.
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column: Direktori Dosen Penilai --}}
        <div>
            <div style="background: #FFFFFF; border: 1px solid #E8E2D8; border-radius: 12px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2px;">
                    <h2 style="font-family: 'DM Serif Display', Georgia, serif; font-size: 20px; color: #2D2621; margin: 0; font-weight: 400;">
                        Direktori dosen penilai
                    </h2>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <span style="font-size: 11px; color: #A3978A;">Master Data → Pengguna · Dosen penilai PBL</span>
                    <a href="#" style="font-size: 11.5px; color: #875529; text-decoration: none; font-weight: 600;">Kelola »</a>
                </div>

                {{-- Search Box --}}
                <div style="position: relative; margin-bottom: 16px;">
                    <input type="text"
                           id="searchDosenInput"
                           placeholder="Cari nama atau ID dosen penilai..."
                           oninput="filterDirektoriDosen(this.value)"
                           style="width: 100%; box-sizing: border-box; background: #FAF8F5; border: 1px solid #D6CEC3; border-radius: 8px; padding: 8px 12px 8px 34px; font-size: 12.5px; color: #2D2621; outline: none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A3978A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>

                {{-- Lecturer Cards List --}}
                <div id="direktoriDosenList" style="display: flex; flex-direction: column; gap: 14px; max-height: 580px; overflow-y: auto; padding-right: 2px;">
                    @foreach($direktoriDosen as $dosen)
                        <div class="direktori-item"
                             data-nama="{{ strtolower($dosen['nama']) }}"
                             data-nidn="{{ strtolower($dosen['nidn']) }}"
                             style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid #F0ECE4;">
                            <div style="display: flex; align-items: flex-start; gap: 10px; min-width: 0; flex: 1;">
                                {{-- Avatar inisial --}}
                                <div style="width: 34px; height: 34px; border-radius: 50%; background: #F0ECE4; color: #4A423A; font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                    {{ $dosen['initials'] }}
                                </div>
                                <div style="min-width: 0; flex: 1;">
                                    <div style="font-size: 13px; font-weight: 700; color: #2D2621; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $dosen['nama'] }}">
                                        {{ $dosen['nama'] }}
                                    </div>
                                    <div style="font-size: 11px; color: #A3978A; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $dosen['subtitle'] }}">
                                        {{ $dosen['subtitle'] }}
                                    </div>
                                    <div style="font-size: 11px; color: #786F65; margin-top: 4px;">
                                        {{ $dosen['assigned_count'] }}/3 mata kuliah · {{ $dosen['slot_tersedia'] }} slot tersedia
                                    </div>
                                    {{-- Progress Bar --}}
                                    <div style="width: 100%; height: 4px; background: #EFEBE4; border-radius: 2px; margin-top: 4px; overflow: hidden;">
                                        <div style="width: {{ $dosen['percent'] }}%; height: 100%; background: #875529; border-radius: 2px;"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Status or Action button --}}
                            <div style="margin-top: 4px; flex-shrink: 0;">
                                @if($dosen['is_penuh'])
                                    <span style="font-size: 12px; font-weight: 600; color: #A3978A;">Penuh</span>
                                @else
                                    <button type="button"
                                            onclick="openDrawerWithDosen({{ $dosen['id'] }})"
                                            style="background: none; border: none; font-size: 12px; font-weight: 600; color: #5C3E21; cursor: pointer; padding: 2px 4px;">
                                        Pilih
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Slide-over Drawer Backdrop & Panel --}}
<div id="drawerBackdrop"
     onclick="closeDrawer()"
     style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 998; transition: opacity 0.2s;">
</div>

<div id="drawerPanel"
     style="position: fixed; top: 0; right: -500px; width: 480px; max-width: 90vw; height: 100vh; background: #FFFFFF; box-shadow: -6px 0 24px rgba(0,0,0,0.12); z-index: 999; display: flex; flex-direction: column; transition: right 0.25s cubic-bezier(0.16, 1, 0.3, 1);">

    {{-- Drawer Header --}}
    <div style="padding: 24px 28px 18px; border-bottom: 1px solid #E8E2D8; display: flex; align-items: flex-start; justify-content: space-between;">
        <div>
            <div style="font-size: 10px; font-weight: 700; letter-spacing: 0.1em; color: #A3978A; text-transform: uppercase;">
                PENUGASAN / GANJIL 2026/2027
            </div>
            <h2 id="drawerTitle" style="font-family: 'DM Serif Display', Georgia, serif; font-size: 24px; color: #2D2621; margin: 4px 0 4px; font-weight: 400;">
                Tugaskan Dosen
            </h2>
            <p id="drawerSubtitle" style="font-size: 12.5px; color: #786F65; margin: 0;">
                Pilih dosen aktif, kelas, dan mata kuliah wajib.
            </p>
        </div>
        <button type="button"
                onclick="closeDrawer()"
                style="background: none; border: none; font-size: 22px; color: #786F65; cursor: pointer; line-height: 1; padding: 4px;">
            &times;
        </button>
    </div>

    {{-- Drawer Body / Form Content --}}
    <div style="flex: 1; overflow-y: auto; padding: 22px 28px;">
        <form id="drawerForm" method="POST" action="{{ route('koordinator.penugasan-dosen.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="pengampu_id" id="editPengampuId" value="">

            {{-- 1. Dosen Pembimbing / Penilai Radio Selection --}}
            <div style="margin-bottom: 22px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #2D2621; margin-bottom: 8px;">
                    Dosen pembimbing <span style="color: #B87C2B;">*</span>
                </label>

                {{-- Search Dosen in Drawer --}}
                <div style="position: relative; margin-bottom: 10px;">
                    <input type="text"
                           placeholder="Cari nama atau ID dosen..."
                           oninput="filterDrawerDosen(this.value)"
                           style="width: 100%; box-sizing: border-box; background: #FAF8F5; border: 1px solid #D6CEC3; border-radius: 8px; padding: 7px 12px 7px 32px; font-size: 12px; color: #2D2621; outline: none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A3978A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>

                {{-- Selectable Dosen Cards --}}
                <div id="drawerDosenList" style="display: flex; flex-direction: column; gap: 8px; max-height: 200px; overflow-y: auto; padding-right: 2px;">
                    @foreach($direktoriDosen as $dosen)
                        <label class="drawer-dosen-card"
                               data-nama="{{ strtolower($dosen['nama']) }}"
                               data-nidn="{{ strtolower($dosen['nidn']) }}"
                               data-dosen-id="{{ $dosen['id'] }}"
                               data-assigned="{{ $dosen['assigned_count'] }}"
                               style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border: 1px solid #E8E2D8; border-radius: 10px; cursor: {{ $dosen['is_penuh'] ? 'not-allowed' : 'pointer' }}; background: {{ $dosen['is_penuh'] ? '#F9F7F5' : '#FFFFFF' }}; opacity: {{ $dosen['is_penuh'] ? '0.7' : '1' }}; transition: all 0.15s;">
                            <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                <input type="radio"
                                       name="dosen_id"
                                       value="{{ $dosen['id'] }}"
                                       {{ $dosen['is_penuh'] ? 'disabled' : '' }}
                                       onchange="onDosenRadioChange(this)"
                                       style="display: none;">
                                <div class="radio-indicator" style="width: 18px; height: 18px; border-radius: 50%; border: 1.5px solid #CFC7BC; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: #FFFFFF;">
                                    <span class="radio-check-icon" style="display: none; font-size: 11px; color: #FFFFFF; font-weight: 700;">✓</span>
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-size: 12.5px; font-weight: 700; color: #2D2621; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $dosen['nama'] }} · {{ $dosen['assigned_count'] }}/3 penugasan
                                    </div>
                                    <div style="font-size: 11px; color: #786F65;">
                                        {{ $dosen['nidn'] }} · mendapat {{ $dosen['assigned_count'] }} penugasan
                                    </div>
                                </div>
                            </div>

                            @if($dosen['is_penuh'])
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A3978A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            @endif
                        </label>
                    @endforeach
                </div>
                <div style="font-size: 11px; color: #A3978A; margin-top: 6px;">
                    Sumber: Master Data → Pengguna · {{ count($direktoriDosen) }} dosen aktif untuk penugasan PBL
                </div>
            </div>

            {{-- 2. Semester --}}
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #2D2621; margin-bottom: 6px;">
                    Semester <span style="color: #B87C2B;">*</span>
                </label>
                <select name="semester" disabled style="width: 100%; box-sizing: border-box; background: #F9F7F5; border: 1px solid #D6CEC3; border-radius: 8px; padding: 8px 12px; font-size: 13px; color: #4A423A;">
                    <option value="5" selected>Semester 5</option>
                </select>
            </div>

            {{-- 3. Kelas --}}
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: #2D2621; margin-bottom: 6px;">
                    Kelas <span style="color: #B87C2B;">*</span>
                </label>
                <select name="kelas_id" id="drawerKelasSelect" onchange="updateDrawerMataKuliahPreview()" style="width: 100%; box-sizing: border-box; background: #FFFFFF; border: 1px solid #D6CEC3; border-radius: 8px; padding: 8px 12px; font-size: 13px; color: #2D2621; outline: none; font-weight: 500;">
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ ($selectedKelas && $selectedKelas->id === $k->id) ? 'selected' : '' }}>
                            Kelas {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 4. Penilai per mata kuliah (3 mata kuliah wajib) --}}
            <div style="margin-bottom: 22px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <label style="font-size: 13px; font-weight: 700; color: #2D2621; margin: 0;">
                        Penilai per mata kuliah <span style="color: #B87C2B;">*</span>
                    </label>
                    <span style="font-size: 11px; font-weight: 600; color: #786F65;">3 mata kuliah wajib</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach($mataKuliahList as $mk)
                        <label class="mk-checkbox-card" style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border: 1px solid #E8E2D8; border-radius: 10px; cursor: pointer; background: #FFFFFF; transition: all 0.15s;">
                            <input type="checkbox"
                                   name="mata_kuliah_ids[]"
                                   value="{{ $mk->id }}"
                                   onchange="onMkCheckboxChange()"
                                   style="width: 16px; height: 16px; accent-color: #5C3E21;">
                            <span style="font-size: 13px; font-weight: 600; color: #2D2621;">
                                {{ $mk->nama_matkul }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- 5. Live Info Banner & Pratinjau Kapasitas --}}
            <div id="drawerCapacityPreview" style="background: #FDFBF7; border: 1px solid #EFE8DE; border-radius: 10px; padding: 12px 14px; margin-bottom: 18px; font-size: 12px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-weight: 700; color: #2D2621;">Pratinjau kapasitas</span>
                    <span id="capacityStatusText" style="font-weight: 600; color: #3B7454;">• Valid</span>
                </div>
                <div id="capacityCalculationText" style="color: #786F65; margin-bottom: 6px;">
                    Pilih dosen dan mata kuliah untuk memeriksa kapasitas.
                </div>
                <div style="width: 100%; height: 5px; background: #EFEBE4; border-radius: 3px; overflow: hidden;">
                    <div id="capacityProgressBar" style="width: 0%; height: 100%; background: #875529; border-radius: 3px; transition: width 0.2s;"></div>
                </div>
                <div style="font-size: 11px; color: #A3978A; margin-top: 6px;">
                    Batas 3 mata kuliah per dosen · tidak melebihi kapasitas
                </div>
            </div>

            {{-- 6. Penjelasan Penugasan per Kelas --}}
            <div style="font-size: 11.5px; color: #786F65; line-height: 1.5; margin-bottom: 24px; padding: 0 4px;">
                Setelah disimpan, penugasan mencakup seluruh kelompok di kelas terpilih untuk tiga mata kuliah wajib. Direktori kelas dan riwayat penugasan akan ikut diperbarui.
            </div>

            {{-- Form Actions --}}
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding-top: 14px; border-top: 1px solid #E8E2D8;">
                <button type="button"
                        onclick="closeDrawer()"
                        style="background: #FFFFFF; border: 1px solid #CFC7BC; color: #2D2621; font-size: 13px; font-weight: 600; padding: 9px 18px; border-radius: 8px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit"
                        id="submitDrawerBtn"
                        style="display: inline-flex; align-items: center; gap: 6px; background: #5C3E21; color: #FFFFFF; font-size: 13px; font-weight: 600; padding: 9px 20px; border-radius: 8px; border: none; cursor: pointer;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span id="submitBtnText">Simpan penilai</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let selectedDosenAssigned = 0;

    function openDrawer() {
        resetDrawerForm();
        document.getElementById('drawerTitle').innerText = 'Tugaskan Dosen';
        document.getElementById('drawerSubtitle').innerText = 'Pilih dosen aktif, kelas, dan mata kuliah wajib.';
        document.getElementById('submitBtnText').innerText = 'Simpan penilai';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('drawerForm').action = "{{ route('koordinator.penugasan-dosen.store') }}";

        document.getElementById('drawerBackdrop').style.display = 'block';
        setTimeout(() => {
            document.getElementById('drawerPanel').style.right = '0';
        }, 10);
    }

    function openDrawerWithDosen(dosenId) {
        openDrawer();
        const radio = document.querySelector(`input[name="dosen_id"][value="${dosenId}"]`);
        if (radio && !radio.disabled) {
            radio.checked = true;
            onDosenRadioChange(radio);
        }
    }

    function openDrawerForAssign(kelasId, mkId) {
        openDrawer();
        const kelasSelect = document.getElementById('drawerKelasSelect');
        if (kelasSelect) kelasSelect.value = kelasId;

        const mkCheckbox = document.querySelector(`input[name="mata_kuliah_ids[]"][value="${mkId}"]`);
        if (mkCheckbox) {
            mkCheckbox.checked = true;
            onMkCheckboxChange();
        }
    }

    function openDrawerForEdit(pengampuId, currentDosenId, kelasId, mkId) {
        resetDrawerForm();
        document.getElementById('drawerTitle').innerText = 'Ubah Dosen Penilai';
        document.getElementById('drawerSubtitle').innerText = 'Pilih dosen pengganti untuk mata kuliah di kelas ini.';
        document.getElementById('submitBtnText').innerText = 'Perbarui Penilai';
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('drawerForm').action = "{{ url('koordinator/penugasan-dosen') }}/" + pengampuId;
        document.getElementById('editPengampuId').value = pengampuId;

        const kelasSelect = document.getElementById('drawerKelasSelect');
        if (kelasSelect) kelasSelect.value = kelasId;

        const mkCheckbox = document.querySelector(`input[name="mata_kuliah_ids[]"][value="${mkId}"]`);
        if (mkCheckbox) {
            mkCheckbox.checked = true;
            onMkCheckboxChange();
        }

        const radio = document.querySelector(`input[name="dosen_id"][value="${currentDosenId}"]`);
        if (radio) {
            radio.checked = true;
            onDosenRadioChange(radio);
        }

        document.getElementById('drawerBackdrop').style.display = 'block';
        setTimeout(() => {
            document.getElementById('drawerPanel').style.right = '0';
        }, 10);
    }

    function closeDrawer() {
        document.getElementById('drawerPanel').style.right = '-500px';
        setTimeout(() => {
            document.getElementById('drawerBackdrop').style.display = 'none';
        }, 250);
    }

    function resetDrawerForm() {
        document.getElementById('drawerForm').reset();
        document.querySelectorAll('.drawer-dosen-card').forEach(card => {
            card.style.borderColor = '#E8E2D8';
            card.style.background = card.getAttribute('data-assigned') >= 3 ? '#F9F7F5' : '#FFFFFF';
            const ind = card.querySelector('.radio-indicator');
            if (ind) {
                ind.style.borderColor = '#CFC7BC';
                ind.style.background = '#FFFFFF';
                ind.querySelector('.radio-check-icon').style.display = 'none';
            }
        });
        document.querySelectorAll('.mk-checkbox-card').forEach(card => {
            card.style.borderColor = '#E8E2D8';
            card.style.background = '#FFFFFF';
        });
        selectedDosenAssigned = 0;
        updateCapacityPreview();
    }

    function onDosenRadioChange(radio) {
        document.querySelectorAll('.drawer-dosen-card').forEach(card => {
            const isChecked = card.contains(radio) && radio.checked;
            const ind = card.querySelector('.radio-indicator');
            if (isChecked) {
                card.style.borderColor = '#875529';
                card.style.background = '#FDFBF7';
                if (ind) {
                    ind.style.borderColor = '#875529';
                    ind.style.background = '#875529';
                    ind.querySelector('.radio-check-icon').style.display = 'inline';
                }
                selectedDosenAssigned = parseInt(card.getAttribute('data-assigned') || '0');
            } else {
                card.style.borderColor = '#E8E2D8';
                card.style.background = card.getAttribute('data-assigned') >= 3 ? '#F9F7F5' : '#FFFFFF';
                if (ind) {
                    ind.style.borderColor = '#CFC7BC';
                    ind.style.background = '#FFFFFF';
                    ind.querySelector('.radio-check-icon').style.display = 'none';
                }
            }
        });
        updateCapacityPreview();
    }

    function onMkCheckboxChange() {
        document.querySelectorAll('.mk-checkbox-card').forEach(card => {
            const cb = card.querySelector('input[type="checkbox"]');
            if (cb && cb.checked) {
                card.style.borderColor = '#875529';
                card.style.background = '#FDFBF7';
            } else {
                card.style.borderColor = '#E8E2D8';
                card.style.background = '#FFFFFF';
            }
        });
        updateCapacityPreview();
    }

    function updateCapacityPreview() {
        const checkedMkCount = document.querySelectorAll('input[name="mata_kuliah_ids[]"]:checked').length;
        const totalAfter = selectedDosenAssigned + checkedMkCount;
        const calcEl = document.getElementById('capacityCalculationText');
        const statusEl = document.getElementById('capacityStatusText');
        const barEl = document.getElementById('capacityProgressBar');
        const submitBtn = document.getElementById('submitDrawerBtn');

        if (selectedDosenAssigned === 0 && checkedMkCount === 0) {
            calcEl.innerText = 'Pilih dosen dan mata kuliah untuk memeriksa kapasitas.';
            statusEl.innerText = '• Siap';
            statusEl.style.color = '#786F65';
            barEl.style.width = '0%';
            barEl.style.background = '#875529';
            submitBtn.disabled = false;
            return;
        }

        calcEl.innerText = `${selectedDosenAssigned} saat ini + ${checkedMkCount} penugasan baru = ${totalAfter} / 3 penugasan`;
        const percent = Math.min(100, Math.round((totalAfter / 3) * 100));
        barEl.style.width = `${percent}%`;

        if (totalAfter > 3) {
            statusEl.innerText = '• Melebihi Kapasitas';
            statusEl.style.color = '#9B2C2C';
            barEl.style.background = '#9B2C2C';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
        } else {
            statusEl.innerText = '• Valid';
            statusEl.style.color = '#3B7454';
            barEl.style.background = '#875529';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }
    }

    function filterDirektoriDosen(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.direktori-item').forEach(item => {
            const nama = item.getAttribute('data-nama') || '';
            const nidn = item.getAttribute('data-nidn') || '';
            if (nama.includes(q) || nidn.includes(q)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function filterDrawerDosen(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.drawer-dosen-card').forEach(item => {
            const nama = item.getAttribute('data-nama') || '';
            const nidn = item.getAttribute('data-nidn') || '';
            if (nama.includes(q) || nidn.includes(q)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function updateDrawerMataKuliahPreview() {
        // Reserved for future dynamic class checks
    }
</script>
@endpush
