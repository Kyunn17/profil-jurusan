@extends('layouts.admin')

@section('title', 'Dashboard')

@section('before_container')
<section class="hero">
    <h1>Dashboard Akademik</h1>

    <p class="hero-subtitle">
        Halo, <strong style="color: var(--primary);">{{ Auth::user()->name }}</strong>!
    </p>

    <div>
        <span class="role-pill">
            <i class="fas fa-user-shield"></i> Sebagai {{ Auth::user()->role }}
        </span>
    </div>

    <p class="hero-desc">
        Kelola Data Sekolah, Akademik, dan Nilai Siswa SMKN 4 Padalarang secara terpadu dan bersih.
    </p>
</section>
@endsection

@section('container_attrs', 'style="margin-top: -28px; position: relative; z-index: 2;"')

@section('content')
<div class="cards-grid">
    <a href="{{ route('siswa') }}" class="card">
        <div class="card-icon-box"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-value">{{ $jumlahSiswa ?? 0 }}</div>
        <div class="stat-label">Total Siswa</div>
    </a>

    <a href="{{ route('guru') }}" class="card">
        <div class="card-icon-box" style="background: var(--teal-light); color: var(--teal);"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-value">{{ $jumlahGuru ?? 0 }}</div>
        <div class="stat-label">Total Guru</div>
    </a>

    <a href="{{ route('kelas') }}" class="card">
        <div class="card-icon-box"><i class="fas fa-school"></i></div>
        <div class="stat-value">{{ $jumlahKelas ?? 0 }}</div>
        <div class="stat-label">Total Kelas</div>
    </a>

    <a href="{{ route('mapel') }}" class="card">
        <div class="card-icon-box" style="background: var(--teal-light); color: var(--teal);"><i class="fas fa-book"></i></div>
        <div class="stat-value">{{ $jumlahMapel ?? 0 }}</div>
        <div class="stat-label">Mata Pelajaran</div>
    </a>

    <a href="{{ route('nilai') }}" class="card">
        <div class="card-icon-box"><i class="fas fa-star"></i></div>
        <div class="stat-value">{{ $jumlahNilai ?? 0 }}</div>
        <div class="stat-label">Data Nilai</div>
    </a>

    <a href="{{ route('absensi') }}" class="card">
        <div class="card-icon-box" style="background: var(--teal-light); color: var(--teal);"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-value">{{ $jumlahAbsensi ?? 0 }}</div>
        <div class="stat-label">Data Absensi</div>
    </a>
</div>
@endsection
