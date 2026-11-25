<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - PPLG System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-badge"><i class="fas fa-key"></i></span>
            <span>PASSWORD BARU</span>
        </div>
        <p class="auth-subtitle">Silakan buat password baru Anda.</p>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required readonly>
                <x-input-error :messages="$errors->get('email')" class="error-message" />
            </div>

            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="password" required autocomplete="new-password" placeholder="Password baru">
                <x-input-error :messages="$errors->get('password')" class="error-message" />
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required placeholder="Ulangi password baru">
                <x-input-error :messages="$errors->get('password_confirmation')" class="error-message" />
            </div>

            <button type="submit" class="auth-btn"><i class="fas fa-save"></i> SIMPAN PASSWORD</button>
        </form>
    </div>
</body>
</html>
