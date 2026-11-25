@extends('layouts.admin')

@section('title', 'Rekapitulasi Nilai')

@push('styles')
<style>
    .nilai-card {
        background: var(--bg-card); border-radius: 12px; padding: 20px;
        border: 1px solid #334155; border-top: 4px solid var(--purple);
        transition: transform 0.3s; position: relative;
    }
    .nilai-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(139, 92, 246, 0.2); }

    .card-header-flex {
        display: flex; align-items: center; gap: 15px; margin-bottom: 15px;
        padding-bottom: 15px; border-bottom: 1px dashed #334155;
    }
    .mini-avatar { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--purple); }

    .semester-badge {
        position: absolute; top: 15px; right: 15px;
        font-size: 0.7rem; font-weight: bold; text-transform: uppercase;
        padding: 3px 8px; border-radius: 10px;
    }
    .smt-ganjil { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid #38bdf8; }
    .smt-genap  { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid #f59e0b; }

    .score-grid {
        display: grid; grid-template-columns: 1fr 1fr 1.5fr; gap: 8px; margin-top: 15px;
    }
    .score-item {
        background: #0f172a; padding: 8px; border-radius: 8px;
        text-align: center; border: 1px solid #334155;
    }
    .score-label { font-size: 0.65rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px; }
    .score-val { font-weight: bold; color: white; font-family: 'Courier New', monospace; font-size: 1rem; }

    .score-final { background: rgba(16, 185, 129, 0.1); border-color: var(--success); }
    .score-final .score-label { color: var(--success); }
    .score-final .score-val { color: var(--success); font-size: 1.2rem; }
</style>
@endpush

@section('content')
<div class="page-header-actions">
    <div>
        <h2 style="color: var(--purple); margin: 0; font-size: 2rem;">Data Nilai Siswa</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0;">Kartu Hasil Studi (KHS) Digital.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('export.preview', ['type' => 'nilai']) }}" class="btn" style="background: var(--success);">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
        <button id="toggleFormBtn" class="btn" style="background: var(--purple);">
            <i class="fas fa-file-signature"></i> Input Nilai
        </button>
        @endif
    </div>
</div>

@if(in_array(Auth::user()->role, ['admin', 'guru'], true))
<div id="formWrapper" class="form-panel" style="display: {{ ($edit || $errors->any()) ? 'block' : 'none' }}; border-color: var(--purple);">
    <h3 style="color: white; border-bottom: 1px solid #334155; padding-bottom: 10px; margin-bottom: 20px;">
        <i class="fas fa-edit"></i> {{ $edit ? 'Edit Nilai' : 'Input Nilai Baru' }}
    </h3>

    <form method="POST" action="{{ $edit ? route('nilai.update') : route('nilai.store') }}">
        @csrf
        @if($edit) <input type="hidden" name="id_nilai" value="{{ $edit->id_nilai }}"> @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div class="form-group">
                <label style="color: var(--purple);">Nama Siswa</label>
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
                <label style="color: var(--purple);">Mata Pelajaran</label>
                <select name="id_ampu" required>
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($ampu as $a)
                    <option value="{{ $a->id_ampu }}" {{ (string) old('id_ampu', $edit->id_ampu ?? '') === (string) $a->id_ampu ? 'selected' : '' }}>
                        {{ $a->mapel->nama_mapel ?? '-' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label style="color: var(--purple);">Semester</label>
                <select name="semester" required>
                    <option value="Ganjil" {{ old('semester', $edit->semester ?? '') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ old('semester', $edit->semester ?? '') === 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 15px; border-radius: 8px; border: 1px solid #334155;">
            <label style="color: white; font-weight: bold; display: block; margin-bottom: 10px;">Rincian Nilai:</label>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Tugas</label>
                    <input type="number" step="0.01" min="0" max="100" id="input_tugas" name="nilai_tugas"
                           value="{{ old('nilai_tugas', $edit->nilai_tugas ?? 0) }}" oninput="hitungRataRata()" required>
                </div>

                <div class="form-group">
                    <label>UTS</label>
                    <input type="number" step="0.01" min="0" max="100" id="input_uts" name="nilai_uts"
                           value="{{ old('nilai_uts', $edit->nilai_uts ?? 0) }}" oninput="hitungRataRata()" required>
                </div>

                <div class="form-group">
                    <label style="color: var(--success);">Nilai Akhir (Rata²)</label>
                    <input type="text" id="input_akhir"
                           value="{{ old('nilai_akhir', $edit->nilai_akhir ?? 0) }}" readonly
                           style="border-color: var(--success); background: #064e3b; font-weight: bold;">
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; text-align: right;">
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn" style="background: var(--purple);">Simpan</button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @foreach($nilai as $n)
    <div class="nilai-card">
        <span class="semester-badge {{ $n->semester === 'Ganjil' ? 'smt-ganjil' : 'smt-genap' }}">
            {{ $n->semester }}
        </span>

        <div class="card-header-flex">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($n->siswa->nama ?? 'X') }}&background=0f172a&color=a78bfa" class="mini-avatar">
            <div>
                <h4 style="margin: 0; color: white; font-size: 1rem;">{{ $n->siswa->nama ?? 'Siswa Hapus' }}</h4>
                <small style="color: var(--text-muted);">{{ $n->siswa->nisn ?? '-' }}</small>
            </div>
        </div>

        <div style="margin-bottom: 10px;">
            <div style="font-weight: bold; color: var(--purple); display: flex; align-items: center; gap: 5px;">
                <i class="fas fa-book"></i> {{ $n->ampu->mapel->nama_mapel ?? 'Mapel Hapus' }}
            </div>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 3px;">
                <i class="fas fa-chalkboard-teacher"></i> {{ $n->ampu->guru->nama ?? 'Guru Hapus' }}
            </div>
        </div>

        <div class="score-grid">
            <div class="score-item">
                <span class="score-label">TUGAS</span>
                <span class="score-val">{{ $n->nilai_tugas }}</span>
            </div>
            <div class="score-item">
                <span class="score-label">UTS</span>
                <span class="score-val">{{ $n->nilai_uts }}</span>
            </div>
            <div class="score-item score-final">
                <span class="score-label">N. AKHIR</span>
                <span class="score-val">{{ $n->nilai_akhir }}</span>
            </div>
        </div>

        @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
        <div class="card-actions" style="margin-top: 15px; border-top: 1px solid #334155; padding-top: 15px;">
            <a href="{{ route('nilai', ['edit' => $n->id_nilai]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>
            <button type="button"
                    data-url="{{ route('nilai.hapus', $n->id_nilai) }}"
                    data-name="{{ ($n->siswa->nama ?? 'Siswa') . ' - ' . ($n->ampu->mapel->nama_mapel ?? 'Mapel') }}"
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
