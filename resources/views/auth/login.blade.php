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
            width: 340px;
            min-width: 340px;
            background-color: #231811;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 32px 36px 40px;
            position: relative;
            overflow: hidden;
        }

        /* ── Brand Card ── */
        .brand-card {
            background: #FFFFFF;
            border-radius: 14px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }
        .brand-logos {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        /* ── JTI SVG Logo ── */
        .logo-jti-wrap {
            height: 40px;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        .logo-jti-wrap svg { height: 40px; width: auto; }

        /* ── Polinema Shield Logo ── */
        .logo-polinema-wrap {
            height: 44px;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        .logo-polinema-wrap svg { height: 44px; width: auto; }

        /* Divider between logos and text */
        .brand-divider {
            width: 1px;
            height: 36px;
            background: #D0C8BC;
            flex-shrink: 0;
            margin: 0 2px;
        }
        .brand-label {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.13em;
            text-transform: uppercase;
            color: #6B5E52;
            line-height: 1.5;
        }

        /* ── Left Body (tagline area) ── */
        .login-left-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 20px 0 0;
            position: relative;
            z-index: 1;
        }
        .login-eyebrow {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.16em;
            color: #B08A5A;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .login-tagline {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: clamp(28px, 2.5vw, 38px);
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
                width: 280px;
                min-width: 280px;
                padding: 28px 28px 36px;
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
                padding: 24px 24px 32px;
                /* Compact on mobile — just show brand card + eyebrow */
                justify-content: flex-start;
                gap: 20px;
            }
            .login-left-body {
                padding-bottom: 0;
            }
            .login-tagline {
                font-size: 26px;
            }
            .login-tagline-desc {
                display: none; /* hide on mobile to save space */
            }
            .login-right {
                flex: 1;
                padding: 36px 24px 48px;
                background: #FAF8F5;
            }
        }

        @media (max-width: 480px) {
            .login-left {
                padding: 20px 20px 24px;
            }
            .login-tagline {
                font-size: 23px;
            }
            .login-right {
                padding: 28px 20px 40px;
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

        {{-- Brand Card --}}
        <div class="brand-card">
            <div class="brand-logos">
                {{-- JTI Logo (SVG recreation) --}}
                <div class="logo-jti-wrap">
                    <svg viewBox="0 0 72 48" xmlns="http://www.w3.org/2000/svg">
                        <!-- J: dark navy -->
                        <path d="M6 4 L6 34 Q6 44 16 44 Q22 44 25 38 L19 35 Q18 40 16 40 Q12 40 12 34 L12 4 Z" fill="#1B2A5E"/>
                        <!-- T: left rect orange, right rect red-orange -->
                        <rect x="27" y="4" width="8" height="40" fill="#E8431A"/>
                        <rect x="27" y="4" width="22" height="8" fill="#F47B20"/>
                        <!-- i dot: yellow circle -->
                        <circle cx="60" cy="8" r="5" fill="#F5C518"/>
                        <!-- i stem: orange -->
                        <rect x="56" y="17" width="8" height="27" fill="#F47B20"/>
                    </svg>
                </div>
                {{-- Polinema Shield Logo (SVG recreation) --}}
                <div class="logo-polinema-wrap">
                    <svg viewBox="0 0 48 54" xmlns="http://www.w3.org/2000/svg">
                        <!-- Pentagon shield shape -->
                        <path d="M24 1 L46 10 L46 32 Q46 48 24 53 Q2 48 2 32 L2 10 Z" fill="#4A2F8F" stroke="#6A4FC0" stroke-width="1"/>
                        <!-- Inner border ring -->
                        <path d="M24 5 L42 13 L42 32 Q42 45 24 50 Q6 45 6 32 L6 13 Z" fill="none" stroke="#C8A800" stroke-width="1.2"/>
                        <!-- Gear/sun rays simplified -->
                        <circle cx="24" cy="28" r="12" fill="#3A2070"/>
                        <circle cx="24" cy="28" r="8" fill="#4A2F8F"/>
                        <!-- Tower/monument simplified -->
                        <rect x="22" y="20" width="4" height="14" fill="#C8A800" rx="1"/>
                        <polygon points="24,16 21,22 27,22" fill="#C8A800"/>
                        <!-- Base -->
                        <rect x="19" y="34" width="10" height="3" fill="#C8A800" rx="1"/>
                        <!-- Text arc placeholder -->
                        <text x="24" y="10" text-anchor="middle" font-size="4" font-weight="700" fill="#FFD700" letter-spacing="0.5">POLINEMA</text>
                    </svg>
                </div>
            </div>
            <div class="brand-divider"></div>
            <div class="brand-label">Sistem<br>Monitoring PBL</div>
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
