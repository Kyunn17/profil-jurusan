<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - PPLG FUTURISTIC</title>
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.95)), url('{{ asset("assets/bg.jpg") }}'); background-size: cover; background-position: center; font-family: 'Segoe UI', sans-serif; }
        .login-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); border-top: 4px solid var(--primary); padding: 40px; border-radius: 16px; width: 100%; max-width: 400px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5); text-align: center; animation: fadeIn 0.8s ease; }
        .login-logo { font-size: 1.8rem; font-weight: 800; color: var(--primary); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        .login-subtitle { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 30px; line-height: 1.5; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; color: var(--primary); font-size: 0.85rem; margin-bottom: 8px; font-weight: 600; }
        .form-input { width: 100%; padding: 12px 15px; background: rgba(15, 23, 42, 0.8); border: 1px solid #334155; border-radius: 8px; color: white; outline: none; transition: 0.3s; }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 10px rgba(56, 189, 248, 0.3); }
        .btn-login { width: 100%; padding: 12px; background: var(--primary); color: #0f172a; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; font-size: 1rem; margin-top: 10px; }
        .btn-login:hover { background: #0ea5e9; transform: scale(1.02); box-shadow: 0 0 15px rgba(56, 189, 248, 0.4); }
        .auth-links { margin-top: 25px; border-top: 1px solid #334155; padding-top: 20px; font-size: 0.85rem; color: var(--text-muted); }
        .auth-links a { color: var(--primary); text-decoration: none; font-weight: 600; transition: 0.3s; }
        .auth-links a:hover { color: var(--secondary); text-shadow: 0 0 5px rgba(236, 72, 153, 0.5); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo"><i class="fas fa-lock-open"></i> RESET PASS</div>
        <p class="login-subtitle">Lupa password? Masukkan email Anda, kami akan kirimkan link reset.</p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="email@sekolah.sch.id">
                <x-input-error :messages="$errors->get('email')" class="mt-2" style="color: #ef4444; font-size: 0.8rem;" />
            </div>

            <button type="submit" class="btn-login"><i class="fas fa-paper-plane"></i> KIRIM LINK RESET</button>

            <div class="auth-links">
                <a href="{{ route('login') }}">← Kembali ke Login</a>
            </div>
        </form>
    </div>
</body>
</html>