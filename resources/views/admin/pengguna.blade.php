@extends('layouts.admin')

@section('title', 'Data Pengguna')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 class="page-title">Data Pengguna (Users)</h2>
        <p class="page-subtitle">Manajemen akun login dan hak akses sistem.</p>
    </div>

    <div class="header-btn-group">
        <a href="{{ route('export.preview', ['type' => 'pengguna']) }}" class="btn btn-outline">
            <i class="fas fa-file-csv"></i> Export Data
        </a>
        <button id="toggleFormBtn" class="btn">
            <i class="fas fa-user-plus"></i> Tambah User
        </button>
    </div>
</div>

@if(request('cari'))
<div class="alert-filter">
    <span><i class="fas fa-filter" style="color: var(--primary);"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('pengguna') }}" class="btn btn-secondary btn-sm">Reset Pencarian</a>
</div>
@endif

<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-user-cog"></i> {{ $edit ? 'Edit Data Pengguna' : 'Registrasi Pengguna Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('pengguna.update') : route('pengguna.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id" value="{{ $edit->id }}"> @endif

        <div class="form-grid-2">
            <div>
                <div class="form-group">
                    <label>Nama Lengkap (Name)</label>
                    <input type="text" name="name" value="{{ old('name', $edit->name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>Email (Login)</label>
                    <input type="email" name="email" value="{{ old('email', $edit->email ?? '') }}" required>
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="{{ $edit ? 'Kosongkan jika tidak diubah' : 'Min. 8 Karakter' }}" {{ $edit ? '' : 'required' }}>
                </div>
                <div class="form-group">
                    <label>Role (Hak Akses)</label>
                    <select name="role" required>
                        <option value="">-- Pilih Akses --</option>
                        <option value="admin" {{ old('role', $edit->role ?? '') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="guru" {{ old('role', $edit->role ?? '') === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="siswa" {{ old('role', $edit->role ?? '') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan User</button>
        </div>
    </form>
</div>

<div class="student-grid">
    @forelse($pengguna as $p)
    <div class="student-card" style="border-top-color: {{ ($p->role ?? '') === 'admin' ? 'var(--primary)' : 'var(--accent)' }};">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($p->name) }}&background=dcfce7&color=15803d&bold=true"
             class="student-avatar" alt="{{ $p->name }}">

        <h3 class="student-name">{{ $p->name }}</h3>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin: 4px 0 14px;">{{ $p->email }}</p>

        <span class="student-nisn">
            {{ $p->role ?? 'User' }}
        </span>

        <div class="card-actions">
            <a href="{{ route('pengguna', ['edit' => $p->id]) }}" class="btn-icon btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
            @if((int) $p->id !== (int) Auth::id())
            <button type="button"
                    data-url="{{ route('pengguna.hapus', $p->id) }}"
                    data-name="{{ $p->name }}"
                    onclick="openDeleteModal(this)"
                    class="btn-icon btn-delete" title="Hapus"><i class="fas fa-trash"></i></button>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <strong>Belum ada data pengguna.</strong>
        <p style="margin: 6px 0 0;">Tambahkan akun pengguna baru untuk mengakses sistem.</p>
    </div>
    @endforelse
</div>
@endsection
