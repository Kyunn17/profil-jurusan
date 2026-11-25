@extends('layouts.admin')

@section('title', 'Rekap Absensi')

@push('styles')
<style>
    .card-hadir { border-top: 4px solid var(--success); }
    .card-izin  { border-top: 4px solid var(--primary); }
    .card-sakit { border-top: 4px solid var(--warning); }
    .card-alpha { border-top: 4px solid var(--danger); }

    .status-badge-lg {
        padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.85rem;
        display: inline-block; margin-bottom: 10px; text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="page-header-actions">
    <div>
        <h2 style="color: var(--danger); margin: 0; font-size: 2rem;">Log Absensi</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0;">Monitoring kehadiran siswa realtime.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('export.preview', ['type' => 'absensi']) }}" class="btn" style="background: var(--success);">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
        <button id="toggleFormBtn" class="btn" style="background: var(--danger);">
            <i class="fas fa-calendar-check"></i> Catat Kehadiran
        </button>
        @endif
    </div>
</div>

@if(in_array(Auth::user()->role, ['admin', 'guru'], true))
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }}; border-color: var(--danger);">
    <h3 style="color: white; border-bottom: 1px solid #334155; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="fas fa-user-clock"></i> {{ $edit ? 'Edit Data Absensi' : 'Input Absensi Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('absensi.update') : route('absensi.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_absensi" value="{{ $edit->id_absensi }}"> @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div class="form-group">
                    <label style="color: var(--danger);">Siswa</label>
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
                    <label style="color: var(--danger);">Kelas</label>
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
                    <label style="color: var(--danger);">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $edit->tanggal ?? date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label style="color: var(--danger);">Status Kehadiran</label>
                    <select name="status" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="hadir" {{ old('status', $edit->status ?? '') === 'hadir' ? 'selected' : '' }}>✅ Hadir</option>
                        <option value="izin" {{ old('status', $edit->status ?? '') === 'izin' ? 'selected' : '' }}>📩 Izin</option>
                        <option value="sakit" {{ old('status', $edit->status ?? '') === 'sakit' ? 'selected' : '' }}>💊 Sakit</option>
                        <option value="alpha" {{ old('status', $edit->status ?? '') === 'alpha' ? 'selected' : '' }}>❌ Alpha</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="button" id="cancelBtn" class="btn btn-secondary" style="margin-right: 10px;">Batal</button>
            <button type="submit" class="btn" style="background: var(--danger);">Simpan Data</button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @foreach($absensi as $a)
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
        <div style="position: absolute; top: 15px; right: 15px; font-size: 0.8rem; color: var(--text-muted);">
            <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($a->tanggal)->format('d M') }}
        </div>

        <img src="https://ui-avatars.com/api/?name={{ urlencode($a->siswa->nama ?? 'X') }}&background=0f172a&color=fff"
             class="student-avatar" style="border-color: #334155;">

        <h3 class="student-name">{{ $a->siswa->nama ?? 'Siswa Dihapus' }}</h3>

        <span class="status-badge-lg {{ $statusColor }}">
            {{ $statusLabel }}
        </span>

        <div class="student-details" style="justify-content: space-around;">
            <span title="Kelas"><i class="fas fa-users"></i> {{ $a->kelas->nama_kelas ?? '-' }}</span>
            <span title="NISN"><i class="fas fa-id-card"></i> {{ $a->siswa->nisn ?? '-' }}</span>
        </div>

        @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
        <div class="card-actions" style="margin-top: 15px;">
            <a href="{{ route('absensi', ['edit' => $a->id_absensi]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>
            <button type="button"
                    data-url="{{ route('absensi.hapus', $a->id_absensi) }}"
                    data-name="{{ $a->siswa->nama ?? 'Siswa ini' }}"
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
