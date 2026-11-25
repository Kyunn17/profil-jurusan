@extends('layouts.admin')

@section('title', Auth::user()->role === 'siswa' ? 'Absensi Harian' : 'Rekap Absensi')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ Auth::user()->role === 'siswa' ? 'Absensi Harian' : 'Presensi & Kehadiran Siswa' }}</h1>
        <p>{{ Auth::user()->role === 'siswa' ? 'Catat kehadiran harian Anda dan klik item riwayat untuk melihat detail.' : 'Input roll-call harian per kelas. Klik log presensi di bawah untuk melihat detail.' }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('export.preview', ['type' => 'absensi']) }}" class="btn btn-outline btn-sm">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
        <button type="button" id="toggleFormBtn" class="btn btn-secondary btn-sm">
            <i class="fas fa-plus"></i> Input Manual
        </button>
        @endif
    </div>
</div>

@if(Auth::user()->role === 'siswa')
<div class="checkin-banner">
    <div class="checkin-info">
        <h3>
            <i class="fas fa-fingerprint" style="color: var(--brand); margin-right: 6px;"></i>
            Absen Hari Ini — <span class="mono">{{ \Carbon\Carbon::parse($today)->format('d M Y') }}</span>
        </h3>
        @if($currentSiswa)
            <p>{{ $currentSiswa->nama }} · <span class="mono">{{ $currentSiswa->nisn }}</span> · {{ $currentSiswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}</p>
        @else
            <p>Akun Anda belum terhubung ke data siswa. Pilih nama Anda di bawah untuk check-in hari ini.</p>
        @endif
    </div>

    <div class="checkin-actions">
        @if($todayAbsensi)
            <span class="status-pill pill-{{ $todayAbsensi->status }}">
                <span class="status-dot"></span>
                Sudah Absen Hari Ini ({{ strtoupper($todayAbsensi->status) }})
            </span>
        @else
            <form method="POST" action="{{ route('absensi.checkin') }}" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                @csrf
                @if(!$currentSiswa)
                    <select name="id_siswa" class="input-inline" required style="min-width: 220px;">
                        <option value="">-- Pilih Data Siswa Anda --</option>
                        @foreach($siswa as $s)
                            <option value="{{ $s->id_siswa }}">{{ $s->nama }} ({{ $s->kelas->nama_kelas ?? '-' }})</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" name="status" value="hadir" class="btn">
                    <i class="fas fa-check"></i> Absen Hadir Hari Ini
                </button>
                <button type="submit" name="status" value="izin" class="btn btn-outline">
                    Ajukan Izin
                </button>
                <button type="submit" name="status" value="sakit" class="btn btn-secondary">
                    Lapor Sakit
                </button>
            </form>
        @endif
    </div>
</div>
@endif

@if(in_array(Auth::user()->role, ['admin', 'guru'], true))
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-user-clock"></i> {{ $edit ? 'Edit Data Absensi' : 'Input Absensi Manual' }}
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
                    <input type="date" name="tanggal" value="{{ old('tanggal', $edit->tanggal ?? $today) }}" required>
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
            <button type="button" id="cancelBtn" class="btn btn-secondary btn-sm">Batal</button>
            <button type="submit" class="btn btn-sm"><i class="fas fa-save"></i> Simpan Data</button>
        </div>
    </form>
</div>

<section class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="fas fa-clipboard-list"></i>
            Presensi Harian (Roll-Call) — <span class="mono">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</span>
        </h2>

        <form method="GET" action="{{ route('absensi') }}" class="toolbar">
            <input type="date" name="tanggal" value="{{ $selectedDate }}" class="input-inline" style="min-width: 150px;">
            <select name="kelas" class="input-inline" style="min-width: 160px;">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id_kelas }}" {{ (string) $selectedKelas === (string) $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline btn-sm">Tampilkan</button>
            <button type="button" onclick="markAllHadir()" class="btn btn-secondary btn-sm">Tandai Semua Hadir</button>
        </form>
    </div>

    <div class="panel-body" style="padding-bottom: 8px;">
        <div class="mini-stats" style="margin-bottom: 0;">
            <div class="mini-stat-box"><span>Hadir</span><strong style="color: var(--hadir-ink);">{{ $todayCounts['hadir'] }}</strong></div>
            <div class="mini-stat-box"><span>Izin</span><strong style="color: var(--izin-ink);">{{ $todayCounts['izin'] }}</strong></div>
            <div class="mini-stat-box"><span>Sakit</span><strong style="color: var(--sakit-ink);">{{ $todayCounts['sakit'] }}</strong></div>
            <div class="mini-stat-box"><span>Alpha</span><strong style="color: var(--alpha-ink);">{{ $todayCounts['alpha'] }}</strong></div>
        </div>
    </div>

    <form method="POST" action="{{ route('absensi.bulk') }}">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $selectedDate }}">
        @if($selectedKelas) <input type="hidden" name="kelas" value="{{ $selectedKelas }}"> @endif

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th style="text-align: right;">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rollCallSiswa as $rs)
                    @php
                        $parts = preg_split('/\s+/', trim((string) $rs->nama)) ?: ['S'];
                        $ini = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                        $cur = $existingStatuses->get($rs->id_siswa, '');
                    @endphp
                    <tr>
                        <td>
                            <div class="cell-person">
                                <span class="initials">{{ $ini }}</span>
                                <strong>{{ $rs->nama }}</strong>
                            </div>
                        </td>
                        <td class="mono">{{ $rs->nisn }}</td>
                        <td><span class="status-pill pill-neutral">{{ $rs->kelas->nama_kelas ?? '-' }}</span></td>
                        <td style="text-align: right;">
                            <div class="seg-group">
                                @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $val => $label)
                                <div class="seg-option">
                                    <input type="radio"
                                           id="rc_{{ $rs->id_siswa }}_{{ $val }}"
                                           name="statuses[{{ $rs->id_siswa }}]"
                                           value="{{ $val }}"
                                           class="rollcall-radio-{{ $val }}"
                                           {{ $cur === $val ? 'checked' : '' }}>
                                    <label for="rc_{{ $rs->id_siswa }}_{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty-row">Tidak ada data siswa pada filter ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rollCallSiswa->count() > 0)
        <div style="padding: 14px 18px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; background: var(--surface-muted);">
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Simpan Presensi Harian
            </button>
        </div>
        @endif
    </form>
</section>
@endif

<section class="panel" style="margin-bottom: 0;">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="fas fa-clock-rotate-left"></i>
            {{ Auth::user()->role === 'siswa' ? 'Riwayat Presensi Saya' : 'Linimasa Log Presensi' }} ({{ $absensi->count() }})
        </h2>

        <form method="GET" action="{{ route('absensi') }}" class="toolbar">
            <input type="date" name="filter_tanggal" value="{{ request('filter_tanggal') }}" class="input-inline" style="min-width: 145px;">
            @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
            <select name="filter_kelas" class="input-inline" style="min-width: 145px;">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id_kelas }}" {{ (string) request('filter_kelas') === (string) $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
            @if(request()->hasAny(['filter_tanggal', 'filter_kelas']))
                <a href="{{ route('absensi') }}" class="btn btn-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>

    @if($absensi->isEmpty())
        <div class="panel-body" style="text-align: center; padding: 36px 20px; color: var(--ink-3);">
            Belum ada catatan absensi.
        </div>
    @else
        @php
            $groupedAbsensi = $absensi->groupBy('tanggal');
            $canManage = in_array(Auth::user()->role, ['admin', 'guru'], true);
        @endphp
        @foreach($groupedAbsensi as $tgl => $rows)
        <div class="timeline-group">
            <div class="timeline-date-label mono">
                {{ \Carbon\Carbon::parse($tgl)->format('d M Y') }} · {{ $rows->count() }} catatan
            </div>

            @foreach($rows as $a)
            @php
                $sName = $a->siswa->nama ?? 'Siswa Dihapus';
                $parts = preg_split('/\s+/', trim($sName)) ?: ['S'];
                $ini = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                $detail = [
                    'initials' => $ini,
                    'title' => $sName,
                    'subtitle' => 'Presensi Tanggal ' . \Carbon\Carbon::parse($a->tanggal)->format('d M Y'),
                    'badge' => strtoupper($a->status),
                    'badgeClass' => 'pill-' . $a->status,
                    'fields' => [
                        ['label' => 'Siswa', 'value' => $sName],
                        ['label' => 'NISN', 'value' => $a->siswa->nisn ?? '-', 'mono' => true],
                        ['label' => 'Kelas', 'value' => $a->kelas->nama_kelas ?? '-'],
                        ['label' => 'Tanggal', 'value' => \Carbon\Carbon::parse($a->tanggal)->format('d M Y'), 'mono' => true],
                        ['label' => 'Status Kehadiran', 'value' => strtoupper($a->status)],
                    ],
                    'editUrl' => $canManage ? route('absensi', ['edit' => $a->id_absensi]) : null,
                    'deleteUrl' => $canManage ? route('absensi.hapus', $a->id_absensi) : null,
                    'deleteName' => $sName,
                ];
            @endphp
            <div class="timeline-item" role="button" tabindex="0" data-detail="{{ json_encode($detail) }}" onclick="openDetailModal(this)">
                <div class="cell-person">
                    <span class="initials">{{ $ini }}</span>
                    <div>
                        <strong>{{ $sName }}</strong>
                        <small>{{ $a->kelas->nama_kelas ?? '-' }} · <span class="mono">{{ $a->siswa->nisn ?? '-' }}</span></small>
                    </div>
                </div>
                <div class="roster-right">
                    <span class="status-pill pill-{{ $a->status }}">
                        <span class="status-dot"></span> {{ $a->status }}
                    </span>
                    <i class="fas fa-chevron-right roster-chevron"></i>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    @endif
</section>
@endsection

@push('scripts')
<script>
    function markAllHadir() {
        document.querySelectorAll('.rollcall-radio-hadir').forEach(function (radio) {
            radio.checked = true;
        });
    }
</script>
@endpush
