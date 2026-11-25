@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 style="color: var(--success); margin: 0; font-size: 2rem;">Data Kelas</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0;">Manajemen ruang belajar &amp; kompetensi keahlian.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('export.preview', ['type' => 'kelas']) }}" class="btn" style="background: var(--primary);">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button id="toggleFormBtn" class="btn" style="background: var(--success);">
            <i class="fas fa-plus-circle"></i> Tambah Kelas
        </button>
        @endif
    </div>
</div>

@if(request('cari'))
<div class="alert-success" style="justify-content: space-between;">
    <span><i class="fas fa-filter"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('kelas') }}" class="btn btn-secondary" style="padding: 5px 12px; font-size: 12px;">Reset Pencarian</a>
</div>
@endif

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }}; border-color: var(--success);">
    <h3 style="color: white; border-bottom: 1px solid #334155; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="fas fa-chalkboard"></i> {{ $edit ? 'Edit Data Kelas' : 'Tambah Kelas Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('kelas.update') : route('kelas.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_kelas" value="{{ $edit->id_kelas }}"> @endif

        <div class="form-group">
            <label style="color: var(--success);">Nama Kelas</label>
            <input type="text" name="nama_kelas" value="{{ old('nama_kelas', $edit->nama_kelas ?? '') }}" placeholder="Contoh: XII PPLG 1" required>
        </div>

        <div class="form-group">
            <label style="color: var(--success);">Kompetensi Keahlian (Jurusan)</label>
            <input type="text" name="jurusan" value="{{ old('jurusan', $edit->jurusan ?? '') }}" placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim" required>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="button" id="cancelBtn" class="btn btn-secondary" style="margin-right: 10px;">Batal</button>
            <button type="submit" class="btn" style="background: var(--success);"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @foreach($kelas as $k)
    <div class="student-card" style="border-top: 4px solid var(--success);">
        <div class="student-avatar" style="background: var(--bg-card); border-color: var(--success); display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-school" style="font-size: 35px; color: var(--success);"></i>
        </div>

        <h3 class="student-name">{{ $k->nama_kelas }}</h3>

        <span class="student-nisn" style="background: rgba(16, 185, 129, 0.1); color: var(--success); border-radius: 4px; border: 1px solid var(--success);">
            {{ $k->jurusan ?? 'Umum' }}
        </span>

        @if(Auth::user()->role === 'admin')
        <div class="card-actions" style="margin-top: 25px;">
            <a href="{{ route('kelas', ['edit' => $k->id_kelas]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>

            <button type="button"
                    data-url="{{ route('kelas.hapus', $k->id_kelas) }}"
                    data-name="{{ $k->nama_kelas }}"
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
