<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi - PPLG System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-card" style="border-top-color: var(--danger);">
        <div class="auth-logo" style="color: var(--danger);">
            <span class="logo-badge" style="background: var(--danger-bg); color: var(--danger);"><i class="fas fa-shield-alt"></i></span>
            <span>KONFIRMASI</span>
        </div>
        <p class="auth-subtitle">Ini area aman. Harap konfirmasi password Anda sebelum melanjutkan.</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password Anda">
                <x-input-error :messages="$errors->get('password')" class="error-message" />
            </div>

            <button type="submit" class="auth-btn" style="background: var(--danger);"><i class="fas fa-check-circle"></i> KONFIRMASI</button>
        </form>
    </div>
</body>
</html>
