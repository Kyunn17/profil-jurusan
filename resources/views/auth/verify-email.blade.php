<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email — SMKN 4 Padalarang</title>
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
                <span style="font-size: 0.78rem; color: rgba(255,255,255,0.75);">Verifikasi Email</span>
            </div>
        </div>
        <div class="auth-brand-body">
            <h1>Verifikasi alamat email Anda.</h1>
            <p>Klik tautan verifikasi yang baru saja kami kirimkan ke inbox Anda untuk mulai menggunakan portal akademik.</p>
        </div>
        <div class="auth-brand-foot">&copy; {{ date('Y') }} SMKN 4 Padalarang</div>
    </aside>

    <main class="auth-form-pane">
        <div class="auth-form-box">
            <h2>Cek Email Anda</h2>
            <p class="auth-sub">Terima kasih telah mendaftar. Sebelum melanjutkan, verifikasi alamat email Anda melalui tautan yang kami kirimkan.</p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>Tautan verifikasi baru telah dikirim!</span>
                </div>
            @endif

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn auth-submit">
                        <i class="fas fa-paper-plane"></i> Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline auth-submit">Keluar</button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
