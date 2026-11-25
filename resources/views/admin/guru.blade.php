@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 class="page-title">Data Guru</h2>
        <p class="page-subtitle">Manajemen data tenaga pendidik sekolah.</p>
    </div>
    <div class="header-btn-group">
        <a href="{{ route('export.preview', ['type' => 'guru']) }}" class="btn btn-outline">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button id="toggleFormBtn" class="btn">
            <i class="fas fa-chalkboard-teacher"></i> Tambah Guru
        </button>
        @endif
    </div>
</div>

@if(request('cari'))
<div class="alert-filter">
    <span><i class="fas fa-filter" style="color: var(--primary);"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('guru') }}" class="btn btn-secondary btn-sm">Reset Pencarian</a>
</div>
@endif

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-user-edit"></i> {{ $edit ? 'Edit Data Guru' : 'Registrasi Guru Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('guru.update') : route('guru.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_guru" value="{{ $edit->id_guru }}"> @endif

        <div class="form-grid-2">
            <div>
                <div class="form-group">
                    <label>NIP / NUPTK</label>
                    <input type="text" name="nip" value="{{ old('nip', $edit->nip ?? '') }}" placeholder="Nomor Induk Pegawai">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $edit->nama ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih Gender --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $edit->jenis_kelamin ?? '') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $edit->jenis_kelamin ?? '') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $edit->email ?? '') }}" placeholder="email@sekolah.sch.id">
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $edit->no_hp ?? '') }}" placeholder="08xxxxxxxxxx">
                </div>
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
    @forelse($guru as $g)
    <div class="student-card" style="border-top-color: var(--teal);">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($g->nama) }}&background=ccfbf1&color=0f766e&bold=true"
             class="student-avatar" style="border-color: var(--teal-light);" alt="{{ $g->nama }}">

        <h3 class="student-name">{{ $g->nama }}</h3>
        <span class="student-nisn" style="background: var(--teal-light); color: #115e59; border-color: #99f6e4;">
            {{ $g->nip ?? '-' }}
        </span>

        <div class="student-details">
            <span title="Email"><i class="fas fa-envelope" style="color: var(--teal);"></i> {{ Str::limit($g->email ?? '-', 24) }}</span>
        </div>
        <div class="student-details-sub">
            <span title="No HP"><i class="fas fa-phone" style="color: var(--primary);"></i> {{ $g->no_hp ?? '-' }}</span>
            <span title="Gender"><i class="fas fa-venus-mars" style="color: var(--primary);"></i> {{ $g->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</span>
        </div>

        @if(Auth::user()->role === 'admin')
        <div class="card-actions">
            <a href="{{ route('guru', ['edit' => $g->id_guru]) }}" class="btn-icon btn-edit" title="Edit"><i class="fas fa-pen"></i></a>

            <button type="button"
                    data-url="{{ route('guru.hapus', $g->id_guru) }}"
                    data-name="{{ $g->nama }}"
                    onclick="openDeleteModal(this)"
                    class="btn-icon btn-delete" title="Hapus">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-chalkboard-teacher"></i>
        <strong>Belum ada data guru.</strong>
        <p style="margin: 6px 0 0;">Data tenaga pendidik akan tampil di sini.</p>
    </div>
    @endforelse
</div>
@endsection
