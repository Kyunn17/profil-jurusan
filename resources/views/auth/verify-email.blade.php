<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - PPLG FUTURISTIC</title>
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.95)), url('{{ asset("assets/bg.jpg") }}'); background-size: cover; background-position: center; font-family: 'Segoe UI', sans-serif; }
        .login-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); border-top: 4px solid var(--primary); padding: 40px; border-radius: 16px; width: 100%; max-width: 450px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5); text-align: center; animation: fadeIn 0.8s ease; }
        .login-logo { font-size: 1.8rem; font-weight: 800; color: var(--primary); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        .login-subtitle { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 30px; line-height: 1.6; }
        .btn-login { width: 100%; padding: 12px; background: var(--primary); color: #0f172a; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; font-size: 1rem; margin-top: 10px; }
        .btn-login:hover { background: #0ea5e9; transform: scale(1.02); box-shadow: 0 0 15px rgba(56, 189, 248, 0.4); }
        .btn-logout { background: transparent; color: #94a3b8; border: 1px solid #334155; width: 100%; padding: 10px; border-radius: 8px; margin-top: 15px; cursor: pointer; transition: 0.3s; font-weight: bold; }
        .btn-logout:hover { border-color: var(--primary); color: var(--primary); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo" style="color: #10b981; border-color: #10b981;"><i class="fas fa-envelope-open-text"></i> CEK EMAIL</div>
        <p class="login-subtitle">
            Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div style="background: rgba(16, 185, 129, 0.1); color: #34d399; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; border: 1px solid #10b981;">
                <i class="fas fa-check-circle"></i> Tautan verifikasi baru telah dikirim!
            </div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-login" style="background: #10b981;">
                    <i class="fas fa-paper-plane"></i> KIRIM ULANG EMAIL
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">LOGOUT</button>
            </form>
        </div>
    </div>
</body>
</html>