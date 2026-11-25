@extends('layouts.admin')

@section('title', 'Dashboard')
@section('logo_spin', 'fa-spin" style="--fa-animation-duration: 15s;')

@section('before_container')
<section class="hero"
    style="background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)),
           url('{{ asset('assets/bg.jpg') }}');
           background-size: cover;
           background-position: center;
           text-align: center;
           padding: 60px 20px;">

    <h1 style="font-size: 2.5rem; margin-bottom: 10px; text-shadow: 0 0 20px rgba(56, 189, 248, 0.5);">
        Dashboard Akademik
    </h1>

    <p style="font-size: 1.2rem; color: #e2e8f0; margin-bottom: 5px;">
        Halo, <strong style="color: var(--primary); text-transform: uppercase;">{{ Auth::user()->name }}</strong>! 👋
    </p>

    <div style="margin-top: 10px;">
        <span style="background: rgba(56, 189, 248, 0.1); color: var(--primary); padding: 5px 15px; border-radius: 20px; border: 1px solid var(--primary); font-size: 0.9rem; font-weight: bold; text-transform: uppercase;">
            <i class="fas fa-user-shield"></i> Sebagai {{ Auth::user()->role }}
        </span>
    </div>

    <p style="font-size: 0.95rem; margin-top: 20px; color: var(--text-muted);">
        Kelola Data Sekolah &amp; Nilai Siswa SMK dengan Teknologi Terkini.
    </p>
</section>
@endsection

@section('container_attrs', 'style="margin-top: -30px; position: relative; z-index: 2;"')

@section('content')
<div class="cards-grid">
    <a href="{{ route('siswa') }}" class="card">
        <div class="card-icon" style="color: var(--primary);"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-value">{{ $jumlahSiswa ?? 0 }}</div>
        <div class="stat-label">Total Siswa</div>
    </a>

    <a href="{{ route('guru') }}" class="card">
        <div class="card-icon" style="color: var(--secondary);"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-value">{{ $jumlahGuru ?? 0 }}</div>
        <div class="stat-label">Total Guru</div>
    </a>

    <a href="{{ route('kelas') }}" class="card">
        <div class="card-icon" style="color: var(--success);"><i class="fas fa-school"></i></div>
        <div class="stat-value">{{ $jumlahKelas ?? 0 }}</div>
        <div class="stat-label">Total Kelas</div>
    </a>

    <a href="{{ route('mapel') }}" class="card">
        <div class="card-icon" style="color: var(--warning);"><i class="fas fa-book"></i></div>
        <div class="stat-value">{{ $jumlahMapel ?? 0 }}</div>
        <div class="stat-label">Mata Pelajaran</div>
    </a>

    <a href="{{ route('nilai') }}" class="card">
        <div class="card-icon" style="color: var(--purple);"><i class="fas fa-star"></i></div>
        <div class="stat-value">{{ $jumlahNilai ?? 0 }}</div>
        <div class="stat-label">Data Nilai</div>
    </a>

    <a href="{{ route('absensi') }}" class="card">
        <div class="card-icon" style="color: var(--danger);"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-value">{{ $jumlahAbsensi ?? 0 }}</div>
        <div class="stat-label">Data Absensi</div>
    </a>
</div>
@endsection
