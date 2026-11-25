@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="page-head">
    <div>
        <h1>Data Guru</h1>
        <p>Klik kartu pengajar untuk melihat detail NIP, email, dan kontak.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('export.preview', ['type' => 'guru']) }}" class="btn btn-outline btn-sm">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button type="button" id="toggleFormBtn" class="btn btn-sm">
            <i class="fas fa-chalkboard-teacher"></i> Tambah Guru
        </button>
        @endif
    </div>
</div>

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
            <button type="button" id="cancelBtn" class="btn btn-secondary btn-sm">Batal</button>
            <button type="submit" class="btn btn-sm"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
@endif

<div class="section-toolbar">
    <span class="section-count"><i class="fas fa-chalkboard-teacher"></i> {{ $guru->count() }} Tenaga Pendidik</span>
    <form method="GET" action="{{ route('guru') }}" class="toolbar">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau NIP..." class="input-inline">
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
        @if(request('cari'))
            <a href="{{ route('guru') }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

@if($guru->isEmpty())
    <div class="empty-panel">
        <i class="fas fa-chalkboard-teacher"></i>
        <strong>Belum ada data guru.</strong>
        <p style="margin: 4px 0 0;">Data tenaga pendidik akan muncul di sini setelah ditambahkan.</p>
    </div>
@else
    <div class="roster-grid">
        @foreach($guru as $g)
        @php
            $parts = preg_split('/\s+/', trim((string) $g->nama)) ?: ['G'];
            $ini = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
            $detail = [
                'initials' => $ini,
                'initialsClass' => 'initials-teal',
                'title' => $g->nama,
                'subtitle' => 'NIP: ' . ($g->nip ?? '-'),
                'badge' => $g->jenis_kelamin,
                'badgeClass' => 'pill-izin',
                'fields' => [
                    ['label' => 'NIP / NUPTK', 'value' => $g->nip ?? '-', 'mono' => true],
                    ['label' => 'Jenis Kelamin', 'value' => $g->jenis_kelamin],
                    ['label' => 'Email', 'value' => $g->email ?? '-'],
                    ['label' => 'No. HP', 'value' => $g->no_hp ?? '-', 'mono' => true],
                ],
                'editUrl' => Auth::user()->role === 'admin' ? route('guru', ['edit' => $g->id_guru]) : null,
                'deleteUrl' => Auth::user()->role === 'admin' ? route('guru.hapus', $g->id_guru) : null,
                'deleteName' => $g->nama,
            ];
        @endphp
        <div class="roster-item" role="button" tabindex="0" data-detail="{{ json_encode($detail) }}" onclick="openDetailModal(this)">
            <span class="initials initials-teal">{{ $ini }}</span>
            <div class="roster-main">
                <strong>{{ $g->nama }}</strong>
                <small>{{ $g->email ?? 'Belum ada email' }} · <span class="mono">{{ $g->nip ?? '-' }}</span></small>
            </div>
            <div class="roster-right">
                <span class="status-pill pill-izin">{{ $g->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</span>
                <i class="fas fa-chevron-right roster-chevron"></i>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
