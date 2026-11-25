@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 style="color: var(--secondary); margin: 0; font-size: 2rem;">Data Guru</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0;">Manajemen Data Tenaga Pendidik.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('export.preview', ['type' => 'guru']) }}" class="btn" style="background: var(--success);">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button id="toggleFormBtn" class="btn" style="background: var(--secondary);">
            <i class="fas fa-chalkboard-teacher"></i> Tambah Guru
        </button>
        @endif
    </div>
</div>

@if(request('cari'))
<div class="alert-success" style="justify-content: space-between; border-color: var(--secondary); color: var(--secondary); background: rgba(236, 72, 153, 0.1);">
    <span><i class="fas fa-filter"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('guru') }}" class="btn btn-secondary" style="padding: 5px 12px; font-size: 12px;">Reset Pencarian</a>
</div>
@endif

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }}; border-color: var(--secondary);">
    <h3 style="color: white; border-bottom: 1px solid #334155; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="fas fa-user-edit"></i> {{ $edit ? 'Edit Data Guru' : 'Registrasi Guru Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('guru.update') : route('guru.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_guru" value="{{ $edit->id_guru }}"> @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            <div>
                <div class="form-group">
                    <label style="color: var(--secondary);">NIP / NUPTK</label>
                    <input type="text" name="nip" value="{{ old('nip', $edit->nip ?? '') }}" placeholder="Nomor Induk Pegawai">
                </div>
                <div class="form-group">
                    <label style="color: var(--secondary);">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $edit->nama ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label style="color: var(--secondary);">Jenis Kelamin</label>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih Gender --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $edit->jenis_kelamin ?? '') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $edit->jenis_kelamin ?? '') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label style="color: var(--secondary);">Email</label>
                    <input type="email" name="email" value="{{ old('email', $edit->email ?? '') }}" placeholder="email@sekolah.sch.id">
                </div>
                <div class="form-group">
                    <label style="color: var(--secondary);">Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $edit->no_hp ?? '') }}" placeholder="08xxxxxxxxxx">
                </div>
            </div>
        </div>

        <div style="margin-top: 25px; text-align: right;">
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn" style="background: var(--secondary);"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @foreach($guru as $g)
    <div class="student-card" style="border-top: 4px solid var(--secondary);">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($g->nama) }}&background=0f172a&color=ec4899&bold=true"
             class="student-avatar" style="border-color: var(--secondary);">

        <h3 class="student-name">{{ $g->nama }}</h3>
        <span class="student-nisn" style="color: var(--secondary); background: rgba(236, 72, 153, 0.1);">
            {{ $g->nip ?? '-' }}
        </span>

        <div class="student-details">
            <span title="Email"><i class="fas fa-envelope"></i> {{ Str::limit($g->email ?? '-', 20) }}</span>
        </div>
        <div class="student-details" style="margin-top: -5px; border:none; padding:0;">
            <span title="No HP"><i class="fas fa-phone"></i> {{ $g->no_hp ?? '-' }}</span>
            <span title="Gender"><i class="fas fa-venus-mars"></i> {{ $g->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</span>
        </div>

        @if(Auth::user()->role === 'admin')
        <div class="card-actions">
            <a href="{{ route('guru', ['edit' => $g->id_guru]) }}" class="btn-icon btn-edit" title="Edit"><i class="fas fa-pen"></i></a>

            <button type="button"
                    data-url="{{ route('guru.hapus', $g->id_guru) }}"
                    data-name="{{ $g->nama }}"
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
