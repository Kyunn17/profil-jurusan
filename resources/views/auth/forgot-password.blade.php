<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — SMKN 4 Padalarang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
                <span style="font-size: 0.78rem; color: rgba(255,255,255,0.75);">Pemulihan Akun</span>
            </div>
        </div>
        <div class="auth-brand-body">
            <h1>Atur ulang password Anda.</h1>
            <p>Masukkan alamat email terdaftar dan kami akan mengirimkan tautan pemulihan kata sandi.</p>
        </div>
        <div class="auth-brand-foot">&copy; {{ date('Y') }} SMKN 4 Padalarang</div>
    </aside>

    <main class="auth-form-pane">
        <div class="auth-form-box">
            <h2>Lupa Password</h2>
            <p class="auth-sub">Kami akan mengirimkan tautan reset password ke email Anda.</p>

            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@sekolah.sch.id">
                    <x-input-error :messages="$errors->get('email')" class="error-message" />
                </div>

                <button type="submit" class="btn auth-submit">
                    <i class="fas fa-paper-plane"></i> Kirim Link Reset
                </button>
            </form>

            <div class="auth-footer-links">
                <a href="{{ route('login') }}">← Kembali ke halaman masuk</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
