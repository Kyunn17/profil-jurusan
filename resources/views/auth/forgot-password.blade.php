<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - PPLG System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-badge"><i class="fas fa-lock-open"></i></span>
            <span>RESET PASS</span>
        </div>
        <p class="auth-subtitle">Lupa password? Masukkan email Anda, kami akan kirimkan link reset.</p>

        @if(session('status'))
            <div class="alert-success" style="text-align: left; font-size: 0.85rem;">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="email@sekolah.sch.id">
                <x-input-error :messages="$errors->get('email')" class="error-message" />
            </div>

            <button type="submit" class="auth-btn"><i class="fas fa-paper-plane"></i> KIRIM LINK RESET</button>

            <div class="auth-links">
                <a href="{{ route('login') }}">← Kembali ke Login</a>
            </div>
        </form>
    </div>
</body>
</html>
