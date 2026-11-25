@extends('layouts.admin')

@section('title', 'Portal Berita')

@push('styles')
<style>
    .status-badge { padding: 3px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; min-width: auto; }
    .st-pending { background: #f59e0b; color: #000; }
    .st-published { background: #10b981; color: #000; }
    .st-rejected { background: #ef4444; color: #fff; }

    .admin-notif-area {
        background: rgba(245, 158, 11, 0.1); border: 1px solid #f59e0b;
        border-radius: 12px; padding: 20px; margin-bottom: 30px;
    }
    .notif-item {
        display: flex; justify-content: space-between; align-items: center;
        background: var(--bg-card); padding: 15px; margin-bottom: 10px;
        border-radius: 8px; border: 1px solid #334155;
    }
</style>
@endpush

@section('before_container')
<div class="breaking-news-bar">
    <div class="bn-label">⚡ BREAKING NEWS</div>
    <div class="bn-content">
        @foreach($beritaPublished as $b)
            <span class="bn-item">📢 {{ $b->judul }}</span>
        @endforeach
        <span class="bn-item">🚀 Selamat Datang di Portal Berita Resmi SMK PPLG Futuristic!</span>
    </div>
</div>
@endsection

@section('content')
<div class="page-header-actions">
    <div>
        <h2 style="color: var(--text-main); margin: 0; font-size: 2rem;">Portal Berita</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0;">Informasi terkini seputar kegiatan sekolah.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('export.preview', ['type' => 'berita']) }}" class="btn" style="background: var(--success);">
            <i class="fas fa-file-csv"></i> Export
        </a>
        @endif
        <button id="toggleFormBtn" class="btn" style="background: var(--danger);">
            <i class="fas fa-pen-nib"></i> Tulis Berita
        </button>
    </div>
</div>

@if(request('cari'))
<div class="alert-success" style="justify-content: space-between; border-color: var(--danger); color: var(--danger); background: rgba(239, 68, 68, 0.1);">
    <span><i class="fas fa-filter"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('berita') }}" class="btn btn-secondary" style="padding: 5px 12px; font-size: 12px;">Reset Pencarian</a>
</div>
@endif

<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }}; border-color: var(--danger);">
    <h3 style="color: white; border-bottom: 1px solid #334155; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="fas fa-newspaper"></i> {{ $edit ? 'Edit Berita' : 'Tulis Berita Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('berita.update') : route('berita.store') }}" enctype="multipart/form-data">
        @csrf
        @if($edit) <input type="hidden" name="id_berita" value="{{ $edit->id_berita }}"> @endif

        <div class="form-group">
            <label style="color: var(--danger);">Judul Berita</label>
            <input type="text" name="judul" value="{{ old('judul', $edit->judul ?? '') }}" placeholder="Judul yang menarik..." required>
        </div>

        <div class="form-group">
            <label style="color: var(--danger);">Foto Sampul</label>
            <input type="file" name="foto" {{ $edit ? '' : 'required' }}>
        </div>

        <div class="form-group">
            <label style="color: var(--danger);">Isi Berita</label>
            <textarea name="isi" rows="6" placeholder="Tulis detail berita..." required>{{ old('isi', $edit->isi ?? '') }}</textarea>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn" style="background: var(--danger);">{{ Auth::user()->role === 'admin' ? 'Terbitkan' : 'Kirim Moderasi' }}</button>
        </div>
    </form>
</div>

@if(Auth::user()->role === 'admin' && count($beritaPending) > 0)
<div class="admin-notif-area">
    <h3 style="color: #f59e0b; margin-top: 0;"><i class="fas fa-bell"></i> Menunggu Konfirmasi ({{ count($beritaPending) }})</h3>
    @foreach($beritaPending as $bp)
    <div class="notif-item">
        <div style="display: flex; gap: 15px; align-items: center;">
            @if($bp->foto)
                <img src="data:image/jpeg;base64,{{ base64_encode($bp->foto) }}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
            @else
                <div style="width: 60px; height: 40px; background: #333; border-radius: 4px;"></div>
            @endif
            <div>
                <strong style="color: white; font-size: 1rem;">{{ $bp->judul }}</strong>
                <div style="font-size: 0.8rem; color: #94a3b8;">
                    Oleh: <span style="color: var(--primary);">{{ $bp->user?->name ?? 'User Terhapus' }}</span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <form method="POST" action="{{ route('berita.approve', $bp->id_berita) }}" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm" style="background: var(--success);">✔ ACC</button>
            </form>
            <button type="button"
                    data-url="{{ route('berita.hapus', $bp->id_berita) }}"
                    data-name="Berita '{{ $bp->judul }}'"
                    onclick="openDeleteModal(this)"
                    class="btn btn-sm" style="background: var(--danger); color: white;">✖ Tolak</button>
        </div>
    </div>
    @endforeach
</div>
@endif

<h3 style="color: white; border-bottom: 2px solid #334155; padding-bottom: 10px;"><i class="fas fa-rss"></i> Berita Terbaru</h3>
<div class="news-grid">
    @foreach($beritaPublished as $b)
    <div class="news-card">
        <div class="news-img-wrapper" style="position: relative;">
            @if($b->foto)
                <img src="data:image/jpeg;base64,{{ base64_encode($b->foto) }}" class="news-img" style="object-fit: cover; width: 100%; height: 200px;">
            @else
                <img src="https://via.placeholder.com/400?text=No+Image" class="news-img" style="object-fit: cover; width: 100%; height: 200px;">
            @endif

            @if(Auth::id() === $b->id_user || Auth::user()->role === 'admin')
            <div class="news-actions" style="position: absolute; top: 10px; right: 10px; display: flex; gap: 6px;">
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
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px; flex-grow: 1;">{{ Str::limit(strip_tags($b->isi), 80) }}</p>
            <div class="news-author" style="display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-user"></i> {{ $b->user?->name ?? 'User Terhapus' }}</span>
                <span style="font-size: 0.7rem; background: #334155; padding: 2px 6px; border-radius: 4px;">{{ $b->user?->role ?? '-' }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(Auth::user()->role !== 'admin' && count($myBerita) > 0)
<div style="margin-top: 50px;">
    <h3 style="color: var(--text-muted);">📂 Riwayat Upload Saya</h3>
    <div style="overflow-x: auto;">
        <table style="width: 100%; text-align: left; border-collapse: collapse; color: #cbd5e1;">
            <thead>
                <tr style="border-bottom: 1px solid #334155;">
                    <th style="padding: 10px;">Judul</th>
                    <th style="padding: 10px;">Tanggal</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($myBerita as $mb)
                <tr style="border-bottom: 1px solid #1e293b;">
                    <td style="padding: 10px;">{{ $mb->judul }}</td>
                    <td style="padding: 10px;">{{ \Carbon\Carbon::parse($mb->tanggal_post)->format('d M Y') }}</td>
                    <td style="padding: 10px;">
                        @if($mb->status === 'published') <span class="status-badge st-published">Published</span>
                        @elseif($mb->status === 'pending') <span class="status-badge st-pending">Menunggu ACC</span>
                        @else <span class="status-badge st-rejected">Ditolak</span> @endif
                    </td>
                    <td style="padding: 10px;">
                        <a href="{{ route('berita', ['edit' => $mb->id_berita]) }}" style="color: var(--primary); margin-right: 10px;">Edit</a>
                        <button type="button"
                                data-url="{{ route('berita.hapus', $mb->id_berita) }}"
                                data-name="{{ $mb->judul }}"
                                onclick="openDeleteModal(this)"
                                style="background: none; border: none; color: var(--danger); cursor: pointer; padding: 0; font: inherit;">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
