@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 class="page-title">Data Kelas</h2>
        <p class="page-subtitle">Manajemen ruang belajar &amp; kompetensi keahlian.</p>
    </div>
    <div class="header-btn-group">
        <a href="{{ route('export.preview', ['type' => 'kelas']) }}" class="btn btn-outline">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button id="toggleFormBtn" class="btn">
            <i class="fas fa-plus-circle"></i> Tambah Kelas
        </button>
        @endif
    </div>
</div>

@if(request('cari'))
<div class="alert-filter">
    <span><i class="fas fa-filter" style="color: var(--primary);"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('kelas') }}" class="btn btn-secondary btn-sm">Reset Pencarian</a>
</div>
@endif

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-chalkboard"></i> {{ $edit ? 'Edit Data Kelas' : 'Tambah Kelas Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('kelas.update') : route('kelas.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_kelas" value="{{ $edit->id_kelas }}"> @endif

        <div class="form-grid-2">
            <div class="form-group">
                <label>Nama Kelas</label>
                <input type="text" name="nama_kelas" value="{{ old('nama_kelas', $edit->nama_kelas ?? '') }}" placeholder="Contoh: XII PPLG 1" required>
            </div>

            <div class="form-group">
                <label>Kompetensi Keahlian (Jurusan)</label>
                <input type="text" name="jurusan" value="{{ old('jurusan', $edit->jurusan ?? '') }}" placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @forelse($kelas as $k)
    <div class="student-card">
        <div class="student-avatar">
            <i class="fas fa-school" style="font-size: 30px; color: var(--primary);"></i>
        </div>

        <h3 class="student-name">{{ $k->nama_kelas }}</h3>

        <span class="student-nisn">
            {{ $k->jurusan ?? 'Umum' }}
        </span>

        @if(Auth::user()->role === 'admin')
        <div class="card-actions" style="margin-top: 18px;">
            <a href="{{ route('kelas', ['edit' => $k->id_kelas]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>

            <button type="button"
                    data-url="{{ route('kelas.hapus', $k->id_kelas) }}"
                    data-name="{{ $k->nama_kelas }}"
                    onclick="openDeleteModal(this)"
                    class="btn-icon btn-delete" title="Hapus">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-school"></i>
        <strong>Belum ada data kelas.</strong>
        <p style="margin: 6px 0 0;">Tambahkan kelas baru untuk mulai mengelola rombel belajar.</p>
    </div>
    @endforelse
</div>
@endsection
