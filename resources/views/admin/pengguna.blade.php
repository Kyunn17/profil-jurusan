@extends('layouts.admin')

@section('title', 'Data Pengguna')

@section('content')
<div class="page-head">
    <div>
        <h1>Data Pengguna</h1>
        <p>Klik akun pengguna untuk melihat detail peran, email, dan kelola akses.</p>
    </div>

    <div class="page-actions">
        <a href="{{ route('export.preview', ['type' => 'pengguna']) }}" class="btn btn-outline btn-sm">
            <i class="fas fa-file-csv"></i> Export Data
        </a>
        <button type="button" id="toggleFormBtn" class="btn btn-sm">
            <i class="fas fa-user-plus"></i> Tambah User
        </button>
    </div>
</div>

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
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $edit->name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label>Email Login</label>
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
            <button type="button" id="cancelBtn" class="btn btn-secondary btn-sm">Batal</button>
            <button type="submit" class="btn btn-sm"><i class="fas fa-save"></i> Simpan User</button>
        </div>
    </form>
</div>

<div class="section-toolbar">
    <span class="section-count"><i class="fas fa-users-cog"></i> {{ $pengguna->count() }} Akun Terdaftar</span>
    <form method="GET" action="{{ route('pengguna') }}" class="toolbar">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau email..." class="input-inline">
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
        @if(request('cari'))
            <a href="{{ route('pengguna') }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

@if($pengguna->isEmpty())
    <div class="empty-panel">
        <i class="fas fa-users-cog"></i>
        <strong>Belum ada data pengguna.</strong>
        <p style="margin: 4px 0 0;">Tambahkan akun baru untuk memberikan akses ke sistem.</p>
    </div>
@else
    <div class="roster-grid">
        @foreach($pengguna as $p)
        @php
            $parts = preg_split('/\s+/', trim((string) $p->name)) ?: ['U'];
            $ini = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
            $roleBadgeClass = ($p->role ?? '') === 'admin' ? 'pill-hadir' : (($p->role ?? '') === 'guru' ? 'pill-izin' : 'pill-neutral');
            $detail = [
                'initials' => $ini,
                'initialsClass' => ($p->role ?? '') === 'guru' ? 'initials-teal' : '',
                'title' => $p->name,
                'subtitle' => $p->email,
                'badge' => strtoupper($p->role ?? 'User'),
                'badgeClass' => $roleBadgeClass,
                'fields' => [
                    ['label' => 'Nama Lengkap', 'value' => $p->name],
                    ['label' => 'Alamat Email', 'value' => $p->email, 'mono' => true],
                    ['label' => 'Role (Hak Akses)', 'value' => strtoupper($p->role ?? 'User')],
                    ['label' => 'Verifikasi Email', 'value' => $p->email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi'],
                    ['label' => 'Terdaftar Sejak', 'value' => $p->created_at ? $p->created_at->format('d M Y') : '-', 'mono' => true],
                ],
                'editUrl' => route('pengguna', ['edit' => $p->id]),
                'deleteUrl' => (int) $p->id !== (int) Auth::id() ? route('pengguna.hapus', $p->id) : null,
                'deleteName' => $p->name,
            ];
        @endphp
        <div class="roster-item" role="button" tabindex="0" data-detail="{{ json_encode($detail) }}" onclick="openDetailModal(this)">
            <span class="initials {{ ($p->role ?? '') === 'guru' ? 'initials-teal' : '' }}">{{ $ini }}</span>
            <div class="roster-main">
                <strong>{{ $p->name }}</strong>
                <small class="mono">{{ $p->email }}</small>
            </div>
            <div class="roster-right">
                <span class="status-pill {{ $roleBadgeClass }}">{{ $p->role ?? 'User' }}</span>
                <i class="fas fa-chevron-right roster-chevron"></i>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
