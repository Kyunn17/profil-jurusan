<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SMKN 4 Padalarang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="auth-split">
    <aside class="auth-brand-pane">
        <div class="auth-brand-top">
            <span class="brand-mark">S4</span>
            <div>
                <strong style="display: block; font-size: 0.95rem;">SMKN 4 Padalarang</strong>
                <span style="font-size: 0.78rem; color: rgba(255,255,255,0.75);">Sistem Akademik PPLG</span>
            </div>
        </div>

        <div class="auth-brand-body">
            <h1>Satu ruang kerja untuk data akademik sekolah.</h1>
            <p>Pantau presensi harian, buku nilai siswa, jadwal pengajaran, dan pengumuman resmi sekolah dengan cepat dan akurat.</p>
        </div>

        <div class="auth-brand-foot">
            &copy; {{ date('Y') }} SMKN 4 Padalarang · XI PPLG B
        </div>
    </aside>

    <main class="auth-form-pane">
        <div class="auth-form-box">
            <h2>Masuk ke Akun Anda</h2>
            <p class="auth-sub">Gunakan email dan password yang terdaftar di sistem.</p>

            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Email atau password yang Anda masukkan salah.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@sekolah.sch.id">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                </div>

                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 18px; font-size: 0.84rem; color: var(--ink-2);">
                    <input id="remember_me" type="checkbox" name="remember" style="accent-color: var(--brand);">
                    <label for="remember_me" style="cursor: pointer;">Ingat sesi saya</label>
                </div>

                <button type="submit" class="btn auth-submit">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <div class="auth-footer-links">
                @if (Route::has('password.request'))
                    <span>Lupa password? <a href="{{ route('password.request') }}">Reset di sini</a></span>
                @endif
                @if (Route::has('register'))
                    <span>Belum memiliki akun? <a href="{{ route('register') }}">Daftar sekarang</a></span>
                @endif
            </div>
        </div>
    </main>
</div>
</body>
</html>
