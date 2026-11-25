@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 style="color: var(--primary); margin: 0; font-size: 2rem;">Data Siswa</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0;">Kelola profil agen siswa dengan mudah.</p>
    </div>

    <div style="display: flex; gap: 10px;">
        <a href="{{ route('export.preview', ['type' => 'siswa']) }}" class="btn" style="background-color: var(--success);">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button id="toggleFormBtn" class="btn">
            <i class="fas fa-user-plus"></i> Tambah Siswa Baru
        </button>
        @endif
    </div>
</div>

@if(request('cari'))
<div class="alert-success" style="justify-content: space-between; border-color: var(--primary); color: var(--primary); background: rgba(56, 189, 248, 0.1);">
    <span><i class="fas fa-filter"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('siswa') }}" class="btn btn-secondary" style="padding: 5px 12px; font-size: 12px;">Reset Pencarian</a>
</div>
@endif

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 style="color: white; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 10px;">
        <i class="fas fa-pen-to-square"></i> {{ $edit ? 'Edit Data Siswa' : 'Registrasi Siswa Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('siswa.update') : route('siswa.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_siswa" value="{{ $edit->id_siswa }}"> @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            <div>
                <div class="form-group">
                    <label>NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $edit->nisn ?? '') }}" placeholder="Masukkan NISN" required>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $edit->nama ?? '') }}" placeholder="Nama Lengkap Siswa" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih Gender --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin', $edit->jenis_kelamin ?? '') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin', $edit->jenis_kelamin ?? '') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kelas</label>
                    <select name="id_kelas" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}" {{ (string) old('id_kelas', $edit->id_kelas ?? '') === (string) $k->id_kelas ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $edit->tanggal_lahir ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" value="{{ old('alamat', $edit->alamat ?? '') }}" placeholder="Alamat Domisili">
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $edit->no_hp ?? '') }}" placeholder="08xxxxxxxxxx">
                </div>
            </div>
        </div>

        <div style="margin-top: 25px; text-align: right;">
            <button type="button" id="cancelBtn" class="btn btn-secondary" style="margin-right: 10px;">Batal</button>
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> {{ $edit ? 'Perbarui Data' : 'Simpan Data' }}
            </button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @foreach($siswa as $s)
    <div class="student-card">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($s->nama) }}&background=0f172a&color=38bdf8&bold=true" class="student-avatar">

        <h3 class="student-name">{{ $s->nama }}</h3>
        <span class="student-nisn">{{ $s->nisn }}</span>

        <div class="student-details">
            <span title="Kelas"><i class="fas fa-users"></i> {{ $s->kelas->nama_kelas ?? 'N/A' }}</span>
            <span title="Gender"><i class="fas fa-venus-mars"></i> {{ $s->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</span>
        </div>

        <div class="student-details" style="border:none; padding-top:0; margin-bottom:15px; font-size: 0.85rem;">
            <span title="No HP"><i class="fas fa-phone"></i> {{ $s->no_hp ?? '-' }}</span>
        </div>

        @if(Auth::user()->role === 'admin')
        <div class="card-actions">
            <a href="{{ route('siswa', ['edit' => $s->id_siswa]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>

            <button type="button"
                    data-url="{{ route('siswa.hapus', $s->id_siswa) }}"
                    data-name="{{ $s->nama }}"
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
