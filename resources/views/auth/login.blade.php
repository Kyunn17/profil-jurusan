<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PPLG System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">

    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-badge"><i class="fas fa-graduation-cap"></i></span>
            <span>PPLG SYSTEM</span>
        </div>
        <p class="auth-subtitle">Silakan login untuk mengakses sistem akademik</p>

        @if(session('status'))
            <div class="alert-success" style="text-align: left; font-size: 0.85rem;">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error" style="text-align: left; font-size: 0.85rem;">
                <i class="fas fa-exclamation-circle"></i> Email atau Password salah!
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@sekolah.sch.id">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>

            <div class="remember-me">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me" style="margin: 0; font-weight: 500; cursor: pointer;">Ingat Saya</label>
            </div>

            <button type="submit" class="auth-btn">
                <i class="fas fa-sign-in-alt"></i> MASUK SISTEM
            </button>

            <div class="auth-links">
                @if (Route::has('password.request'))
                    <div style="margin-bottom: 8px;">
                        Lupa password? <a href="{{ route('password.request') }}">Reset di sini</a>
                    </div>
                @endif

                @if (Route::has('register'))
                    <div>
                        Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
                    </div>
                @endif
            </div>
        </form>
    </div>

</body>
</html>
