@extends('layouts.admin')

@section('title', 'Distribusi Pengajaran')

@push('styles')
<style>
    .teaching-badge-container {
        background: #0f172a; border-radius: 8px; padding: 15px; margin: 15px 0;
        border: 1px solid #334155; display: flex; flex-direction: column; gap: 10px;
    }
    .t-badge { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px; border-radius: 6px; font-size: 0.9rem; font-weight: bold; }
    .tb-mapel { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .tb-kelas { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
    .arrow-divider { color: var(--text-muted); font-size: 0.8rem; opacity: 0.5; }
</style>
@endpush

@section('content')
<div class="page-header-actions">
    <div>
        <h2 style="color: var(--purple); margin: 0; font-size: 2rem;">Distribusi Pengajaran</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0;">Plotting Guru, Mapel, dan Kelas.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('export.preview', ['type' => 'mapel_diampu']) }}" class="btn" style="background: var(--success);">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button id="toggleFormBtn" class="btn" style="background: var(--purple);">
            <i class="fas fa-network-wired"></i> Atur Jadwal
        </button>
        @endif
    </div>
</div>

@if(Auth::user()->role === 'admin')
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }}; border-color: var(--purple);">
    <h3 style="color: white; border-bottom: 1px solid #334155; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="fas fa-chalkboard-teacher"></i> {{ $edit ? 'Edit Data Pengajaran' : 'Tambah Data Pengajaran' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('mapel_diampu.update') : route('mapel_diampu.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_ampu" value="{{ $edit->id_ampu }}"> @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label style="color: var(--purple);">Guru Pengajar</label>
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
                <label style="color: var(--purple);">Mata Pelajaran</label>
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
                <label style="color: var(--purple);">Target Kelas</label>
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

        <div style="margin-top: 20px; text-align: right;">
            <button type="button" id="cancelBtn" class="btn btn-secondary" style="margin-right: 10px;">Batal</button>
            <button type="submit" class="btn" style="background: var(--purple);">Simpan Data</button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @foreach($ampu as $a)
    <div class="student-card" style="border-top: 4px solid var(--purple);">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($a->guru->nama ?? 'X') }}&background=0f172a&color=a78bfa&bold=true"
             class="student-avatar" style="border-color: var(--purple);">

        <h3 class="student-name">{{ $a->guru->nama ?? 'Guru Dihapus' }}</h3>

        <span class="student-nisn" style="background: rgba(139, 92, 246, 0.1); color: var(--purple); border: 1px solid var(--purple);">
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
                    class="btn-icon btn-delete" style="border:none;" title="Hapus">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endsection
