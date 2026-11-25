@extends('layouts.admin')

@section('title', 'Rekapitulasi Nilai')

@section('content')
<div class="page-head">
    <div>
        <h1>Data Nilai Siswa</h1>
        <p>Klik baris KHS siswa untuk melihat rincian nilai, predikat, dan guru pengampu.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('export.preview', ['type' => 'nilai']) }}" class="btn btn-outline btn-sm">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
        <button type="button" id="toggleFormBtn" class="btn btn-sm">
            <i class="fas fa-file-signature"></i> Input Nilai
        </button>
        @endif
    </div>
</div>

@if(in_array(Auth::user()->role, ['admin', 'guru'], true))
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }};">
    <h3 class="form-panel-title">
        <i class="fas fa-edit"></i> {{ $edit ? 'Edit Nilai' : 'Input Nilai Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('nilai.update') : route('nilai.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_nilai" value="{{ $edit->id_nilai }}"> @endif

        <div class="form-grid-3" style="margin-bottom: 14px;">
            <div class="form-group">
                <label>Nama Siswa</label>
                <select name="id_siswa" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswa as $s)
                    <option value="{{ $s->id_siswa }}" {{ (string) old('id_siswa', $edit->id_siswa ?? '') === (string) $s->id_siswa ? 'selected' : '' }}>
                        {{ $s->nama }} ({{ $s->kelas->nama_kelas ?? '' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Mata Pelajaran</label>
                <select name="id_ampu" required>
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($ampu as $a)
                    <option value="{{ $a->id_ampu }}" {{ (string) old('id_ampu', $edit->id_ampu ?? '') === (string) $a->id_ampu ? 'selected' : '' }}>
                        {{ $a->mapel->nama_mapel ?? '-' }} — {{ $a->kelas->nama_kelas ?? '-' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Semester</label>
                <select name="semester" required>
                    <option value="Ganjil" {{ old('semester', $edit->semester ?? '') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ old('semester', $edit->semester ?? '') === 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>
        </div>

        <div style="background: var(--surface-muted); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
            <div class="form-grid-3">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Nilai Tugas</label>
                    <input type="number" step="0.01" min="0" max="100" id="input_tugas" name="nilai_tugas"
                           value="{{ old('nilai_tugas', $edit->nilai_tugas ?? 0) }}" oninput="hitungRataRata()" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Nilai UTS</label>
                    <input type="number" step="0.01" min="0" max="100" id="input_uts" name="nilai_uts"
                           value="{{ old('nilai_uts', $edit->nilai_uts ?? 0) }}" oninput="hitungRataRata()" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--brand);">Nilai Akhir (Rata-rata)</label>
                    <input type="text" id="input_akhir"
                           value="{{ old('nilai_akhir', $edit->nilai_akhir ?? 0) }}" readonly>
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
    <span class="section-count"><i class="fas fa-award"></i> {{ $nilai->count() }} Rekaman Nilai Akademik</span>
</div>

@if($nilai->isEmpty())
    <div class="empty-panel">
        <i class="fas fa-award"></i>
        <strong>Belum ada data nilai.</strong>
        <p style="margin: 4px 0 0;">Nilai siswa akan tampil di sini setelah diinput oleh guru atau admin.</p>
    </div>
@else
    <div class="score-list">
        @foreach($nilai as $n)
        @php
            $sName = $n->siswa->nama ?? 'Siswa Dihapus';
            $parts = preg_split('/\s+/', trim($sName)) ?: ['S'];
            $ini = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
            $akhir = (float) $n->nilai_akhir;
            $predikat = $akhir >= 85 ? 'A (Sangat Baik)' : ($akhir >= 75 ? 'B (Baik)' : ($akhir >= 60 ? 'C (Cukup)' : 'D (Perlu Perbaikan)'));
            $canManage = in_array(Auth::user()->role, ['admin', 'guru'], true);
            $detail = [
                'initials' => $ini,
                'title' => $sName,
                'subtitle' => ($n->ampu->mapel->nama_mapel ?? 'Mapel Dihapus') . ' · Semester ' . $n->semester,
                'badge' => 'Akhir: ' . $n->nilai_akhir,
                'badgeClass' => $akhir >= 75 ? 'pill-hadir' : 'pill-sakit',
                'fields' => [
                    ['label' => 'Siswa', 'value' => $sName],
                    ['label' => 'NISN', 'value' => $n->siswa->nisn ?? '-', 'mono' => true],
                    ['label' => 'Kelas', 'value' => $n->ampu->kelas->nama_kelas ?? '-'],
                    ['label' => 'Mata Pelajaran', 'value' => $n->ampu->mapel->nama_mapel ?? 'Mapel Dihapus'],
                    ['label' => 'Guru Pengajar', 'value' => $n->ampu->guru->nama ?? 'Guru Dihapus'],
                    ['label' => 'Semester', 'value' => $n->semester],
                    ['label' => 'Nilai Tugas', 'value' => (string) $n->nilai_tugas, 'mono' => true],
                    ['label' => 'Nilai UTS', 'value' => (string) $n->nilai_uts, 'mono' => true],
                    ['label' => 'Nilai Akhir', 'value' => (string) $n->nilai_akhir, 'mono' => true],
                    ['label' => 'Predikat', 'value' => $predikat],
                ],
                'editUrl' => $canManage ? route('nilai', ['edit' => $n->id_nilai]) : null,
                'deleteUrl' => $canManage ? route('nilai.hapus', $n->id_nilai) : null,
                'deleteName' => $sName . ' - ' . ($n->ampu->mapel->nama_mapel ?? 'Mapel'),
            ];
        @endphp
        <div class="score-strip" role="button" tabindex="0" data-detail="{{ json_encode($detail) }}" onclick="openDetailModal(this)">
            <div class="score-strip-left">
                <span class="initials">{{ $ini }}</span>
                <div class="score-strip-meta">
                    <strong>{{ $sName }}</strong>
                    <small>{{ $n->ampu->mapel->nama_mapel ?? 'Mapel Dihapus' }} · {{ $n->ampu->guru->nama ?? 'Guru Dihapus' }} · <span class="mono">{{ $n->semester }}</span></small>
                </div>
            </div>

            <div class="score-strip-metrics">
                <div class="score-pill-box">
                    <span>Tugas</span>
                    <strong>{{ $n->nilai_tugas }}</strong>
                </div>
                <div class="score-pill-box">
                    <span>UTS</span>
                    <strong>{{ $n->nilai_uts }}</strong>
                </div>
                <div class="score-pill-box score-pill-final">
                    <span>Akhir</span>
                    <strong>{{ $n->nilai_akhir }}</strong>
                </div>
                <i class="fas fa-chevron-right roster-chevron" style="margin-left: 4px;"></i>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection

@push('scripts')
<script>
    function hitungRataRata() {
        const tugas = parseFloat(document.getElementById('input_tugas')?.value) || 0;
        const uts = parseFloat(document.getElementById('input_uts')?.value) || 0;
        const akhir = document.getElementById('input_akhir');
        if (akhir) {
            akhir.value = ((tugas + uts) / 2).toFixed(2);
        }
    }
    hitungRataRata();
</script>
@endpush
