@php
    // Redirect jika sudah login
    foreach (['mahasiswa', 'dosen', 'koordinator'] as $guard) {
        if (auth()->guard($guard)->check()) {
            header('Location: ' . route($guard . '.dashboard'));
            exit;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SIM-PBL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .login-wrap {
            display: flex;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════
           LEFT PANEL
        ══════════════════════════════════════ */
        .login-left {
            width: 360px;
            min-width: 360px;
            background-color: #231811;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 36px 36px 40px;
            position: relative;
            overflow: hidden;
        }

        /* ── Brand Header (Top of left panel) ── */
        .login-brand-header {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .brand-logo-card {
            background: #FFFFFF;
            border-radius: 10px;
            padding: 8px 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.18);
            flex-shrink: 0;
        }

        .brand-logo-img {
            height: 36px;
            width: auto;
            display: block;
            object-fit: contain;
        }

        .brand-header-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .brand-name {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 16px;
            font-weight: 400;
            color: #FAF8F5;
            letter-spacing: 0.03em;
            line-height: 1.2;
        }

        .brand-sub {
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #C3B6AB;
            line-height: 1.2;
        }

        /* ── Left Body (tagline area) ── */
        .login-left-body {
            margin-top: clamp(48px, 11vh, 100px);
            margin-bottom: auto;
            position: relative;
            z-index: 1;
        }

        .login-eyebrow {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.16em;
            color: #B08A5A;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .login-tagline {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: clamp(28px, 2.6vw, 36px);
            font-weight: 400; /* non-bold */
            line-height: 1.18;
            color: #FAF8F5;
            margin-bottom: 20px;
        }

        .login-tagline em {
            font-style: italic;
            color: #D4A86A;
        }

        .login-tagline-desc {
            font-size: 12.5px;
            font-weight: 400;
            color: #8B7B6E;
            line-height: 1.65;
            max-width: 280px;
        }

        /* ══════════════════════════════════════
           RIGHT PANEL
        ══════════════════════════════════════ */
        .login-right {
            flex: 1;
            background-color: #F5F1EA;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
        }

        .login-form-wrap {
            width: 100%;
            max-width: 400px;
        }

        .login-form-eyebrow {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.13em;
            text-transform: uppercase;
            color: #B08A5A;
            margin-bottom: 10px;
        }

        .login-form-title {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: clamp(26px, 3vw, 34px);
            font-weight: 400;
            color: #231811;
            line-height: 1.1;
            margin-bottom: 8px;
        }

        .login-form-desc {
            font-size: 13.5px;
            color: #7A6E63;
            margin-bottom: 32px;
        }

        /* ── Error ── */
        .login-error {
            background: #FEF2F2;
            border: 1.5px solid #FECACA;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #991B1B;
            line-height: 1.5;
        }

        /* ── Form Fields ── */
        .login-field {
            margin-bottom: 18px;
        }

        .login-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #3A2E27;
            margin-bottom: 7px;
        }

        .login-input {
            width: 100%;
            padding: 11px 16px;
            border: 1.5px solid #DDD5C8;
            border-radius: 9px;
            background: #FDFAF7;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            color: #231811;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .login-input::placeholder { color: #B8AFA5; }

        .login-input:focus {
            border-color: #8B6240;
            box-shadow: 0 0 0 3px rgba(139, 98, 64, 0.12);
        }

        .login-pass-wrap {
            position: relative;
        }

        .login-pass-wrap .login-input {
            padding-right: 60px;
        }

        .login-show-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-family: 'Inter', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            color: #8B6240;
            cursor: pointer;
            padding: 4px;
        }

        .login-show-btn:hover { color: #231811; }

        /* ── Remember + Forgot ── */
        .login-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 8px;
        }

        .login-remember {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12.5px;
            color: #5C4F44;
            cursor: pointer;
            user-select: none;
        }

        .login-remember input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #5C4036;
            cursor: pointer;
        }

        .login-forgot {
            font-size: 12.5px;
            color: #8B6240;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
        }

        .login-forgot:hover { text-decoration: underline; }

        /* ── Submit Button ── */
        .login-submit {
            width: 100%;
            padding: 13px 20px;
            background: #2B1D16;
            color: #FAF8F5;
            border: none;
            border-radius: 9px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.01em;
        }

        .login-submit:hover { background: #3D2820; }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */

        /* Tablet: narrow the left panel */
        @media (max-width: 1024px) {
            .login-left {
                width: 300px;
                min-width: 300px;
                padding: 30px 28px 36px;
            }
            .brand-logo-img {
                height: 32px;
            }
            .login-tagline {
                font-size: 28px;
            }
        }

        /* Mobile: stack vertically, left panel becomes top banner */
        @media (max-width: 768px) {
            .login-wrap {
                flex-direction: column;
                min-height: 100vh;
            }
            .login-left {
                width: 100%;
                min-width: 0;
                padding: 24px 20px 24px;
            }
            .login-left-body {
                margin-top: 18px;
                margin-bottom: 0;
            }
            .login-tagline {
                font-size: 24px;
                margin-bottom: 8px;
            }
            .login-tagline-desc {
                display: none;
            }
            .login-right {
                flex: 1;
                padding: 32px 20px 40px;
                background: #FAF8F5;
            }
        }

        @media (max-width: 480px) {
            .login-left {
                padding: 20px 16px 20px;
            }
            .brand-logo-img {
                height: 28px;
            }
            .brand-name {
                font-size: 14px;
            }
            .brand-sub {
                font-size: 7.5px;
            }
            .login-tagline {
                font-size: 22px;
            }
            .login-right {
                padding: 24px 16px 36px;
            }
            .login-form-title {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
<div class="login-wrap">

    {{-- ── LEFT PANEL ── --}}
    <div class="login-left">

        {{-- Brand Header (Logo Card + App Name) --}}
        <div class="login-brand-header">
            <div class="brand-logo-card">
                <img src="{{ asset('images/logo-jti-polinema.png') }}" alt="JTI Polinema" class="brand-logo-img">
            </div>
            <div class="brand-header-text">
                <div class="brand-name">SIM-PBL</div>
                <div class="brand-sub">SISTEM MONITORING PBL</div>
            </div>
        </div>

        {{-- Tagline --}}
        <div class="login-left-body">
            <p class="login-eyebrow">Selamat datang kembali</p>
            <h1 class="login-tagline">
                Ruang yang<br>membuat<br>proses terasa<br><em>mungkin.</em>
            </h1>
            <p class="login-tagline-desc">
                Masuk untuk melanjutkan langkah kecil yang sedang dikerjakan kelompokmu.
            </p>
        </div>

    </div>

    {{-- ── RIGHT PANEL ── --}}
    <div class="login-right">
        <div class="login-form-wrap"
             x-data="{ showPassword: false }">

            <p class="login-form-eyebrow">Akses Ruang Kerja</p>
            <h2 class="login-form-title">Masuk ke SIM-PBL</h2>
            <p class="login-form-desc">Gunakan akun kampus untuk melanjutkan.</p>

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="login-error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Identifier --}}
                <div class="login-field">
                    <label class="login-label" for="identifier">Username</label>
                    <input
                        type="text"
                        id="identifier"
                        name="identifier"
                        value="{{ old('identifier') }}"
                        class="login-input"
                        placeholder="NIM / NIDN"
                        autocomplete="username"
                        required>
                </div>

                {{-- Password --}}
                <div class="login-field">
                    <label class="login-label" for="password">Kata sandi</label>
                    <div class="login-pass-wrap">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            id="password"
                            class="login-input"
                            placeholder="Kata sandi"
                            autocomplete="current-password"
                            required>
                        <button type="button" class="login-show-btn" @click="showPassword = !showPassword"
                            x-text="showPassword ? 'Tutup' : 'Lihat'"></button>
                    </div>
                </div>

                {{-- Remember + Forgot --}}
                <div class="login-row">
                    <label class="login-remember">
                        <input type="checkbox" name="remember" value="1">
                        Ingat perangkat ini
                    </label>
                    <a href="https://siakad.polinema.ac.id/recovery_password"
                       target="_blank" rel="noopener noreferrer"
                       class="login-forgot">
                        Lupa kata sandi?
                    </a>
                </div>

                {{-- Submit --}}
                <button type="submit" class="login-submit">
                    <span>Masuk</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
