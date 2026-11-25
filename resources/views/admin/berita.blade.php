@extends('layouts.admin')

@section('title', 'Portal Berita')

@section('content')
<div class="page-head">
    <div>
        <h1>Portal Berita Sekolah</h1>
        <p>Klik artikel untuk membaca berita lengkap beserta lampiran foto.</p>
    </div>
    <div class="page-actions">
        <form method="GET" action="{{ route('berita') }}" class="toolbar">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari judul berita..." class="input-inline" style="min-width: 190px;">
            <button type="submit" class="btn btn-outline btn-sm">Cari</button>
            @if(request('cari'))
                <a href="{{ route('berita') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>

        @if(Auth::user()->role === 'admin')
        <a href="{{ route('export.preview', ['type' => 'berita']) }}" class="btn btn-outline btn-sm">
            <i class="fas fa-file-csv"></i> Export
        </a>
        @endif

        <button type="button" id="toggleFormBtn" class="btn btn-sm">
            <i class="fas fa-pen-nib"></i> Tulis Berita
        </button>
    </div>
</div>

<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-newspaper"></i> {{ $edit ? 'Edit Berita' : 'Tulis Berita Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('berita.update') : route('berita.store') }}" enctype="multipart/form-data">
        @csrf
        @if($edit) <input type="hidden" name="id_berita" value="{{ $edit->id_berita }}"> @endif

        <div class="form-grid-2">
            <div class="form-group">
                <label>Judul Berita</label>
                <input type="text" name="judul" value="{{ old('judul', $edit->judul ?? '') }}" placeholder="Judul berita..." required>
            </div>
            <div class="form-group">
                <label>Foto Sampul {{ $edit ? '(Opsional saat edit)' : '' }}</label>
                <input type="file" name="foto" {{ $edit ? '' : 'required' }}>
            </div>
        </div>

        <div class="form-group">
            <label>Isi Berita</label>
            <textarea name="isi" rows="5" placeholder="Tulis detail berita..." required>{{ old('isi', $edit->isi ?? '') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="button" id="cancelBtn" class="btn btn-secondary btn-sm">Batal</button>
            <button type="submit" class="btn btn-sm">{{ Auth::user()->role === 'admin' ? 'Terbitkan' : 'Kirim Moderasi' }}</button>
        </div>
    </form>
</div>

@if(Auth::user()->role === 'admin' && count($beritaPending) > 0)
<section class="panel" style="border-color: #fde68a;">
    <div class="panel-header" style="background: var(--sakit-bg);">
        <h2 class="panel-title" style="color: var(--sakit-ink);">
            <i class="fas fa-clock" style="color: var(--sakit-dot);"></i>
            Antrean Moderasi ({{ count($beritaPending) }})
        </h2>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tanggal</th>
                    <th style="text-align: right;">Keputusan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($beritaPending as $bp)
                @php
                    $bpDetail = [
                        'title' => $bp->judul,
                        'subtitle' => 'Oleh ' . ($bp->user?->name ?? 'User Terhapus') . ' · ' . \Carbon\Carbon::parse($bp->tanggal_post)->format('d M Y'),
                        'badge' => 'Menunggu ACC',
                        'badgeClass' => 'pill-pending',
                        'image' => $bp->foto ? 'data:image/jpeg;base64,' . base64_encode($bp->foto) : null,
                        'body' => strip_tags((string) $bp->isi),
                        'fields' => [
                            ['label' => 'Penulis', 'value' => $bp->user?->name ?? 'User Terhapus'],
                            ['label' => 'Peran', 'value' => $bp->user?->role ?? '-'],
                        ],
                    ];
                @endphp
                <tr class="clickable-row" data-detail="{{ json_encode($bpDetail) }}" onclick="openDetailModal(this)">
                    <td><strong>{{ $bp->judul }}</strong></td>
                    <td>{{ $bp->user?->name ?? 'User Terhapus' }}</td>
                    <td class="mono">{{ \Carbon\Carbon::parse($bp->tanggal_post)->format('d M Y') }}</td>
                    <td style="text-align: right;" onclick="event.stopPropagation();">
                        <div class="row-actions">
                            <form method="POST" action="{{ route('berita.approve', $bp->id_berita) }}" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-xs"><i class="fas fa-check"></i> ACC</button>
                            </form>
                            <button type="button"
                                    data-url="{{ route('berita.hapus', $bp->id_berita) }}"
                                    data-name="Berita '{{ $bp->judul }}'"
                                    onclick="openDeleteModal(this)"
                                    class="btn-ghost-delete">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif

<div class="editorial-grid">
    @forelse($beritaPublished as $b)
    @php
        $canManage = Auth::id() === $b->id_user || Auth::user()->role === 'admin';
        $detail = [
            'title' => $b->judul,
            'subtitle' => 'Oleh ' . ($b->user?->name ?? 'User Terhapus') . ' · ' . \Carbon\Carbon::parse($b->tanggal_post)->format('d M Y'),
            'badge' => 'Published',
            'badgeClass' => 'pill-published',
            'image' => $b->foto ? 'data:image/jpeg;base64,' . base64_encode($b->foto) : null,
            'body' => strip_tags((string) $b->isi),
            'fields' => [
                ['label' => 'Penulis', 'value' => $b->user?->name ?? 'User Terhapus'],
                ['label' => 'Peran Penulis', 'value' => $b->user?->role ?? '-'],
                ['label' => 'Tanggal Terbit', 'value' => \Carbon\Carbon::parse($b->tanggal_post)->format('d M Y'), 'mono' => true],
                ['label' => 'Status', 'value' => strtoupper($b->status)],
            ],
            'editUrl' => $canManage ? route('berita', ['edit' => $b->id_berita]) : null,
            'deleteUrl' => $canManage ? route('berita.hapus', $b->id_berita) : null,
            'deleteName' => $b->judul,
        ];
    @endphp
    <article class="news-card news-card-interactive" role="button" tabindex="0" data-detail="{{ json_encode($detail) }}" onclick="openDetailModal(this)">
        @if($b->foto)
            <img src="data:image/jpeg;base64,{{ base64_encode($b->foto) }}" class="news-thumb" alt="{{ $b->judul }}">
        @else
            <div class="news-thumb-empty">
                <i class="fas fa-image fa-2x"></i>
            </div>
        @endif

        <div class="news-body">
            <div class="news-meta">
                <span class="mono">{{ \Carbon\Carbon::parse($b->tanggal_post)->format('d M Y') }}</span>
                <span class="status-pill pill-published">{{ $b->user?->role ?? '-' }}</span>
            </div>

            <h3 class="news-title">{{ Str::limit($b->judul, 70) }}</h3>
            <p class="news-excerpt">{{ Str::limit(strip_tags($b->isi), 140) }}</p>

            <div class="news-footer">
                <span style="font-size: 0.8rem; color: var(--ink-2); font-weight: 500;">
                    Oleh <strong>{{ $b->user?->name ?? 'User Terhapus' }}</strong>
                </span>

                <span style="font-size: 0.78rem; color: var(--brand); font-weight: 600;">
                    Baca Selengkapnya <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                </span>
            </div>
        </div>
    </article>
    @empty
    <div class="panel" style="grid-column: 1 / -1; margin-bottom: 0;">
        <div class="panel-body" style="text-align: center; padding: 40px 20px; color: var(--ink-3);">
            Belum ada berita yang diterbitkan.
        </div>
    </div>
    @endforelse
</div>

@if(Auth::user()->role !== 'admin' && count($myBerita) > 0)
<section class="panel" style="margin-top: 24px; margin-bottom: 0;">
    <div class="panel-header">
        <h2 class="panel-title"><i class="fas fa-folder-open"></i> Riwayat Kiriman Saya</h2>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($myBerita as $mb)
                <tr>
                    <td><strong>{{ $mb->judul }}</strong></td>
                    <td class="mono">{{ \Carbon\Carbon::parse($mb->tanggal_post)->format('d M Y') }}</td>
                    <td>
                        @if($mb->status === 'published')
                            <span class="status-pill pill-published"><span class="status-dot"></span> Published</span>
                        @elseif($mb->status === 'pending')
                            <span class="status-pill pill-pending"><span class="status-dot"></span> Menunggu ACC</span>
                        @else
                            <span class="status-pill pill-rejected"><span class="status-dot"></span> Ditolak</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="row-actions">
                            <a href="{{ route('berita', ['edit' => $mb->id_berita]) }}" class="btn-ghost-edit">Edit</a>
                            <button type="button"
                                    data-url="{{ route('berita.hapus', $mb->id_berita) }}"
                                    data-name="{{ $mb->judul }}"
                                    onclick="openDeleteModal(this)"
                                    class="btn-ghost-delete">Hapus</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif
@endsection
