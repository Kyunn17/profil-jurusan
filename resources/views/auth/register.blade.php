<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — SMKN 4 Padalarang</title>
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
            <h1>Buat akun akademik Anda.</h1>
            <p>Siswa dan guru dapat mendaftar untuk mengakses presensi harian, nilai semester, dan informasi sekolah.</p>
        </div>

        <div class="auth-brand-foot">
            &copy; {{ date('Y') }} SMKN 4 Padalarang · XI PPLG B
        </div>
    </aside>

    <main class="auth-form-pane">
        <div class="auth-form-box">
            <h2>Registrasi Akun</h2>
            <p class="auth-sub">Lengkapi data di bawah ini untuk membuat akun baru.</p>

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Terdapat kesalahan pada data yang dimasukkan.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Nama lengkap sesuai data sekolah">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="nama@sekolah.sch.id">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">Daftar Sebagai</label>
                    <select id="role" name="role" required>
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Peran --</option>
                        <option value="siswa" {{ old('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="guru" {{ old('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                    </select>
                    @error('role')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Ulangi password">
                </div>

                <button type="submit" class="btn auth-submit">
                    <i class="fas fa-user-check"></i> Daftar Sekarang
                </button>
            </form>

            <div class="auth-footer-links">
                <span>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></span>
            </div>
        </div>
    </main>
</div>
</body>
</html>
