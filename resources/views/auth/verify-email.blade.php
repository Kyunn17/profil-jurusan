<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - PPLG System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-card" style="max-width: 450px;">
        <div class="auth-logo">
            <span class="logo-badge"><i class="fas fa-envelope-open-text"></i></span>
            <span>CEK EMAIL</span>
        </div>
        <p class="auth-subtitle">
            Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert-success" style="text-align: left; font-size: 0.85rem;">
                <i class="fas fa-check-circle"></i> Tautan verifikasi baru telah dikirim!
            </div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="auth-btn">
                    <i class="fas fa-paper-plane"></i> KIRIM ULANG EMAIL
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="auth-btn-secondary">LOGOUT</button>
            </form>
        </div>
    </div>
</body>
</html>
