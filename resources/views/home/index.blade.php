<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMKN 4 Padalarang — Sistem Informasi Akademik PPLG</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header class="landing-nav">
    <a href="{{ route('landing') }}" class="sidebar-brand" style="border-bottom: none; padding: 0; height: auto;">
        <span class="brand-mark">S4</span>
        <div class="brand-meta">
            <strong>SMKN 4 Padalarang</strong>
            <span>Portal Akademik PPLG</span>
        </div>
    </a>

    <div class="page-actions">
        @auth
            <a href="{{ route('home') }}" class="btn btn-sm">
                <i class="fas fa-table-columns"></i> Buka Dashboard
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">Keluar</button>
            </form>
        @else
            <a href="{{ route('register') }}" class="btn btn-outline btn-sm">Daftar</a>
            <a href="{{ route('login') }}" class="btn btn-sm">Masuk</a>
        @endauth
    </div>
</header>

<main class="landing-main">
    <div class="landing-hero-copy">
        <span class="landing-eyebrow">SMKN 4 Padalarang · Jurusan PPLG</span>

        @auth
            <h1>Halo, {{ Auth::user()->name }}.</h1>
            <p>Akses seluruh catatan presensi harian, rekap nilai, jadwal pengajaran, dan pengumuman resmi sekolah dari satu ruang kerja terpadu.</p>
            <div class="page-actions">
                <a href="{{ route('home') }}" class="btn">
                    <i class="fas fa-arrow-right"></i> Buka Dashboard
                </a>
                <a href="{{ route('absensi') }}" class="btn btn-outline">
                    {{ Auth::user()->role === 'siswa' ? 'Absen Hari Ini' : 'Kelola Presensi' }}
                </a>
            </div>
        @else
            <h1>Administrasi akademik sekolah dalam satu ruang kerja.</h1>
            <p>Kelola data siswa, guru, presensi harian mandiri, buku nilai, dan portal berita SMKN 4 Padalarang secara tertib dan transparan.</p>
            <div class="page-actions">
                <a href="{{ route('login') }}" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Masuk ke Sistem
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline">
                    Daftar Akun Baru
                </a>
            </div>
        @endauth
    </div>

    <div class="module-directory">
        <div class="module-directory-head">Modul Akademik Terpadu</div>

        <a href="{{ route('absensi') }}" class="module-row">
            <span class="module-row-icon"><i class="fas fa-calendar-check"></i></span>
            <div>
                <strong>Presensi Harian</strong>
                <small>Check-in mandiri siswa &amp; roll-call kelas oleh guru</small>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>

        <a href="{{ route('nilai') }}" class="module-row">
            <span class="module-row-icon"><i class="fas fa-award"></i></span>
            <div>
                <strong>Nilai &amp; Rapor</strong>
                <small>Rekapitulasi tugas, UTS, dan kalkulasi nilai akhir</small>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>

        <a href="{{ route('siswa') }}" class="module-row">
            <span class="module-row-icon"><i class="fas fa-user-graduate"></i></span>
            <div>
                <strong>Data Siswa</strong>
                <small>Direktori induk siswa aktif dan pembagian kelas</small>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>

        <a href="{{ route('guru') }}" class="module-row">
            <span class="module-row-icon"><i class="fas fa-chalkboard-teacher"></i></span>
            <div>
                <strong>Data Guru</strong>
                <small>Informasi tenaga pendidik dan mata pelajaran diampu</small>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>

        <a href="{{ route('mapel') }}" class="module-row">
            <span class="module-row-icon"><i class="fas fa-book"></i></span>
            <div>
                <strong>Kurikulum &amp; Mapel</strong>
                <small>Daftar mata pelajaran produktif, umum, dan mulok</small>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>

        <a href="{{ route('berita') }}" class="module-row">
            <span class="module-row-icon"><i class="fas fa-newspaper"></i></span>
            <div>
                <strong>Portal Berita</strong>
                <small>Pengumuman resmi dan dokumentasi kegiatan sekolah</small>
            </div>
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</main>

<footer class="app-footer">
    &copy; {{ date('Y') }} Khairul Faridz · XI PPLG B — SMKN 4 Padalarang
</footer>

</body>
</html>
