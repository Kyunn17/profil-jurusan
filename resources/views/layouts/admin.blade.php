<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - PPLG FUTURISTIC</title>

    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body>

<header class="header">
    <div class="logo">
        <i class="fas fa-globe-asia @yield('logo_spin')"></i> PPLG FUTURISTIC
    </div>

    <nav class="nav-links">
        <div class="menu-item">
            <a href="{{ route('home') }}" class="menu-link {{ request()->routeIs('home', 'dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>

        <div class="menu-item dropdown">
            <a href="#" class="menu-link {{ request()->routeIs('siswa', 'guru', 'kelas', 'mapel', 'pengguna') ? 'active' : '' }}">Master Data ▾</a>
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
            <a href="#" class="menu-link {{ request()->routeIs('mapel_diampu', 'nilai', 'absensi') ? 'active' : '' }}">Akademik ▾</a>
            <div class="dropdown-content">
                <a href="{{ route('mapel_diampu') }}"><i class="fas fa-chalkboard"></i> Mapel Diampu</a>
                @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
                    <a href="{{ route('nilai') }}"><i class="fas fa-star"></i> Input Nilai</a>
                    <a href="{{ route('absensi') }}"><i class="fas fa-calendar-check"></i> Rekap Absensi</a>
                @else
                    <a href="{{ route('nilai') }}"><i class="fas fa-star"></i> Lihat Nilai</a>
                    <a href="{{ route('absensi') }}"><i class="fas fa-calendar-check"></i> Rekap Absensi</a>
                @endif
            </div>
        </div>

        <div class="menu-item">
            <a href="{{ route('berita') }}" class="menu-link {{ request()->routeIs('berita') ? 'active' : '' }}">Berita</a>
        </div>
        <div class="menu-item">
            <a href="{{ route('search') }}" class="menu-link {{ request()->routeIs('search') ? 'active' : '' }}">Pencarian</a>
        </div>
    </nav>

    <div class="user-actions">
        <div class="profile-pic" style="background-image: url('https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3b82f6&color=fff&bold=true');" title="{{ Auth::user()->name }}"></div>

        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-power-off"></i> Logout
            </button>
        </form>
    </div>
</header>

@yield('before_container')

@hasSection('raw_content')
    @yield('raw_content')
@else
    <div class="dashboard-container" @yield('container_attrs')>
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            <div id="toast" class="custom-toast show">
                <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
                <div class="toast-msg">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-success" style="background: rgba(239, 68, 68, 0.1); border-color: var(--danger); color: var(--danger);">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-success" style="background: rgba(239, 68, 68, 0.1); border-color: var(--danger); color: var(--danger); display: block;">
                <div style="font-weight: bold; margin-bottom: 6px;">
                    <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan validasi:
                </div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
@endif

<footer style="text-align: center; padding: 40px; color: #64748b; font-size: 14px; border-top: 1px solid #1e293b; margin-top: 40px;">
    <p>&copy; {{ date('Y') }} <strong>PPLG FUTURISTIC SYSTEM</strong>. All Rights Reserved.</p>
</footer>

<div id="deleteModal" class="custom-modal-overlay">
    <div class="custom-modal-box">
        <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <h3 class="modal-title">Hapus Data?</h3>
        <p class="modal-desc">Anda yakin ingin menghapus <strong id="deleteTargetName" style="color: var(--primary);"></strong>? Data tidak bisa dikembalikan.</p>
        <div class="modal-actions">
            <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
            <form id="globalDeleteForm" method="POST" action="" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn" style="background-color: var(--danger); color: white;">Ya, Hapus!</button>
            </form>
        </div>
    </div>
</div>

<script>
    const formWrapper = document.getElementById('formWrapper');
    const toggleBtn = document.getElementById('toggleFormBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    if (toggleBtn && formWrapper) {
        toggleBtn.onclick = () => {
            formWrapper.style.display = 'block';
            formWrapper.scrollIntoView({ behavior: 'smooth' });
        };
    }

    if (cancelBtn && formWrapper) {
        cancelBtn.onclick = () => {
            formWrapper.style.display = 'none';
        };
    }

    const toast = document.getElementById('toast');
    if (toast) {
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    const deleteModal = document.getElementById('deleteModal');
    const globalDeleteForm = document.getElementById('globalDeleteForm');
    const deleteTargetName = document.getElementById('deleteTargetName');

    function openDeleteModal(button) {
        if (!deleteModal || !globalDeleteForm || !deleteTargetName) return;
        deleteTargetName.textContent = button.dataset.name || 'data ini';
        globalDeleteForm.action = button.dataset.url;
        deleteModal.classList.add('active');
    }

    function closeDeleteModal() {
        if (deleteModal) {
            deleteModal.classList.remove('active');
        }
    }

    window.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });
</script>
@stack('scripts')
</body>
</html>
