<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — SMKN 4 Padalarang</title>
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
            <h1>Buat kata sandi baru.</h1>
            <p>Pastikan kata sandi baru mudah diingat dan minimal terdiri dari 8 karakter.</p>
        </div>
        <div class="auth-brand-foot">&copy; {{ date('Y') }} SMKN 4 Padalarang</div>
    </aside>

    <main class="auth-form-pane">
        <div class="auth-form-box">
            <h2>Password Baru</h2>
            <p class="auth-sub">Silakan masukkan password baru untuk akun Anda.</p>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label>Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required readonly>
                    <x-input-error :messages="$errors->get('email')" class="error-message" />
                </div>

                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    <x-input-error :messages="$errors->get('password')" class="error-message" />
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi password baru">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="error-message" />
                </div>

                <button type="submit" class="btn auth-submit">
                    <i class="fas fa-save"></i> Simpan Password
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
