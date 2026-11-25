@extends('layouts.admin')

@section('title', 'Rekap Absensi')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 class="page-title">Log Absensi</h2>
        <p class="page-subtitle">Monitoring kehadiran siswa secara realtime.</p>
    </div>
    <div class="header-btn-group">
        <a href="{{ route('export.preview', ['type' => 'absensi']) }}" class="btn btn-outline">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
        <button id="toggleFormBtn" class="btn">
            <i class="fas fa-calendar-check"></i> Catat Kehadiran
        </button>
        @endif
    </div>
</div>

@if(in_array(Auth::user()->role, ['admin', 'guru'], true))
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-user-clock"></i> {{ $edit ? 'Edit Data Absensi' : 'Input Absensi Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('absensi.update') : route('absensi.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_absensi" value="{{ $edit->id_absensi }}"> @endif

        <div class="form-grid-2">
            <div>
                <div class="form-group">
                    <label>Siswa</label>
                    <select name="id_siswa" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswa as $s)
                        <option value="{{ $s->id_siswa }}" {{ (string) old('id_siswa', $edit->id_siswa ?? '') === (string) $s->id_siswa ? 'selected' : '' }}>
                            {{ $s->nama }} ({{ $s->kelas->nama_kelas ?? '-' }})
                        </option>
                        @endforeach
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
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $edit->tanggal ?? date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Status Kehadiran</label>
                    <select name="status" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="hadir" {{ old('status', $edit->status ?? '') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="izin" {{ old('status', $edit->status ?? '') === 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ old('status', $edit->status ?? '') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="alpha" {{ old('status', $edit->status ?? '') === 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>
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
    @forelse($absensi as $a)
    @php
        if ($a->status === 'hadir') {
            $statusClass = 'card-hadir'; $statusLabel = 'HADIR'; $statusColor = 'status-hadir';
        } elseif ($a->status === 'izin') {
            $statusClass = 'card-izin'; $statusLabel = 'IZIN'; $statusColor = 'status-izin';
        } elseif ($a->status === 'sakit') {
            $statusClass = 'card-sakit'; $statusLabel = 'SAKIT'; $statusColor = 'status-sakit';
        } else {
            $statusClass = 'card-alpha'; $statusLabel = 'ALPHA'; $statusColor = 'status-alpha';
        }
    @endphp

    <div class="student-card {{ $statusClass }}">
        <div style="position: absolute; top: 15px; right: 15px; font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
            <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($a->tanggal)->format('d M') }}
        </div>

        <img src="https://ui-avatars.com/api/?name={{ urlencode($a->siswa->nama ?? 'X') }}&background=dcfce7&color=15803d&bold=true"
             class="student-avatar" alt="{{ $a->siswa->nama ?? 'Siswa' }}">

        <h3 class="student-name">{{ $a->siswa->nama ?? 'Siswa Dihapus' }}</h3>

        <span class="status-badge-lg {{ $statusColor }}">
            {{ $statusLabel }}
        </span>

        <div class="student-details" style="justify-content: space-around;">
            <span title="Kelas"><i class="fas fa-users" style="color: var(--primary);"></i> {{ $a->kelas->nama_kelas ?? '-' }}</span>
            <span title="NISN"><i class="fas fa-id-card" style="color: var(--teal);"></i> {{ $a->siswa->nisn ?? '-' }}</span>
        </div>

        @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
        <div class="card-actions">
            <a href="{{ route('absensi', ['edit' => $a->id_absensi]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>
            <button type="button"
                    data-url="{{ route('absensi.hapus', $a->id_absensi) }}"
                    data-name="{{ $a->siswa->nama ?? 'Siswa ini' }}"
                    onclick="openDeleteModal(this)"
                    class="btn-icon btn-delete" title="Hapus">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-calendar-check"></i>
        <strong>Belum ada catatan absensi.</strong>
        <p style="margin: 6px 0 0;">Data kehadiran siswa akan tampil di sini.</p>
    </div>
    @endforelse
</div>
@endsection
