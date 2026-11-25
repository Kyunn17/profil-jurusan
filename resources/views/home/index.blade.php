<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - PPLG System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .hero-home {
            min-height: 360px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #ffffff 0%, #ecfdf5 55%, #dcfce7 100%);
            border-bottom: 1px solid var(--border-color);
            text-align: center;
            padding: 48px 20px 84px;
        }

        .hero-home h1 {
            font-size: 2.75rem;
            font-weight: 800;
            margin: 14px 0 10px;
            color: var(--text-main);
            letter-spacing: -0.02em;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 22px;
            padding: 0 24px 40px;
            max-width: 1160px;
            margin: -52px auto 0;
            position: relative;
            z-index: 10;
        }

        .menu-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-top: 4px solid var(--primary);
            border-radius: var(--radius-lg);
            padding: 28px 20px;
            text-align: center;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .menu-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary);
            box-shadow: var(--shadow-md);
        }

        .menu-icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            transition: all 0.25s ease;
        }

        .menu-card:hover .menu-icon-wrap {
            background: var(--primary);
            color: #ffffff;
        }

        .menu-title {
            color: var(--text-main);
            font-size: 1.1rem;
            font-weight: 700;
        }

        .menu-desc {
            color: var(--text-muted);
            font-size: 0.88rem;
        }
    </style>
</head>
<body>

<header class="header">
    <a href="{{ route('landing') }}" class="logo">
        <span class="logo-badge"><i class="fas fa-graduation-cap"></i></span>
        <span>PPLG SYSTEM</span>
    </a>

    <nav class="nav-links">
        @auth
            <div class="menu-item"><a href="{{ route('home') }}" class="menu-link active"><i class="fas fa-home"></i> Dashboard</a></div>

            <div class="menu-item dropdown">
                <a href="#" class="menu-link">Master Data ▾</a>
                <div class="dropdown-content">
                    <a href="{{ route('siswa') }}"><i class="fas fa-user-graduate"></i> Data Siswa</a>
                    <a href="{{ route('guru') }}"><i class="fas fa-chalkboard-teacher"></i> Data Guru</a>
                    <a href="{{ route('kelas') }}"><i class="fas fa-school"></i> Data Kelas</a>
                    <a href="{{ route('mapel') }}"><i class="fas fa-book"></i> Mata Pelajaran</a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('pengguna') }}"><i class="fas fa-users-cog"></i> Data Pengguna</a>
                    @endif
                </div>
            </div>

            <div class="menu-item dropdown">
                <a href="#" class="menu-link">Akademik ▾</a>
                <div class="dropdown-content">
                    <a href="{{ route('mapel_diampu') }}"><i class="fas fa-chalkboard"></i> Mapel Diampu</a>
                    @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
                        <a href="{{ route('nilai') }}"><i class="fas fa-star"></i> Input Nilai</a>
                    @else
                        <a href="{{ route('nilai') }}"><i class="fas fa-star"></i> Lihat Nilai</a>
                    @endif
                    <a href="{{ route('absensi') }}"><i class="fas fa-calendar-check"></i> Rekap Absensi</a>
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
            <a href="{{ route('profile.edit') }}" class="profile-link" title="{{ Auth::user()->name }}">
                <span class="profile-pic" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=15803d&color=ffffff&bold=true');"></span>
                <span class="profile-name">{{ Auth::user()->name }}</span>
            </a>
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
    <span class="role-pill">
        <i class="fas fa-school"></i> SMKN 4 Padalarang
    </span>

    @auth
        <h1>Selamat Datang, {{ Auth::user()->name }}!</h1>
        <p style="color: var(--text-secondary); font-size: 1.08rem; margin: 0;">Sistem Informasi Akademik Terpadu SMKN 4 Padalarang.</p>
    @else
        <h1>PPLG SYSTEM</h1>
        <p style="color: var(--text-secondary); font-size: 1.08rem; margin: 0;">Portal Akademik Digital yang Bersih, Cepat, dan Terintegrasi.</p>
        <div style="margin-top: 22px;">
            <a href="{{ route('login') }}" class="btn" style="padding: 12px 28px; font-size: 0.98rem;">
                <i class="fas fa-arrow-right"></i> Mulai Sekarang
            </a>
        </div>
    @endauth
</section>

<div class="menu-grid">
    <a href="{{ route('siswa') }}" class="menu-card">
        <div class="menu-icon-wrap"><i class="fas fa-user-graduate"></i></div>
        <div class="menu-title">Data Siswa</div>
        <div class="menu-desc">Kelola data seluruh siswa aktif</div>
    </a>

    <a href="{{ route('guru') }}" class="menu-card">
        <div class="menu-icon-wrap" style="background: var(--teal-light); color: var(--teal);"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="menu-title">Data Guru</div>
        <div class="menu-desc">Manajemen staf pengajar</div>
    </a>

    <a href="{{ route('nilai') }}" class="menu-card">
        <div class="menu-icon-wrap"><i class="fas fa-star"></i></div>
        <div class="menu-title">Nilai &amp; Rapor</div>
        <div class="menu-desc">Input dan rekapitulasi nilai</div>
    </a>

    <a href="{{ route('mapel') }}" class="menu-card">
        <div class="menu-icon-wrap" style="background: var(--teal-light); color: var(--teal);"><i class="fas fa-book"></i></div>
        <div class="menu-title">Kurikulum</div>
        <div class="menu-desc">Daftar mata pelajaran</div>
    </a>

    <a href="{{ route('absensi') }}" class="menu-card">
        <div class="menu-icon-wrap"><i class="fas fa-calendar-check"></i></div>
        <div class="menu-title">Absensi</div>
        <div class="menu-desc">Monitoring kehadiran harian</div>
    </a>

    <a href="{{ route('berita') }}" class="menu-card">
        <div class="menu-icon-wrap" style="background: var(--teal-light); color: var(--teal);"><i class="fas fa-newspaper"></i></div>
        <div class="menu-title">Portal Berita</div>
        <div class="menu-desc">Informasi dan pengumuman sekolah</div>
    </a>
</div>

<div style="text-align: center; padding: 36px 20px 12px; color: var(--text-muted);">
    <h3 style="margin: 0 0 6px; color: var(--text-main);">Created by XI PPLG B</h3>
    <p style="margin: 0; font-size: 0.92rem;">Dikembangkan menggunakan Laravel 12 untuk SMKN 4 Padalarang.</p>
</div>

<footer class="app-footer">
    <p style="margin: 0;">&copy; {{ date('Y') }} <strong>Khairul Faridz</strong> - SMKN 4 PADALARANG. All Rights Reserved.</p>
</footer>

</body>
</html>
