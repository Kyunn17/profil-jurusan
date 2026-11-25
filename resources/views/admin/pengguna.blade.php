@extends('layouts.admin')

@section('title', 'Data Pengguna')

@push('styles')
<style>
    .form-input { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #475569; background: #0f172a; color: white; }
</style>
@endpush

@section('content')
<div class="page-header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="color: var(--primary); margin: 0; font-size: 2rem;">Data Pengguna (Users)</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0;">Manajemen akun Login.</p>
    </div>

    <div style="display: flex; gap: 10px;">
        <a href="{{ route('export.preview', ['type' => 'pengguna']) }}" class="btn" style="background-color: #10b981;">
            <i class="fas fa-file-csv"></i> Export Data
        </a>
        <button id="toggleFormBtn" class="btn">
            <i class="fas fa-user-plus"></i> Tambah User
        </button>
    </div>
</div>

@if(request('cari'))
<div class="alert-success" style="justify-content: space-between; border-color: var(--primary); color: var(--primary); background: rgba(56, 189, 248, 0.1);">
    <span><i class="fas fa-filter"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('pengguna') }}" class="btn btn-secondary" style="padding: 5px 12px; font-size: 12px;">Reset Pencarian</a>
</div>
@endif

<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }}; border-top: 4px solid #ec4899; background: #1e293b; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
    <h3 style="color: white; margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 10px;">
        <i class="fas fa-user-cog"></i> {{ $edit ? 'Edit Data Pengguna' : 'Registrasi Pengguna Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('pengguna.update') : route('pengguna.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id" value="{{ $edit->id }}"> @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color: #ec4899; display: block; margin-bottom: 5px;">Nama Lengkap (Name)</label>
                    <input type="text" name="name" value="{{ old('name', $edit->name ?? '') }}" class="form-input" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color: #ec4899; display: block; margin-bottom: 5px;">Email (Login)</label>
                    <input type="email" name="email" value="{{ old('email', $edit->email ?? '') }}" class="form-input" required>
                </div>
            </div>

            <div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color: #ec4899; display: block; margin-bottom: 5px;">Password</label>
                    <input type="password" name="password" placeholder="{{ $edit ? 'Kosongkan jika tidak diubah' : 'Min. 8 Karakter' }}" class="form-input" {{ $edit ? '' : 'required' }}>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="color: #ec4899; display: block; margin-bottom: 5px;">Role (Hak Akses)</label>
                    <select name="role" class="form-input" required>
                        <option value="">-- Pilih Akses --</option>
                        <option value="admin" {{ old('role', $edit->role ?? '') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="guru" {{ old('role', $edit->role ?? '') === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="siswa" {{ old('role', $edit->role ?? '') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-top: 25px; text-align: right;">
            <button type="button" id="cancelBtn" class="btn" style="background-color: #64748b; margin-right: 10px;">Batal</button>
            <button type="submit" class="btn" style="background-color: #ec4899;">Simpan User</button>
        </div>
    </form>
</div>

<div class="student-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
    @foreach($pengguna as $p)
    <div class="student-card" style="background: #1e293b; padding: 20px; border-radius: 10px; text-align: center; border-top: 4px solid {{ ($p->role ?? '') === 'admin' ? '#ec4899' : '#38bdf8' }}; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($p->name) }}&background=0f172a&color=fff&bold=true"
             class="student-avatar"
             style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 15px;">

        <h3 class="student-name" style="color: #f1f5f9; margin: 0; font-size: 1.1rem;">{{ $p->name }}</h3>
        <p style="color: #94a3b8; font-size: 0.9rem; margin: 5px 0 15px;">{{ $p->email }}</p>

        <span style="background: #334155; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; text-transform: uppercase;">
            {{ $p->role ?? 'User' }}
        </span>

        <div class="card-actions" style="margin-top: 20px; display: flex; justify-content: center; gap: 10px;">
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
    @endforeach
</div>
@endsection
