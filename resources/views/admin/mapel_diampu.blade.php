@extends('layouts.admin')

@section('title', 'Distribusi Pengajaran')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 class="page-title">Distribusi Pengajaran</h2>
        <p class="page-subtitle">Plotting guru pengajar, mata pelajaran, dan kelas.</p>
    </div>
    <div class="header-btn-group">
        <a href="{{ route('export.preview', ['type' => 'mapel_diampu']) }}" class="btn btn-outline">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button id="toggleFormBtn" class="btn">
            <i class="fas fa-network-wired"></i> Atur Jadwal
        </button>
        @endif
    </div>
</div>

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-chalkboard-teacher"></i> {{ $edit ? 'Edit Data Pengajaran' : 'Tambah Data Pengajaran' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('mapel_diampu.update') : route('mapel_diampu.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_ampu" value="{{ $edit->id_ampu }}"> @endif

        <div class="form-grid-3">
            <div class="form-group">
                <label>Guru Pengajar</label>
                <select name="id_guru" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($guru as $g)
                    <option value="{{ $g->id_guru }}" {{ (string) old('id_guru', $edit->id_guru ?? '') === (string) $g->id_guru ? 'selected' : '' }}>
                        {{ $g->nama }} ({{ $g->nip ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Mata Pelajaran</label>
                <select name="id_mapel" required>
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($mapel as $m)
                    <option value="{{ $m->id_mapel }}" {{ (string) old('id_mapel', $edit->id_mapel ?? '') === (string) $m->id_mapel ? 'selected' : '' }}>
                        {{ $m->nama_mapel }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Target Kelas</label>
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

        <div class="form-actions">
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Data</button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @forelse($ampu as $a)
    <div class="student-card">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($a->guru->nama ?? 'X') }}&background=dcfce7&color=15803d&bold=true"
             class="student-avatar" alt="{{ $a->guru->nama ?? 'Guru' }}">

        <h3 class="student-name">{{ $a->guru->nama ?? 'Guru Dihapus' }}</h3>

        <span class="student-nisn">
            NIP: {{ $a->guru->nip ?? '-' }}
        </span>

        <div class="teaching-badge-container">
            <div class="t-badge tb-mapel">
                <i class="fas fa-book"></i> {{ $a->mapel->nama_mapel ?? 'Mapel Dihapus' }}
            </div>

            <div class="arrow-divider"><i class="fas fa-arrow-down"></i> Mengajar Di</div>

            <div class="t-badge tb-kelas">
                <i class="fas fa-users"></i> {{ $a->kelas->nama_kelas ?? 'Kelas Dihapus' }}
            </div>
        </div>

        @if(Auth::user()->role === 'admin')
        <div class="card-actions">
            <a href="{{ route('mapel_diampu', ['edit' => $a->id_ampu]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>

            <button type="button"
                    data-url="{{ route('mapel_diampu.hapus', $a->id_ampu) }}"
                    data-name="Jadwal {{ $a->guru->nama ?? 'Guru' }}"
                    onclick="openDeleteModal(this)"
                    class="btn-icon btn-delete" title="Hapus">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-network-wired"></i>
        <strong>Belum ada jadwal pengajaran.</strong>
        <p style="margin: 6px 0 0;">Atur plotting guru, mapel, dan kelas di sini.</p>
    </div>
    @endforelse
</div>
@endsection
