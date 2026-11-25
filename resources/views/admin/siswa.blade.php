@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 class="page-title">Data Siswa</h2>
        <p class="page-subtitle">Kelola profil dan data siswa aktif dengan mudah.</p>
    </div>

    <div class="header-btn-group">
        <a href="{{ route('export.preview', ['type' => 'siswa']) }}" class="btn btn-outline">
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
<div class="alert-filter">
    <span><i class="fas fa-filter" style="color: var(--primary);"></i> Hasil pencarian untuk: <strong>{{ request('cari') }}</strong></span>
    <a href="{{ route('siswa') }}" class="btn btn-secondary btn-sm">Reset Pencarian</a>
</div>
@endif

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-pen-to-square"></i> {{ $edit ? 'Edit Data Siswa' : 'Registrasi Siswa Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('siswa.update') : route('siswa.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_siswa" value="{{ $edit->id_siswa }}"> @endif

        <div class="form-grid-2">
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

        <div class="form-actions">
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> {{ $edit ? 'Perbarui Data' : 'Simpan Data' }}
            </button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @forelse($siswa as $s)
    <div class="student-card">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($s->nama) }}&background=dcfce7&color=15803d&bold=true" class="student-avatar" alt="{{ $s->nama }}">

        <h3 class="student-name">{{ $s->nama }}</h3>
        <span class="student-nisn">{{ $s->nisn }}</span>

        <div class="student-details">
            <span title="Kelas"><i class="fas fa-users" style="color: var(--primary);"></i> {{ $s->kelas->nama_kelas ?? 'N/A' }}</span>
            <span title="Gender"><i class="fas fa-venus-mars" style="color: var(--primary);"></i> {{ $s->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</span>
        </div>

        <div class="student-details-sub">
            <span title="No HP"><i class="fas fa-phone" style="color: var(--teal);"></i> {{ $s->no_hp ?? '-' }}</span>
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
                    class="btn-icon btn-delete" title="Hapus">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-user-graduate"></i>
        <strong>Belum ada data siswa.</strong>
        <p style="margin: 6px 0 0;">Data siswa akan muncul di sini setelah ditambahkan.</p>
    </div>
    @endforelse
</div>
@endsection
