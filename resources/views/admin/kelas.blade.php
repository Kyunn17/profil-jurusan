@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('content')
<div class="page-head">
    <div>
        <h1>Data Kelas</h1>
        <p>Klik ruang kelas untuk melihat detail jurusan dan jumlah siswa terdaftar.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('export.preview', ['type' => 'kelas']) }}" class="btn btn-outline btn-sm">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button type="button" id="toggleFormBtn" class="btn btn-sm">
            <i class="fas fa-plus"></i> Tambah Kelas
        </button>
        @endif
    </div>
</div>

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
            <button type="button" id="cancelBtn" class="btn btn-secondary btn-sm">Batal</button>
            <button type="submit" class="btn btn-sm"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
@endif

<div class="section-toolbar">
    <span class="section-count"><i class="fas fa-school"></i> {{ $kelas->count() }} Rombongan Belajar</span>
    <form method="GET" action="{{ route('kelas') }}" class="toolbar">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari kelas atau jurusan..." class="input-inline">
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
        @if(request('cari'))
            <a href="{{ route('kelas') }}" class="btn btn-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

@if($kelas->isEmpty())
    <div class="empty-panel">
        <i class="fas fa-school"></i>
        <strong>Belum ada data kelas.</strong>
        <p style="margin: 4px 0 0;">Tambahkan kelas baru untuk mulai mengelola rombel belajar.</p>
    </div>
@else
    <div class="room-grid">
        @foreach($kelas as $k)
        @php
            $siswaCount = $k->siswa_count ?? 0;
            $detail = [
                'initials' => strtoupper(substr(preg_replace('/\s+/', '', (string) $k->nama_kelas), 0, 2) ?: 'KL'),
                'title' => $k->nama_kelas,
                'subtitle' => $k->jurusan ?? 'Umum',
                'badge' => $siswaCount . ' Siswa',
                'badgeClass' => 'pill-hadir',
                'fields' => [
                    ['label' => 'Nama Kelas', 'value' => $k->nama_kelas],
                    ['label' => 'Kompetensi Keahlian', 'value' => $k->jurusan ?? 'Umum'],
                    ['label' => 'Jumlah Siswa Terdaftar', 'value' => $siswaCount . ' siswa', 'mono' => true],
                ],
                'editUrl' => Auth::user()->role === 'admin' ? route('kelas', ['edit' => $k->id_kelas]) : null,
                'deleteUrl' => Auth::user()->role === 'admin' ? route('kelas.hapus', $k->id_kelas) : null,
                'deleteName' => $k->nama_kelas,
            ];
        @endphp
        <div class="room-tile" role="button" tabindex="0" data-detail="{{ json_encode($detail) }}" onclick="openDetailModal(this)">
            <div class="room-tile-top">
                <div>
                    <h3>{{ $k->nama_kelas }}</h3>
                    <p>{{ $k->jurusan ?? 'Umum' }}</p>
                </div>
                <i class="fas fa-chevron-right roster-chevron"></i>
            </div>
            <div class="room-tile-foot">
                <span><i class="fas fa-users" style="color: var(--brand); margin-right: 5px;"></i> <span class="mono">{{ $siswaCount }}</span> Siswa</span>
                <span class="status-pill pill-hadir">Aktif</span>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
