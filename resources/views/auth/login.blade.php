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

        /* ── Left Panel ── */
        .login-left {
            width: 320px;
            min-width: 320px;
            background-color: #231811;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 28px 32px 32px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circle */
        .login-left::after {
            content: '';
            position: absolute;
            bottom: -60px;
            right: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.08);
        }
        .login-left::before {
            content: '';
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.05);
        }

        .login-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .login-logo-circle {
            width: 32px;
            height: 32px;
            background: #F4EDE4;
            color: #231811;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 16px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .login-logo-text {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 17px;
            color: #FAF8F5;
            letter-spacing: 0.02em;
        }

        .login-left-body {
            padding-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        .login-eyebrow {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.14em;
            color: #B08A5A;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .login-tagline {
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: 36px;
            line-height: 1.15;
            color: #FAF8F5;
            margin-bottom: 20px;
        }
        .login-tagline em {
            font-style: italic;
            color: #D4A86A;
        }
        .login-tagline-desc {
            font-size: 12.5px;
            color: #8B7B6E;
            line-height: 1.6;
        }

        .login-left-footer {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            color: #6A5A4E;
            position: relative;
            z-index: 1;
        }
        .login-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #4CAF50;
            flex-shrink: 0;
        }

        /* ── Right Panel ── */
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
            font-size: 34px;
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

        /* ── Role Switcher ── */
        .login-role-switcher {
            display: flex;
            background: #EDE8DF;
            border-radius: 10px;
            padding: 4px;
            gap: 3px;
            margin-bottom: 28px;
        }
        .login-role-btn {
            flex: 1;
            padding: 8px 10px;
            border: none;
            border-radius: 7px;
            background: transparent;
            color: #9A8E82;
            font-family: 'Inter', sans-serif;
            font-size: 12.5px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s, color 0.15s, box-shadow 0.15s;
        }
        .login-role-btn.active {
            background: #fff;
            color: #231811;
            font-weight: 600;
            box-shadow: 0 1px 4px rgba(0,0,0,0.10);
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

        /* ── Note ── */
        .login-note {
            margin-top: 20px;
            font-size: 11.5px;
            color: #A89E94;
            text-align: center;
            line-height: 1.6;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .login-left { display: none; }
            .login-right { padding: 32px 20px; background: #FAF8F5; }
        }
    </style>
</head>
<body>
<div class="login-wrap">

    {{-- ── LEFT PANEL ── --}}
    <div class="login-left">
        <a class="login-logo" href="#">
            <div class="login-logo-circle">S</div>
            <span class="login-logo-text">SIM-PBL</span>
        </a>

        <div class="login-left-body">
            <p class="login-eyebrow">Selamat datang kembali</p>
            <h1 class="login-tagline">
                Ruang yang<br>membuat<br>proses terasa<br><em>mungkin.</em>
            </h1>
            <p class="login-tagline-desc">
                Masuk untuk melanjutkan langkah kecil yang sedang dikerjakan kelompokmu.
            </p>
        </div>

        <div class="login-left-footer" style="visibility:hidden; height:0; overflow:hidden;"></div>
    </div>

    {{-- ── RIGHT PANEL ── --}}
    <div class="login-right">
        <div class="login-form-wrap"
             x-data="{
                 role: '{{ old('role', 'mahasiswa') }}',
                 showPassword: false,
                 get submitLabel() {
                     const labels = { mahasiswa: 'Mahasiswa', dosen: 'Dosen', koordinator: 'Koordinator' };
                     return 'Masuk sebagai ' + labels[this.role];
                 }
             }">

            <p class="login-form-eyebrow">Akses Ruang Kerja</p>
            <h2 class="login-form-title">Masuk ke SIM-PBL</h2>
            <p class="login-form-desc">Gunakan akun kampus untuk melanjutkan.</p>

            {{-- Role Switcher --}}
            <div class="login-role-switcher">
                <button type="button"
                    class="login-role-btn"
                    :class="{ active: role === 'mahasiswa' }"
                    @click="role = 'mahasiswa'">
                    Mahasiswa
                </button>
                <button type="button"
                    class="login-role-btn"
                    :class="{ active: role === 'dosen' }"
                    @click="role = 'dosen'">
                    Dosen
                </button>
                <button type="button"
                    class="login-role-btn"
                    :class="{ active: role === 'koordinator' }"
                    @click="role = 'koordinator'">
                    Koordinator
                </button>
            </div>

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
                <input type="hidden" name="role" :value="role">

                {{-- Identifier --}}
                <div class="login-field">
                    <label class="login-label" for="identifier">Username</label>
                    <input
                        type="text"
                        id="identifier"
                        name="identifier"
                        value="{{ old('identifier') }}"
                        class="login-input"
                        placeholder="Username"
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
                    <span x-text="submitLabel">Masuk</span>
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
