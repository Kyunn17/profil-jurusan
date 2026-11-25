<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Password — SMKN 4 Padalarang</title>
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
                <span style="font-size: 0.78rem; color: rgba(255,255,255,0.75);">Keamanan Akun</span>
            </div>
        </div>
        <div class="auth-brand-body">
            <h1>Konfirmasi identitas Anda.</h1>
            <p>Area ini memerlukan verifikasi kata sandi sebelum melakukan perubahan sensitif.</p>
        </div>
        <div class="auth-brand-foot">&copy; {{ date('Y') }} SMKN 4 Padalarang</div>
    </aside>

    <main class="auth-form-pane">
        <div class="auth-form-box">
            <h2>Konfirmasi Password</h2>
            <p class="auth-sub">Harap masukkan password Anda sebelum melanjutkan.</p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password Anda">
                    <x-input-error :messages="$errors->get('password')" class="error-message" />
                </div>

                <button type="submit" class="btn btn-danger auth-submit">
                    <i class="fas fa-check-circle"></i> Konfirmasi
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
