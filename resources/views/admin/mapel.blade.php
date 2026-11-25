@extends('layouts.admin')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 class="page-title">Mata Pelajaran</h2>
        <p class="page-subtitle">Kurikulum dan daftar mata pelajaran sekolah.</p>
    </div>
    <div class="header-btn-group">
        <a href="{{ route('export.preview', ['type' => 'mapel']) }}" class="btn btn-outline">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button id="toggleFormBtn" class="btn">
            <i class="fas fa-book-open"></i> Tambah Mapel
        </button>
        @endif
    </div>
</div>

@if(request('cari'))
<div class="alert-filter">
    <span><i class="fas fa-filter" style="color: var(--primary);"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('mapel') }}" class="btn btn-secondary btn-sm">Reset Pencarian</a>
</div>
@endif

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
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> {{ $edit ? 'Perbarui' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @forelse($mapel as $m)
    <div class="student-card" style="border-top-color: var(--accent);">
        <div class="student-avatar" style="background: var(--primary-soft);">
            <i class="fas fa-book" style="font-size: 30px; color: var(--primary);"></i>
        </div>

        <h3 class="student-name" style="min-height: 48px; display:flex; align-items:center; justify-content:center;">
            {{ $m->nama_mapel }}
        </h3>

        <span class="student-nisn">
            {{ $m->kategori ?? 'Umum' }}
        </span>

        @if(Auth::user()->role === 'admin')
        <div class="card-actions" style="margin-top: 16px;">
            <a href="{{ route('mapel', ['edit' => $m->id_mapel]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>

            <button type="button"
                    data-url="{{ route('mapel.hapus', $m->id_mapel) }}"
                    data-name="{{ $m->nama_mapel }}"
                    onclick="openDeleteModal(this)"
                    class="btn-icon btn-delete" title="Hapus">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-book"></i>
        <strong>Belum ada mata pelajaran.</strong>
        <p style="margin: 6px 0 0;">Data kurikulum akan ditampilkan di sini.</p>
    </div>
    @endforelse
</div>
@endsection
