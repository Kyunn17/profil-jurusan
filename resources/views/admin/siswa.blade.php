@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="page-head">
    <div>
        <h1>Data Siswa</h1>
        <p>Klik salah satu siswa untuk melihat profil lengkap, kontak, dan kelas.</p>
    </div>

    <div class="page-actions">
        <a href="{{ route('export.preview', ['type' => 'siswa']) }}" class="btn btn-outline btn-sm">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button type="button" id="toggleFormBtn" class="btn btn-sm">
            <i class="fas fa-user-plus"></i> Tambah Siswa
        </button>
        @endif
    </div>
</div>

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
            <button type="button" id="cancelBtn" class="btn btn-secondary btn-sm">Batal</button>
            <button type="submit" class="btn btn-sm">
                <i class="fas fa-save"></i> {{ $edit ? 'Perbarui Data' : 'Simpan Data' }}
            </button>
        </div>
    </form>
</div>
@endif

<div class="section-toolbar">
    <span class="section-count"><i class="fas fa-user-graduate"></i> {{ $siswa->count() }} Siswa Terdaftar</span>
    <form method="GET" action="{{ route('siswa') }}" class="toolbar">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau NISN..." class="input-inline">
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
        @if(request('cari'))
            <a href="{{ route('siswa') }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

@if($siswa->isEmpty())
    <div class="empty-panel">
        <i class="fas fa-user-graduate"></i>
        <strong>Belum ada data siswa.</strong>
        <p style="margin: 4px 0 0;">Tambahkan siswa baru untuk mulai mengelola data induk siswa.</p>
    </div>
@else
    <div class="roster-grid">
        @foreach($siswa as $s)
        @php
            $parts = preg_split('/\s+/', trim((string) $s->nama)) ?: ['S'];
            $ini = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
            $detail = [
                'initials' => $ini,
                'title' => $s->nama,
                'subtitle' => 'NISN: ' . $s->nisn,
                'badge' => $s->kelas->nama_kelas ?? 'Tanpa Kelas',
                'badgeClass' => 'pill-hadir',
                'fields' => [
                    ['label' => 'NISN', 'value' => $s->nisn, 'mono' => true],
                    ['label' => 'Kelas', 'value' => $s->kelas->nama_kelas ?? '-'],
                    ['label' => 'Jurusan', 'value' => $s->kelas->jurusan ?? '-'],
                    ['label' => 'Jenis Kelamin', 'value' => $s->jenis_kelamin],
                    ['label' => 'Tanggal Lahir', 'value' => $s->tanggal_lahir ? \Carbon\Carbon::parse($s->tanggal_lahir)->format('d M Y') : '-', 'mono' => true],
                    ['label' => 'No. HP', 'value' => $s->no_hp ?? '-', 'mono' => true],
                    ['label' => 'Alamat', 'value' => $s->alamat ?? '-'],
                ],
                'editUrl' => Auth::user()->role === 'admin' ? route('siswa', ['edit' => $s->id_siswa]) : null,
                'deleteUrl' => Auth::user()->role === 'admin' ? route('siswa.hapus', $s->id_siswa) : null,
                'deleteName' => $s->nama,
            ];
        @endphp
        <div class="roster-item" role="button" tabindex="0" data-detail="{{ json_encode($detail) }}" onclick="openDetailModal(this)">
            <span class="initials">{{ $ini }}</span>
            <div class="roster-main">
                <strong>{{ $s->nama }}</strong>
                <small class="mono">{{ $s->nisn }} · {{ $s->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</small>
            </div>
            <div class="roster-right">
                <span class="status-pill pill-neutral">{{ $s->kelas->nama_kelas ?? 'N/A' }}</span>
                <i class="fas fa-chevron-right roster-chevron"></i>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
