<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - PPLG FUTURISTIC</title>

    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .hero-home {
            height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 1)),
                        url('{{ asset("assets/bg.jpg") }}');
            background-size: cover;
            background-position: center;
            border-bottom: 2px solid var(--primary);
            text-align: center;
            padding: 20px;
        }

        .hero-home h1 {
            font-size: 3.5rem;
            margin-bottom: 10px;
            text-shadow: 0 0 20px rgba(56, 189, 248, 0.6);
            animation: fadeIn 1s ease-out;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            padding: 40px;
            max-width: 1200px;
            margin: -60px auto 0;
            position: relative;
            z-index: 10;
        }

        .menu-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .menu-card:hover {
            transform: translateY(-10px);
            background: var(--bg-card);
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.2);
        }

        .menu-icon {
            font-size: 3rem;
            color: var(--primary);
            transition: 0.3s;
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.1);
            color: #fff;
            text-shadow: 0 0 10px var(--primary);
        }

        .menu-title {
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .menu-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo"><i class="fas fa-globe-asia"></i> PPLG FUTURISTIC</div>

    <nav class="nav-links">
        @auth
            <div class="menu-item"><a href="{{ route('home') }}" class="menu-link active">Dashboard</a></div>

            <div class="menu-item dropdown">
                <a href="#" class="menu-link">Master Data ▾</a>
                <div class="dropdown-content">
                    <a href="{{ route('siswa') }}">Data Siswa</a>
                    <a href="{{ route('guru') }}">Data Guru</a>
                    <a href="{{ route('kelas') }}">Data Kelas</a>
                    <a href="{{ route('mapel') }}">Mata Pelajaran</a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('pengguna') }}">Data Pengguna</a>
                    @endif
                </div>
            </div>

            <div class="menu-item dropdown">
                <a href="#" class="menu-link">Akademik ▾</a>
                <div class="dropdown-content">
                    <a href="{{ route('mapel_diampu') }}">Mapel Diampu</a>
                    @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
                        <a href="{{ route('nilai') }}">Input Nilai</a>
                    @else
                        <a href="{{ route('nilai') }}">Lihat Nilai</a>
                    @endif
                    <a href="{{ route('absensi') }}">Rekap Absensi</a>
                </div>
            </div>

            <div class="menu-item"><a href="{{ route('berita') }}" class="menu-link">Berita</a></div>
            <div class="menu-item"><a href="{{ route('search') }}" class="menu-link">Pencarian</a></div>
        @else
            <div class="menu-item"><a href="{{ route('landing') }}" class="menu-link active">Home</a></div>
        @endauth
    </nav>

    <div class="user-actions">
        @auth
            <div class="profile-pic" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random'); background-size: cover;" title="{{ Auth::user()->name }}"></div>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login Area
            </a>
        @endauth
    </div>
</header>

<section class="hero-home">
    @auth
        <h1>Selamat Datang, {{ Auth::user()->name }}!</h1>
        <p style="color: #cbd5e1; font-size: 1.2rem;">Sistem Informasi Akademik Terpadu SMKN 4 Padalarang.</p>
    @else
        <h1>PPLG FUTURISTIC</h1>
        <p style="color: #cbd5e1; font-size: 1.2rem;">Masa Depan Pendidikan Digital Dimulai Di Sini.</p>
        <div style="margin-top: 20px;">
            <a href="{{ route('login') }}" class="btn" style="padding: 12px 30px; font-size: 1rem;">Mulai Sekarang</a>
        </div>
    @endauth
</section>

<div class="menu-grid">
    <a href="{{ route('siswa') }}" class="menu-card">
        <i class="fas fa-user-graduate menu-icon"></i>
        <div class="menu-title">Data Siswa</div>
        <div class="menu-desc">Kelola data seluruh siswa aktif</div>
    </a>

    <a href="{{ route('guru') }}" class="menu-card">
        <i class="fas fa-chalkboard-teacher menu-icon" style="color: #a78bfa;"></i>
        <div class="menu-title">Data Guru</div>
        <div class="menu-desc">Manajemen staf pengajar</div>
    </a>

    <a href="{{ route('nilai') }}" class="menu-card">
        <i class="fas fa-star menu-icon" style="color: #fbbf24;"></i>
        <div class="menu-title">Nilai &amp; Rapor</div>
        <div class="menu-desc">Input dan rekapitulasi nilai</div>
    </a>

    <a href="{{ route('mapel') }}" class="menu-card">
        <i class="fas fa-book menu-icon" style="color: #34d399;"></i>
        <div class="menu-title">Kurikulum</div>
        <div class="menu-desc">Daftar mata pelajaran</div>
    </a>

    <a href="{{ route('absensi') }}" class="menu-card">
        <i class="fas fa-calendar-check menu-icon" style="color: #f472b6;"></i>
        <div class="menu-title">Absensi</div>
        <div class="menu-desc">Monitoring kehadiran harian</div>
    </a>

    <a href="{{ route('berita') }}" class="menu-card">
        <i class="fas fa-newspaper menu-icon" style="color: #ef4444;"></i>
        <div class="menu-title">Portal Berita</div>
        <div class="menu-desc">Informasi dan pengumuman sekolah</div>
    </a>
</div>

<div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
    <h3>Created by XI PPLG B</h3>
    <p>Dikembangkan dengan ❤️ menggunakan Laravel &amp; Teknologi Masa Depan.</p>
</div>

<footer style="text-align: center; padding: 30px; background: #0f172a; border-top: 1px solid #1e293b; color: #64748b; font-size: 14px;">
    <p>&copy; {{ date('Y') }} <strong>Khairul Faridz</strong> - SMKN 4 PADALARANG. All Rights Reserved.</p>
</footer>

</body>
</html>
