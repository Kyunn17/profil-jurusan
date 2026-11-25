@extends('layouts.admin')

@section('title', 'Rekapitulasi Nilai')

@section('content')
<div class="page-header-actions">
    <div>
        <h2 class="page-title">Data Nilai Siswa</h2>
        <p class="page-subtitle">Kartu Hasil Studi (KHS) Digital siswa.</p>
    </div>
    <div class="header-btn-group">
        <a href="{{ route('export.preview', ['type' => 'nilai']) }}" class="btn btn-outline">
            <i class="fas fa-file-csv"></i> Preview Export
        </a>

        @if(in_array(Auth::user()->role, ['admin', 'guru'], true))
        <button id="toggleFormBtn" class="btn">
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

        <div class="form-grid-3" style="margin-bottom: 18px;">
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
                        {{ $a->mapel->nama_mapel ?? '-' }}
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

        <div style="background: var(--bg-subtle); padding: 18px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <label style="color: var(--text-main); font-weight: 700; display: block; margin-bottom: 12px;">Rincian Nilai:</label>

            <div class="form-grid-3">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Tugas</label>
                    <input type="number" step="0.01" min="0" max="100" id="input_tugas" name="nilai_tugas"
                           value="{{ old('nilai_tugas', $edit->nilai_tugas ?? 0) }}" oninput="hitungRataRata()" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>UTS</label>
                    <input type="number" step="0.01" min="0" max="100" id="input_uts" name="nilai_uts"
                           value="{{ old('nilai_uts', $edit->nilai_uts ?? 0) }}" oninput="hitungRataRata()" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label style="color: var(--primary);">Nilai Akhir (Rata-rata)</label>
                    <input type="text" id="input_akhir"
                           value="{{ old('nilai_akhir', $edit->nilai_akhir ?? 0) }}" readonly
                           style="border-color: var(--primary); background: var(--primary-soft);">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" id="cancelBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>
@endif

<div class="student-grid">
    @forelse($nilai as $n)
    <div class="nilai-card">
        <span class="semester-badge {{ $n->semester === 'Ganjil' ? 'smt-ganjil' : 'smt-genap' }}">
            {{ $n->semester }}
        </span>

        <div class="card-header-flex">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($n->siswa->nama ?? 'X') }}&background=dcfce7&color=15803d&bold=true" class="mini-avatar" alt="{{ $n->siswa->nama ?? 'Siswa' }}">
            <div>
                <h4 style="margin: 0; color: var(--text-main); font-size: 1rem;">{{ $n->siswa->nama ?? 'Siswa Hapus' }}</h4>
                <small style="color: var(--text-muted);">{{ $n->siswa->nisn ?? '-' }}</small>
            </div>
        </div>

        <div style="margin-bottom: 10px;">
            <div style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-book"></i> {{ $n->ampu->mapel->nama_mapel ?? 'Mapel Hapus' }}
            </div>
            <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 4px;">
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
        <div class="card-actions" style="margin-top: 16px; border-top: 1px solid var(--border-color); padding-top: 14px;">
            <a href="{{ route('nilai', ['edit' => $n->id_nilai]) }}" class="btn-icon btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
            </a>
            <button type="button"
                    data-url="{{ route('nilai.hapus', $n->id_nilai) }}"
                    data-name="{{ ($n->siswa->nama ?? 'Siswa') . ' - ' . ($n->ampu->mapel->nama_mapel ?? 'Mapel') }}"
                    onclick="openDeleteModal(this)"
                    class="btn-icon btn-delete" title="Hapus">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-star"></i>
        <strong>Belum ada data nilai.</strong>
        <p style="margin: 6px 0 0;">Nilai siswa akan muncul di sini setelah diinput.</p>
    </div>
    @endforelse
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
