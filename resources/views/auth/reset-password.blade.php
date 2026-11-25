<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - PPLG FUTURISTIC</title>
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.95)), url('{{ asset("assets/bg.jpg") }}'); background-size: cover; background-position: center; font-family: 'Segoe UI', sans-serif; }
        .login-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); border-top: 4px solid var(--primary); padding: 40px; border-radius: 16px; width: 100%; max-width: 400px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5); text-align: center; animation: fadeIn 0.8s ease; }
        .login-logo { font-size: 1.8rem; font-weight: 800; color: var(--primary); margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        .login-subtitle { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; color: var(--primary); font-size: 0.85rem; margin-bottom: 8px; font-weight: 600; }
        .form-input { width: 100%; padding: 12px 15px; background: rgba(15, 23, 42, 0.8); border: 1px solid #334155; border-radius: 8px; color: white; outline: none; transition: 0.3s; }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 10px rgba(56, 189, 248, 0.3); }
        .btn-login { width: 100%; padding: 12px; background: var(--primary); color: #0f172a; font-weight: bold; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; font-size: 1rem; margin-top: 10px; }
        .btn-login:hover { background: #0ea5e9; transform: scale(1.02); box-shadow: 0 0 15px rgba(56, 189, 248, 0.4); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo"><i class="fas fa-key"></i> PASSWORD BARU</div>
        <p class="login-subtitle">Silakan buat password baru Anda.</p>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label>Email Address</label>
                <input class="form-input" type="email" name="email" value="{{ old('email', $request->email) }}" required readonly style="opacity: 0.7;">
                <x-input-error :messages="$errors->get('email')" class="mt-2" style="color: #ef4444;" />
            </div>

            <div class="form-group">
                <label>Password Baru</label>
                <input class="form-input" type="password" name="password" required autocomplete="new-password" placeholder="Password baru">
                <x-input-error :messages="$errors->get('password')" class="mt-2" style="color: #ef4444;" />
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input class="form-input" type="password" name="password_confirmation" required placeholder="Ulangi password baru">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" style="color: #ef4444;" />
            </div>

            <button type="submit" class="btn-login"><i class="fas fa-save"></i> SIMPAN PASSWORD</button>
        </form>
    </div>
</body>
</html>