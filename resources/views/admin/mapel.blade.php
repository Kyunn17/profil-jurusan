@extends('layouts.admin')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="page-head">
    <div>
        <h1>Mata Pelajaran</h1>
        <p>Katalog kurikulum dikelompokkan berdasarkan kategori. Klik mapel untuk melihat detail.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('export.preview', ['type' => 'mapel']) }}" class="btn btn-outline btn-sm">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button type="button" id="toggleFormBtn" class="btn btn-sm">
            <i class="fas fa-book-open"></i> Tambah Mapel
        </button>
        @endif
    </div>
</div>

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-pen"></i> {{ $edit ? 'Edit Mapel' : 'Tambah Mapel Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('mapel.update') : route('mapel.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_mapel" value="{{ $edit->id_mapel }}"> @endif

        <div class="form-grid-2">
            <div class="form-group">
                <label>Nama Mata Pelajaran</label>
                <input type="text" name="nama_mapel" value="{{ old('nama_mapel', $edit->nama_mapel ?? '') }}" placeholder="Contoh: Pemrograman Web" required>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Produktif" {{ old('kategori', $edit->kategori ?? '') === 'Produktif' ? 'selected' : '' }}>Produktif (Kejuruan)</option>
                    <option value="Umum" {{ old('kategori', $edit->kategori ?? '') === 'Umum' ? 'selected' : '' }}>Umum (Adaptif/Normatif)</option>
                    <option value="Mulok" {{ old('kategori', $edit->kategori ?? '') === 'Mulok' ? 'selected' : '' }}>Muatan Lokal</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" id="cancelBtn" class="btn btn-secondary btn-sm">Batal</button>
            <button type="submit" class="btn btn-sm">
                <i class="fas fa-save"></i> {{ $edit ? 'Perbarui' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>
@endif

<div class="section-toolbar">
    <span class="section-count"><i class="fas fa-book"></i> {{ $mapel->count() }} Mata Pelajaran</span>
    <form method="GET" action="{{ route('mapel') }}" class="toolbar">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau kategori..." class="input-inline">
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
        @if(request('cari'))
            <a href="{{ route('mapel') }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

@if($mapel->isEmpty())
    <div class="empty-panel">
        <i class="fas fa-book"></i>
        <strong>Belum ada mata pelajaran.</strong>
        <p style="margin: 4px 0 0;">Data kurikulum akan ditampilkan di sini.</p>
    </div>
@else
    @php
        $groupedMapel = $mapel->groupBy(fn ($item) => $item->kategori ?: 'Umum');
    @endphp

    @foreach($groupedMapel as $kategori => $items)
    <section class="catalog-group">
        <div class="catalog-group-head">
            <h3>{{ $kategori }}</h3>
            <span class="status-pill pill-neutral mono">{{ $items->count() }} Mapel</span>
        </div>

        <div class="catalog-chips">
            @foreach($items as $m)
            @php
                $ampuCount = $m->mapel_diampu_count ?? 0;
                $detail = [
                    'initials' => strtoupper(substr(preg_replace('/\s+/', '', (string) $m->nama_mapel), 0, 2) ?: 'MP'),
                    'initialsClass' => 'initials-teal',
                    'title' => $m->nama_mapel,
                    'subtitle' => 'Kategori: ' . ($m->kategori ?? 'Umum'),
                    'badge' => $m->kategori ?? 'Umum',
                    'badgeClass' => 'pill-izin',
                    'fields' => [
                        ['label' => 'Nama Mata Pelajaran', 'value' => $m->nama_mapel],
                        ['label' => 'Kategori', 'value' => $m->kategori ?? 'Umum'],
                        ['label' => 'Kelas Diampu', 'value' => $ampuCount . ' kelas', 'mono' => true],
                    ],
                    'editUrl' => Auth::user()->role === 'admin' ? route('mapel', ['edit' => $m->id_mapel]) : null,
                    'deleteUrl' => Auth::user()->role === 'admin' ? route('mapel.hapus', $m->id_mapel) : null,
                    'deleteName' => $m->nama_mapel,
                ];
            @endphp
            <div class="catalog-chip" role="button" tabindex="0" data-detail="{{ json_encode($detail) }}" onclick="openDetailModal(this)">
                <div>
                    <strong>{{ $m->nama_mapel }}</strong>
                    <small class="mono">{{ $ampuCount }} kelas diampu</small>
                </div>
                <i class="fas fa-chevron-right roster-chevron"></i>
            </div>
            @endforeach
        </div>
    </section>
    @endforeach
@endif
@endsection
