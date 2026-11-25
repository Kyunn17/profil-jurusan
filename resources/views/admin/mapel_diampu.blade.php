@extends('layouts.admin')

@section('title', 'Distribusi Pengajaran')

@section('content')
<div class="page-head">
    <div>
        <h1>Distribusi Pengajaran</h1>
        <p>Klik kartu penugasan untuk melihat detail guru pengampu, mata pelajaran, dan kelas.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('export.preview', ['type' => 'mapel_diampu']) }}" class="btn btn-outline btn-sm">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(Auth::user()->role === 'admin')
        <button type="button" id="toggleFormBtn" class="btn btn-sm">
            <i class="fas fa-plus"></i> Atur Jadwal
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
            <button type="button" id="cancelBtn" class="btn btn-secondary btn-sm">Batal</button>
            <button type="submit" class="btn btn-sm"><i class="fas fa-save"></i> Simpan Data</button>
        </div>
    </form>
</div>
@endif

<div class="section-toolbar">
    <span class="section-count"><i class="fas fa-diagram-project"></i> {{ $ampu->count() }} Penugasan Pengajaran</span>
</div>

@if($ampu->isEmpty())
    <div class="empty-panel">
        <i class="fas fa-diagram-project"></i>
        <strong>Belum ada jadwal pengajaran.</strong>
        <p style="margin: 4px 0 0;">Atur plotting guru pengajar, mata pelajaran, dan kelas di sini.</p>
    </div>
@else
    <div class="pair-grid">
        @foreach($ampu as $a)
        @php
            $gName = $a->guru->nama ?? 'Guru Dihapus';
            $parts = preg_split('/\s+/', trim($gName)) ?: ['G'];
            $ini = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
            $detail = [
                'initials' => $ini,
                'initialsClass' => 'initials-teal',
                'title' => $a->mapel->nama_mapel ?? 'Mapel Dihapus',
                'subtitle' => 'Pengajar: ' . $gName,
                'badge' => $a->kelas->nama_kelas ?? 'Kelas Dihapus',
                'badgeClass' => 'pill-hadir',
                'fields' => [
                    ['label' => 'Mata Pelajaran', 'value' => $a->mapel->nama_mapel ?? 'Mapel Dihapus'],
                    ['label' => 'Kategori Mapel', 'value' => $a->mapel->kategori ?? '-'],
                    ['label' => 'Kelas', 'value' => $a->kelas->nama_kelas ?? 'Kelas Dihapus'],
                    ['label' => 'Jurusan', 'value' => $a->kelas->jurusan ?? '-'],
                    ['label' => 'Guru Pengajar', 'value' => $gName],
                    ['label' => 'NIP Guru', 'value' => $a->guru->nip ?? '-', 'mono' => true],
                ],
                'editUrl' => Auth::user()->role === 'admin' ? route('mapel_diampu', ['edit' => $a->id_ampu]) : null,
                'deleteUrl' => Auth::user()->role === 'admin' ? route('mapel_diampu.hapus', $a->id_ampu) : null,
                'deleteName' => 'Jadwal ' . $gName,
            ];
        @endphp
        <div class="pair-card" role="button" tabindex="0" data-detail="{{ json_encode($detail) }}" onclick="openDetailModal(this)">
            <div class="pair-head">
                <strong>{{ $a->mapel->nama_mapel ?? 'Mapel Dihapus' }}</strong>
                <span class="status-pill pill-hadir">{{ $a->kelas->nama_kelas ?? 'Kelas Dihapus' }}</span>
            </div>
            <div class="pair-body">
                <div class="cell-person">
                    <span class="initials initials-teal">{{ $ini }}</span>
                    <div>
                        <strong>{{ $gName }}</strong>
                        <small class="mono">NIP: {{ $a->guru->nip ?? '-' }}</small>
                    </div>
                </div>
                <i class="fas fa-chevron-right roster-chevron"></i>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
