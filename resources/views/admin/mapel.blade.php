@extends('layouts.admin')

@section('title', 'Mata Pelajaran')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 style="color: var(--warning); margin: 0; font-size: 2rem;">Mata Pelajaran</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0;">Kurikulum dan daftar pelajaran sekolah.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('export.preview', ['type' => 'mapel']) }}" class="btn" style="background: var(--success);">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button id="toggleFormBtn" class="btn" style="background: var(--warning); color: #000;">
            <i class="fas fa-book-open"></i> Tambah Mapel
        </button>
        @endif
    </div>
</div>

@if(request('cari'))
<div class="alert-success" style="justify-content: space-between; border-color: var(--warning); color: var(--warning); background: rgba(245, 158, 11, 0.1);">
    <span><i class="fas fa-filter"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('mapel') }}" class="btn btn-secondary" style="padding: 5px 12px; font-size: 12px;">Reset Pencarian</a>
</div>
@endif

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }}; border-color: var(--warning);">
    <h3 style="color: white; border-bottom: 1px solid #334155; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="fas fa-pen"></i> {{ $edit ? 'Edit Mapel' : 'Tambah Mapel Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('mapel.update') : route('mapel.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_mapel" value="{{ $edit->id_mapel }}"> @endif

        <div class="form-group">
            <label style="color: var(--warning);">Nama Mata Pelajaran</label>
            <input type="text" name="nama_mapel" value="{{ old('nama_mapel', $edit->nama_mapel ?? '') }}" placeholder="Contoh: Pemrograman Web" required>
        </div>

        <div class="form-group">
            <label style="color: var(--warning);">Kategori</label>
            <select name="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Produktif" {{ old('kategori', $edit->kategori ?? '') === 'Produktif' ? 'selected' : '' }}>Produktif (Kejuruan)</option>
                <option value="Umum" {{ old('kategori', $edit->kategori ?? '') === 'Umum' ? 'selected' : '' }}>Umum (Adaptif/Normatif)</option>
                <option value="Mulok" {{ old('kategori', $edit->kategori ?? '') === 'Mulok' ? 'selected' : '' }}>Muatan Lokal</option>
            </select>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="button" id="cancelBtn" class="btn btn-secondary" style="margin-right: 10px;">Batal</button>
            <button type="submit" class="btn" style="background: var(--warning); color: #000;">
                <i class="fas fa-save"></i> {{ $edit ? 'Perbarui' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @foreach($mapel as $m)
    <div class="student-card" style="border-top: 4px solid var(--warning);">
        <div class="student-avatar" style="background: var(--bg-card); border-color: var(--warning); display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-book" style="font-size: 35px; color: var(--warning);"></i>
        </div>

        <h3 class="student-name" style="min-height: 50px; display:flex; align-items:center; justify-content:center;">
            {{ $m->nama_mapel }}
        </h3>

        <span class="student-nisn" style="background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid var(--warning);">
            {{ $m->kategori ?? 'Umum' }}
        </span>

        @if(Auth::user()->role === 'admin')
        <div class="card-actions" style="margin-top: 20px;">
            <a href="{{ route('mapel', ['edit' => $m->id_mapel]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>

            <button type="button"
                    data-url="{{ route('mapel.hapus', $m->id_mapel) }}"
                    data-name="{{ $m->nama_mapel }}"
                    onclick="openDeleteModal(this)"
                    class="btn-icon btn-delete" style="border:none;" title="Hapus">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endsection
