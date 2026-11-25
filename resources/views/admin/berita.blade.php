@extends('layouts.admin')

@section('title', 'Portal Berita')

@section('before_container')
<div class="breaking-news-bar">
    <div class="bn-label"><i class="fas fa-bolt"></i> BREAKING NEWS</div>
    <div class="bn-content">
        @foreach($beritaPublished as $b)
            <span class="bn-item">{{ $b->judul }}</span>
        @endforeach
        <span class="bn-item">Selamat Datang di Portal Berita Resmi SMKN 4 Padalarang!</span>
    </div>
</div>
@endsection

@section('content')
<div class="page-header-actions">
    <div>
        <h2 class="page-title">Portal Berita</h2>
        <p class="page-subtitle">Informasi terkini seputar kegiatan sekolah.</p>
    </div>
    <div class="header-btn-group">
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('export.preview', ['type' => 'berita']) }}" class="btn btn-outline">
            <i class="fas fa-file-csv"></i> Export
        </a>
        @endif
        <button id="toggleFormBtn" class="btn">
            <i class="fas fa-pen-nib"></i> Tulis Berita
        </button>
    </div>
</div>

@if(request('cari'))
<div class="alert-filter">
    <span><i class="fas fa-filter" style="color: var(--primary);"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('berita') }}" class="btn btn-secondary btn-sm">Reset Pencarian</a>
</div>
@endif

<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-newspaper"></i> {{ $edit ? 'Edit Berita' : 'Tulis Berita Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('berita.update') : route('berita.store') }}" enctype="multipart/form-data">
        @csrf
        @if($edit) <input type="hidden" name="id_berita" value="{{ $edit->id_berita }}"> @endif

        <div class="form-group">
            <label>Judul Berita</label>
            <input type="text" name="judul" value="{{ old('judul', $edit->judul ?? '') }}" placeholder="Judul yang menarik..." required>
        </div>

        <div class="form-group">
            <label>Foto Sampul</label>
            <input type="file" name="foto" {{ $edit ? '' : 'required' }}>
        </div>

        <div class="form-group">
            <label>Isi Berita</label>
            <textarea name="isi" rows="6" placeholder="Tulis detail berita..." required>{{ old('isi', $edit->isi ?? '') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn">{{ Auth::user()->role === 'admin' ? 'Terbitkan' : 'Kirim Moderasi' }}</button>
        </div>
    </form>
</div>

@if(Auth::user()->role === 'admin' && count($beritaPending) > 0)
<div class="admin-notif-area">
    <h3 style="color: var(--warning-text); margin: 0 0 14px; font-size: 1.05rem;">
        <i class="fas fa-bell"></i> Menunggu Konfirmasi ({{ count($beritaPending) }})
    </h3>
    @foreach($beritaPending as $bp)
    <div class="notif-item">
        <div style="display: flex; gap: 14px; align-items: center;">
            @if($bp->foto)
                <img src="data:image/jpeg;base64,{{ base64_encode($bp->foto) }}" style="width: 64px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);" alt="{{ $bp->judul }}">
            @else
                <div style="width: 64px; height: 44px; background: var(--bg-subtle); border-radius: 6px; border: 1px solid var(--border-color);"></div>
            @endif
            <div>
                <strong style="color: var(--text-main); font-size: 0.98rem;">{{ $bp->judul }}</strong>
                <div style="font-size: 0.82rem; color: var(--text-muted);">
                    Oleh: <span style="color: var(--primary); font-weight: 600;">{{ $bp->user?->name ?? 'User Terhapus' }}</span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <form method="POST" action="{{ route('berita.approve', $bp->id_berita) }}" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm"><i class="fas fa-check"></i> ACC</button>
            </form>
            <button type="button"
                    data-url="{{ route('berita.hapus', $bp->id_berita) }}"
                    data-name="Berita '{{ $bp->judul }}'"
                    onclick="openDeleteModal(this)"
                    class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak</button>
        </div>
    </div>
    @endforeach
</div>
@endif

<h3 style="color: var(--text-main); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; font-size: 1.2rem;">
    <i class="fas fa-rss" style="color: var(--primary);"></i> Berita Terbaru
</h3>

<div class="news-grid">
    @forelse($beritaPublished as $b)
    <div class="news-card">
        <div style="position: relative;">
            @if($b->foto)
                <img src="data:image/jpeg;base64,{{ base64_encode($b->foto) }}" class="news-img" alt="{{ $b->judul }}">
            @else
                <div class="news-img" style="display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                    <i class="fas fa-image fa-2x"></i>
                </div>
            @endif

            @if(Auth::id() === $b->id_user || Auth::user()->role === 'admin')
            <div style="position: absolute; top: 10px; right: 10px; display: flex; gap: 6px;">
                <a href="{{ route('berita', ['edit' => $b->id_berita]) }}" class="btn-icon btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                <button type="button"
                        data-url="{{ route('berita.hapus', $b->id_berita) }}"
                        data-name="{{ $b->judul }}"
                        onclick="openDeleteModal(this)"
                        class="btn-icon btn-delete" title="Hapus"><i class="fas fa-trash"></i></button>
            </div>
            @endif
        </div>

        <div class="news-body">
            <div class="news-date">
                <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($b->tanggal_post)->format('d M Y') }}
            </div>
            <h3 class="news-title">{{ Str::limit($b->judul, 50) }}</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0 0 18px; flex-grow: 1;">{{ Str::limit(strip_tags($b->isi), 90) }}</p>
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid var(--border-color); font-size: 0.82rem; color: var(--text-secondary);">
                <span><i class="fas fa-user" style="color: var(--primary);"></i> {{ $b->user?->name ?? 'User Terhapus' }}</span>
                <span class="status-badge status-hadir">{{ $b->user?->role ?? '-' }}</span>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-newspaper"></i>
        <strong>Belum ada berita diterbitkan.</strong>
        <p style="margin: 6px 0 0;">Berita dan pengumuman sekolah akan tampil di sini.</p>
    </div>
    @endforelse
</div>

@if(Auth::user()->role !== 'admin' && count($myBerita) > 0)
<div style="margin-top: 44px;">
    <h3 style="color: var(--text-main); font-size: 1.15rem;"><i class="fas fa-folder-open" style="color: var(--primary);"></i> Riwayat Upload Saya</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($myBerita as $mb)
                <tr>
                    <td style="font-weight: 600; color: var(--text-main);">{{ $mb->judul }}</td>
                    <td>{{ \Carbon\Carbon::parse($mb->tanggal_post)->format('d M Y') }}</td>
                    <td>
                        @if($mb->status === 'published') <span class="status-badge st-published">Published</span>
                        @elseif($mb->status === 'pending') <span class="status-badge st-pending">Menunggu ACC</span>
                        @else <span class="status-badge st-rejected">Ditolak</span> @endif
                    </td>
                    <td>
                        <a href="{{ route('berita', ['edit' => $mb->id_berita]) }}" style="color: var(--primary); font-weight: 600; margin-right: 12px;">Edit</a>
                        <button type="button"
                                data-url="{{ route('berita.hapus', $mb->id_berita) }}"
                                data-name="{{ $mb->judul }}"
                                onclick="openDeleteModal(this)"
                                style="background: none; border: none; color: var(--danger); font-weight: 600; cursor: pointer; padding: 0; font-family: inherit;">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
