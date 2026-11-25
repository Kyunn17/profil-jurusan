@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-head">
    <div>
        <h1>Selamat datang, {{ Auth::user()->name }}</h1>
        <p>Ringkasan data akademik SMKN 4 Padalarang · Peran: <strong style="color: var(--brand); text-transform: uppercase;">{{ Auth::user()->role }}</strong></p>
    </div>
    <div class="page-actions">
        <a href="{{ route('absensi') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-calendar-check"></i> Buka Presensi
        </a>
        <a href="{{ route('berita') }}" class="btn btn-sm">
            <i class="fas fa-newspaper"></i> Portal Berita
        </a>
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
            <p>Pilih nama siswa Anda satu kali untuk menautkan akun dan mencatat kehadiran hari ini.</p>
        @endif
    </div>

    <div class="checkin-actions">
        @if($todayAbsensi)
            <span class="status-pill pill-{{ $todayAbsensi->status }}">
                <span class="status-dot"></span>
                Sudah Absen: {{ strtoupper($todayAbsensi->status) }}
            </span>
        @else
            <form method="POST" action="{{ route('absensi.checkin') }}" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                @csrf
                @if(!$currentSiswa)
                    <select name="id_siswa" class="input-inline" required style="min-width: 220px;">
                        <option value="">-- Pilih Data Siswa Anda --</option>
                        @foreach($siswaList as $s)
                            <option value="{{ $s->id_siswa }}">{{ $s->nama }} ({{ $s->kelas->nama_kelas ?? '-' }})</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" name="status" value="hadir" class="btn">
                    <i class="fas fa-check"></i> Absen Hadir Sekarang
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

<div class="kpi-strip">
    <a href="{{ route('siswa') }}" class="kpi-cell">
        <div class="kpi-label"><span>Siswa</span><i class="fas fa-user-graduate"></i></div>
        <div class="kpi-value">{{ $jumlahSiswa ?? 0 }}</div>
    </a>
    <a href="{{ route('guru') }}" class="kpi-cell">
        <div class="kpi-label"><span>Guru</span><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="kpi-value">{{ $jumlahGuru ?? 0 }}</div>
    </a>
    <a href="{{ route('kelas') }}" class="kpi-cell">
        <div class="kpi-label"><span>Kelas</span><i class="fas fa-school"></i></div>
        <div class="kpi-value">{{ $jumlahKelas ?? 0 }}</div>
    </a>
    <a href="{{ route('mapel') }}" class="kpi-cell">
        <div class="kpi-label"><span>Mapel</span><i class="fas fa-book"></i></div>
        <div class="kpi-value">{{ $jumlahMapel ?? 0 }}</div>
    </a>
    <a href="{{ route('nilai') }}" class="kpi-cell">
        <div class="kpi-label"><span>Nilai</span><i class="fas fa-award"></i></div>
        <div class="kpi-value">{{ $jumlahNilai ?? 0 }}</div>
    </a>
    <a href="{{ route('absensi') }}" class="kpi-cell">
        <div class="kpi-label"><span>Presensi</span><i class="fas fa-calendar-check"></i></div>
        <div class="kpi-value">{{ $jumlahAbsensi ?? 0 }}</div>
    </a>
</div>

<div class="dashboard-split">
    <section class="panel" style="margin-bottom: 0;">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-calendar-day"></i> Presensi Hari Ini</h2>
            <a href="{{ route('absensi') }}" class="btn btn-outline btn-xs">Kelola Presensi</a>
        </div>

        <div class="panel-body" style="padding-bottom: 14px; border-bottom: 1px solid var(--border);">
            <div class="mini-stats" style="margin-bottom: 0;">
                <div class="mini-stat-box">
                    <span>Hadir</span>
                    <strong style="color: var(--hadir-ink);">{{ $todayCounts['hadir'] ?? 0 }}</strong>
                </div>
                <div class="mini-stat-box">
                    <span>Izin</span>
                    <strong style="color: var(--izin-ink);">{{ $todayCounts['izin'] ?? 0 }}</strong>
                </div>
                <div class="mini-stat-box">
                    <span>Sakit</span>
                    <strong style="color: var(--sakit-ink);">{{ $todayCounts['sakit'] ?? 0 }}</strong>
                </div>
                <div class="mini-stat-box">
                    <span>Alpha</span>
                    <strong style="color: var(--alpha-ink);">{{ $todayCounts['alpha'] ?? 0 }}</strong>
                </div>
            </div>
        </div>

        @forelse($recentAbsensi as $ra)
        @php
            $sName = $ra->siswa->nama ?? 'Siswa Dihapus';
            $parts = preg_split('/\s+/', trim($sName)) ?: ['S'];
            $ini = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
            $canManage = in_array(Auth::user()->role, ['admin', 'guru'], true);
            $raDetail = [
                'initials' => $ini,
                'title' => $sName,
                'subtitle' => 'Presensi ' . \Carbon\Carbon::parse($ra->tanggal)->format('d M Y'),
                'badge' => strtoupper($ra->status),
                'badgeClass' => 'pill-' . $ra->status,
                'fields' => [
                    ['label' => 'Siswa', 'value' => $sName],
                    ['label' => 'NISN', 'value' => $ra->siswa->nisn ?? '-', 'mono' => true],
                    ['label' => 'Kelas', 'value' => $ra->kelas->nama_kelas ?? '-'],
                    ['label' => 'Tanggal', 'value' => \Carbon\Carbon::parse($ra->tanggal)->format('d M Y'), 'mono' => true],
                ],
                'editUrl' => $canManage ? route('absensi', ['edit' => $ra->id_absensi]) : null,
                'deleteUrl' => $canManage ? route('absensi.hapus', $ra->id_absensi) : null,
                'deleteName' => $sName,
            ];
        @endphp
        <div class="timeline-item" role="button" tabindex="0" data-detail="{{ json_encode($raDetail) }}" onclick="openDetailModal(this)">
            <div class="cell-person">
                <span class="initials">{{ $ini }}</span>
                <div>
                    <strong>{{ $sName }}</strong>
                    <small>{{ $ra->kelas->nama_kelas ?? '-' }} · <span class="mono">{{ \Carbon\Carbon::parse($ra->tanggal)->format('d M Y') }}</span></small>
                </div>
            </div>
            <div class="roster-right">
                <span class="status-pill pill-{{ $ra->status }}">
                    <span class="status-dot"></span> {{ $ra->status }}
                </span>
                <i class="fas fa-chevron-right roster-chevron"></i>
            </div>
        </div>
        @empty
        <div class="panel-body" style="text-align: center; padding: 32px 18px; color: var(--ink-3);">
            Belum ada catatan presensi terbaru.
        </div>
        @endforelse
    </section>

    <section class="panel" style="margin-bottom: 0;">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-newspaper"></i> Berita Sekolah Terbaru</h2>
            <a href="{{ route('berita') }}" class="btn btn-outline btn-xs">Lihat Semua</a>
        </div>

        @forelse($recentBerita as $rb)
        @php
            $canManageNews = Auth::id() === $rb->id_user || Auth::user()->role === 'admin';
            $rbDetail = [
                'title' => $rb->judul,
                'subtitle' => 'Oleh ' . ($rb->user?->name ?? 'Admin') . ' · ' . \Carbon\Carbon::parse($rb->tanggal_post)->format('d M Y'),
                'badge' => 'Published',
                'badgeClass' => 'pill-published',
                'image' => $rb->foto ? 'data:image/jpeg;base64,' . base64_encode($rb->foto) : null,
                'body' => strip_tags((string) $rb->isi),
                'fields' => [
                    ['label' => 'Penulis', 'value' => $rb->user?->name ?? 'Admin'],
                    ['label' => 'Tanggal Terbit', 'value' => \Carbon\Carbon::parse($rb->tanggal_post)->format('d M Y'), 'mono' => true],
                ],
                'editUrl' => $canManageNews ? route('berita', ['edit' => $rb->id_berita]) : null,
                'deleteUrl' => $canManageNews ? route('berita.hapus', $rb->id_berita) : null,
                'deleteName' => $rb->judul,
            ];
        @endphp
        <div class="timeline-item" role="button" tabindex="0" data-detail="{{ json_encode($rbDetail) }}" onclick="openDetailModal(this)">
            <div>
                <strong style="display: block; font-size: 0.88rem; color: var(--ink);">{{ Str::limit($rb->judul, 52) }}</strong>
                <small style="color: var(--ink-3); font-size: 0.76rem;">
                    {{ $rb->user?->name ?? 'Admin' }} · <span class="mono">{{ \Carbon\Carbon::parse($rb->tanggal_post)->format('d M Y') }}</span>
                </small>
            </div>
            <i class="fas fa-chevron-right roster-chevron"></i>
        </div>
        @empty
        <div class="panel-body" style="text-align: center; padding: 32px 18px; color: var(--ink-3);">
            Belum ada berita diterbitkan.
        </div>
        @endforelse
    </section>
</div>
@endsection
