<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SMKN 4 Padalarang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body>
@php
    $userName = trim((string) Auth::user()->name);
    $nameParts = preg_split('/\s+/', $userName) ?: ['U'];
    $userInitials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
@endphp

<div class="app-shell">
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar(false)"></div>

    <aside id="appSidebar" class="sidebar">
        <a href="{{ route('home') }}" class="sidebar-brand">
            <span class="brand-mark">S4</span>
            <div class="brand-meta">
                <strong>SMKN 4 Padalarang</strong>
                <span>Sistem Akademik PPLG</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Ringkasan</div>
            <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home', 'dashboard') ? 'active' : '' }}">
                <i class="fas fa-table-columns"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('search') }}" class="sidebar-link {{ request()->routeIs('search') ? 'active' : '' }}">
                <i class="fas fa-magnifying-glass"></i>
                <span>Pencarian Data</span>
            </a>

            <div class="sidebar-section-label">Data Master</div>
            <a href="{{ route('siswa') }}" class="sidebar-link {{ request()->routeIs('siswa') ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i>
                <span>Data Siswa</span>
            </a>
            <a href="{{ route('guru') }}" class="sidebar-link {{ request()->routeIs('guru') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Data Guru</span>
            </a>
            <a href="{{ route('kelas') }}" class="sidebar-link {{ request()->routeIs('kelas') ? 'active' : '' }}">
                <i class="fas fa-school"></i>
                <span>Data Kelas</span>
            </a>
            <a href="{{ route('mapel') }}" class="sidebar-link {{ request()->routeIs('mapel') ? 'active' : '' }}">
                <i class="fas fa-book"></i>
                <span>Mata Pelajaran</span>
            </a>
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('pengguna') }}" class="sidebar-link {{ request()->routeIs('pengguna') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Data Pengguna</span>
                </a>
            @endif

            <div class="sidebar-section-label">Akademik</div>
            <a href="{{ route('absensi') }}" class="sidebar-link {{ request()->routeIs('absensi') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>{{ Auth::user()->role === 'siswa' ? 'Absensi Harian' : 'Rekap Absensi' }}</span>
            </a>
            <a href="{{ route('nilai') }}" class="sidebar-link {{ request()->routeIs('nilai') ? 'active' : '' }}">
                <i class="fas fa-award"></i>
                <span>{{ in_array(Auth::user()->role, ['admin', 'guru'], true) ? 'Input Nilai' : 'Lihat Nilai' }}</span>
            </a>
            <a href="{{ route('mapel_diampu') }}" class="sidebar-link {{ request()->routeIs('mapel_diampu') ? 'active' : '' }}">
                <i class="fas fa-diagram-project"></i>
                <span>Mapel Diampu</span>
            </a>

            <div class="sidebar-section-label">Publikasi</div>
            <a href="{{ route('berita') }}" class="sidebar-link {{ request()->routeIs('berita') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i>
                <span>Portal Berita</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <span class="initials">{{ $userInitials }}</span>
                <div class="sidebar-user-meta">
                    <strong title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</strong>
                    <span>{{ Auth::user()->role }}</span>
                </div>
            </div>
            <div class="sidebar-footer-actions">
                <a href="{{ route('profile.edit') }}" class="btn btn-secondary btn-xs" style="flex: 1;">
                    <i class="fas fa-user"></i> Profil
                </a>
                <form method="POST" action="{{ route('logout') }}" style="flex: 1; display: flex;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-xs" style="width: 100%; color: var(--alpha-ink);">
                        <i class="fas fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="app-main">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-menu-btn" onclick="toggleSidebar(true)" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-crumb">
                    <span>Akademik</span>
                    <span>/</span>
                    <strong>@yield('title', 'Dashboard')</strong>
                </div>
            </div>

            <div class="topbar-right">
                <form action="{{ route('search') }}" method="GET" class="topbar-search">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari siswa, guru, mapel...">
                </form>
                <span class="topbar-date mono">{{ now()->format('d M Y') }}</span>
            </div>
        </header>

        <main class="workspace">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <div id="toast" class="toast-banner show">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error" style="display: block;">
                    <div style="font-weight: 700; margin-bottom: 4px;">
                        <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan validasi:
                    </div>
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="app-footer">
            &copy; {{ date('Y') }} SMKN 4 Padalarang — Sistem Informasi Akademik PPLG
        </footer>
    </div>
</div>

<div id="detailModal" class="modal-backdrop">
    <div class="detail-modal-box">
        <div class="detail-modal-head">
            <div class="detail-modal-head-left">
                <span id="detailInitials" class="initials"></span>
                <div>
                    <h3 id="detailTitle"></h3>
                    <small id="detailSubtitle"></small>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span id="detailBadge" class="status-pill pill-neutral" style="display: none;"></span>
                <button type="button" class="detail-close-btn" onclick="closeDetailModal()" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="detail-modal-body">
            <div id="detailImageWrap" style="display: none;"></div>
            <div id="detailBody" class="detail-article-body" style="display: none;"></div>
            <div id="detailMetaGrid" class="detail-meta-grid"></div>
        </div>

        <div class="detail-modal-foot">
            <div style="display: flex; align-items: center; gap: 8px;">
                <a id="detailEditLink" href="#" class="btn btn-outline btn-sm" style="display: none;">
                    <i class="fas fa-pen"></i> Edit
                </a>
                <button id="detailDeleteBtn" type="button" class="btn-ghost-delete" style="display: none; padding: 6px 12px;">
                    <i class="fas fa-trash-can"></i> Hapus
                </button>
            </div>
            <button type="button" onclick="closeDetailModal()" class="btn btn-secondary btn-sm">Tutup</button>
        </div>
    </div>
</div>

<div id="deleteModal" class="modal-backdrop">
    <div class="modal-box">
        <h3>Konfirmasi Hapus</h3>
        <p>Anda yakin ingin menghapus <strong id="deleteTargetName" style="color: var(--ink);"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-actions">
            <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary btn-sm">Batal</button>
            <form id="globalDeleteForm" method="POST" action="" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSidebar(open) {
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (!sidebar || !backdrop) return;
        sidebar.classList.toggle('open', open);
        backdrop.classList.toggle('open', open);
    }

    const formWrapper = document.getElementById('formWrapper');
    const toggleBtn = document.getElementById('toggleFormBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    if (toggleBtn && formWrapper) {
        toggleBtn.onclick = () => {
            formWrapper.style.display = 'block';
            formWrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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
        }, 3200);
    }

    const detailModal = document.getElementById('detailModal');
    const detailInitials = document.getElementById('detailInitials');
    const detailTitle = document.getElementById('detailTitle');
    const detailSubtitle = document.getElementById('detailSubtitle');
    const detailBadge = document.getElementById('detailBadge');
    const detailImageWrap = document.getElementById('detailImageWrap');
    const detailBody = document.getElementById('detailBody');
    const detailMetaGrid = document.getElementById('detailMetaGrid');
    const detailEditLink = document.getElementById('detailEditLink');
    const detailDeleteBtn = document.getElementById('detailDeleteBtn');

    function openDetailModal(el) {
        if (!detailModal || !el || !el.dataset.detail) return;
        let data;
        try {
            data = JSON.parse(el.dataset.detail);
        } catch (e) {
            return;
        }

        if (data.initials) {
            detailInitials.style.display = 'inline-flex';
            detailInitials.className = 'initials ' + (data.initialsClass || '');
            detailInitials.textContent = data.initials;
        } else {
            detailInitials.style.display = 'none';
        }

        detailTitle.textContent = data.title || '';

        if (data.subtitle) {
            detailSubtitle.style.display = 'block';
            detailSubtitle.textContent = data.subtitle;
        } else {
            detailSubtitle.style.display = 'none';
            detailSubtitle.textContent = '';
        }

        detailBadge.replaceChildren();
        if (data.badge) {
            detailBadge.style.display = 'inline-flex';
            detailBadge.className = 'status-pill ' + (data.badgeClass || 'pill-neutral');
            const dot = document.createElement('span');
            dot.className = 'status-dot';
            detailBadge.appendChild(dot);
            detailBadge.appendChild(document.createTextNode(' ' + data.badge));
        } else {
            detailBadge.style.display = 'none';
        }

        detailImageWrap.replaceChildren();
        if (data.image) {
            detailImageWrap.style.display = 'block';
            const img = document.createElement('img');
            img.className = 'detail-cover-img';
            img.src = data.image;
            img.alt = data.title || 'Lampiran';
            detailImageWrap.appendChild(img);
        } else {
            detailImageWrap.style.display = 'none';
        }

        if (data.body) {
            detailBody.style.display = 'block';
            detailBody.textContent = data.body;
        } else {
            detailBody.style.display = 'none';
            detailBody.textContent = '';
        }

        detailMetaGrid.replaceChildren();
        if (Array.isArray(data.fields) && data.fields.length > 0) {
            detailMetaGrid.style.display = 'grid';
            data.fields.forEach(function (field) {
                const box = document.createElement('div');
                box.className = 'detail-meta-item';
                const lbl = document.createElement('span');
                lbl.textContent = field.label || '';
                const val = document.createElement('strong');
                if (field.mono) {
                    val.className = 'mono';
                }
                val.textContent = field.value || '-';
                box.appendChild(lbl);
                box.appendChild(val);
                detailMetaGrid.appendChild(box);
            });
        } else {
            detailMetaGrid.style.display = 'none';
        }

        if (data.editUrl) {
            detailEditLink.style.display = 'inline-flex';
            detailEditLink.href = data.editUrl;
        } else {
            detailEditLink.style.display = 'none';
        }

        if (data.deleteUrl) {
            detailDeleteBtn.style.display = 'inline-flex';
            detailDeleteBtn.dataset.url = data.deleteUrl;
            detailDeleteBtn.dataset.name = data.deleteName || data.title || 'data ini';
            detailDeleteBtn.onclick = function () {
                closeDetailModal();
                openDeleteModal(detailDeleteBtn);
            };
        } else {
            detailDeleteBtn.style.display = 'none';
        }

        detailModal.classList.add('active');
    }

    function closeDetailModal() {
        if (detailModal) {
            detailModal.classList.remove('active');
        }
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
        if (event.target === detailModal) {
            closeDetailModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeDetailModal();
            closeDeleteModal();
        }
    });
</script>
@stack('scripts')
</body>
</html>
