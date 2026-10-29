@extends('layouts.dosen')

@section('title', 'Pilih Kelas')
@section('meta_description', 'Pilih kelas aktif untuk masuk ke ruang kerja dosen SIM-PBL.')

@section('content')
<div class="sim-page-header" style="padding-top: 28px; padding-bottom: 18px;">
    <div class="sim-page-header__left" style="flex:1; min-width:0;">
        <p class="sim-page-header__eyebrow">RUANG DOSEN · PILIH KELAS</p>
        <h1 class="sim-page-header__title">Pilih Kelas</h1>
        <p class="sim-page-header__desc">
            Pilih kelas aktif untuk menavigasi dashboard dosen dan melihat daftar kelompok bimbingan pada semester yang sedang berjalan.
        </p>
    </div>
    <div class="sim-page-header__right">
        <div class="sim-header__user" style="padding: 5px 10px; border: 1px solid var(--sim-border); border-radius: 999px; background: var(--sim-white);">
            <div class="sim-header__user-avatar">DS</div>
            <div class="sim-header__user-info">
                <span class="sim-header__user-name">{{ auth('dosen')->user()->nama ?? 'Dian Hanifudin Subhi, S.Kom., M.Kom.' }}</span>
                <span class="sim-header__user-role">Dosen Pengampu</span>
            </div>
        </div>
    </div>
</div>

@if (session('success') || session('warning') || session('error'))
    <div style="padding: 0 32px 18px;">
        @if (session('success'))
            <div class="sim-catatan" style="background: #EBF7EE; border-color: #CFE7D7; color: #2E6B42;">
                {{ session('success') }}
            </div>
        @elseif (session('warning'))
            <div class="sim-catatan" style="background: #FFF9E6; border-color: #F0E0A0; color: #7C5D00;">
                {{ session('warning') }}
            </div>
        @elseif (session('error'))
            <div class="sim-catatan" style="background: #FDECEC; border-color: #F3C2C2; color: #8B2F2F;">
                {{ session('error') }}
            </div>
        @endif
    </div>
@endif

<div class="sim-dosen-banner">
    <div>
        <div class="sim-dosen-banner__title">Periode Ganjil Berjalan</div>
        <div class="sim-dosen-banner__meta">
            Total kelas yang ditugaskan: <strong>{{ count($kelasList) }} kelas</strong>
        </div>
    </div>
    <button type="button" class="sim-dosen-banner__button">Ganti Semester</button>
</div>

<section class="sim-dosen-grid" id="kelas-grid">
    @foreach ($kelasList as $kelas)
        @php
            $isSelected = ($selectedKelas && $selectedKelas['id'] === $kelas['id']);
        @endphp

        <article
            class="sim-class-card {{ $isSelected ? 'sim-class-card--selected' : '' }}"
            data-class-card
            data-class-id="{{ $kelas['id'] }}"
            style="{{ $isSelected ? 'border-color: rgba(61, 35, 20, 0.8);' : '' }}"
        >
            <span class="sim-class-card__tag {{ $isSelected ? 'sim-class-card__tag--active' : '' }}">
                {{ $isSelected ? '✓ Kelas Dipilih' : 'Penugasan Aktif' }}
            </span>

            <div class="sim-class-card__body">
                <p class="sim-class-card__label">Kelas</p>
                <h2 class="sim-class-card__name">{{ $kelas['nama'] }}</h2>
                <p class="sim-class-card__prodi">{{ $kelas['prodi'] }}</p>

                <div class="sim-class-card__stats">
                    <div class="sim-class-card__stats-head">
                        <span><strong>{{ $kelas['jumlah_kelompok'] }}</strong> Kelompok Bimbingan</span>
                        <strong>{{ $kelas['progress'] }}%</strong>
                    </div>
                    <div class="sim-class-card__progress">
                        <div class="sim-class-card__progress-bar" style="width: {{ $kelas['progress'] }}%"></div>
                    </div>
                    <div class="sim-class-card__review">{{ $kelas['menunggu_review'] }} Item Menunggu Review</div>
                </div>
            </div>

            <button
                type="button"
                class="sim-class-card__button {{ $isSelected ? 'sim-class-card__button--selected' : '' }}"
                data-class-button
                data-class-id="{{ $kelas['id'] }}"
            >
                {{ $isSelected ? '✓ Kelas Terpilih' : 'Pilih Kelas' }}
            </button>
        </article>
    @endforeach
</section>

<div class="sim-class-confirm">
    <div class="sim-class-confirm__inner">
        <p class="sim-class-confirm__text">
            @if ($selectedKelas)
                Anda akan masuk ke Dashboard Dosen dengan konteks: <strong>{{ $selectedKelas['semester'] }} / {{ $selectedKelas['nama'] }}</strong>
            @else
                Belum ada kelas yang dipilih. Pilih salah satu kelas terlebih dahulu.
            @endif
        </p>

        <a
            href="{{ $selectedKelas ? route('dosen.kelompok') . '?class_id=' . $selectedKelas['id'] : '#' }}"
            class="sim-class-confirm__button {{ $selectedKelas ? '' : 'sim-class-confirm__button--disabled' }}"
            onclick="{{ $selectedKelas ? '' : 'event.preventDefault(); alert(\'Pilih kelas terlebih dahulu sebelum masuk ke ruang kelas.\');' }}"
        >
            Masuk ke Kelas →
        </a>
    </div>
</div>

<form id="class-select-form" action="{{ route('dosen.pilih-kelas.select') }}" method="POST" class="hidden">
    @csrf
    <input id="class_id_input" type="hidden" name="class_id" value="{{ $selectedKelas['id'] ?? '' }}">
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('class-select-form');
        const input = document.getElementById('class_id_input');
        const cards = document.querySelectorAll('[data-class-card]');
        const buttons = document.querySelectorAll('[data-class-button]');

        function selectClass(id) {
            if (!form || !input) return;
            input.value = id;
            form.submit();
        }

        cards.forEach(function (card) {
            card.addEventListener('click', function () {
                selectClass(card.dataset.classId);
            });
        });

        buttons.forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.stopPropagation();
                selectClass(button.dataset.classId);
            });
        });
    });
</script>
@endpush
@endsection
