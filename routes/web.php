<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\MapelDiampuController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('home.index');
})->name('landing');

/*
|--------------------------------------------------------------------------
| Protected Routes (WAJIB LOGIN & VERIFIED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // === DASHBOARD ===
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // === FITUR SEARCH ===
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/ajax/live-search', [SearchController::class, 'liveSearch'])->name('search.live');

    // === PROFILE (Breeze) ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // === HALAMAN READ-ONLY (SEMUA ROLE TERAUTENTIKASI) ===
    Route::get('/admin/siswa', [SiswaController::class, 'index'])->name('siswa');
    Route::get('/guru', [GuruController::class, 'index'])->name('guru');
    Route::get('/mapel', [MapelController::class, 'index'])->name('mapel');
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas');
    Route::get('/mapel-diampu', [MapelDiampuController::class, 'index'])->name('mapel_diampu');
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai');
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');

    // === ABSENSI MANDIRI SISWA ===
    Route::post('/absensi/checkin', [AbsensiController::class, 'checkIn'])
        ->middleware('role:siswa')
        ->name('absensi.checkin');

    // === BERITA (Hak akses per-item dicek di controller) ===
    Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
    Route::post('/berita/store', [BeritaController::class, 'store'])->name('berita.store');
    Route::post('/berita/update', [BeritaController::class, 'update'])->name('berita.update');
    Route::delete('/berita/hapus/{id}', [BeritaController::class, 'destroy'])->name('berita.hapus');

    // === EXPORT DATA (Dinamis; pembatasan role per-tipe dicek di controller) ===
    Route::get('/export/preview/{type}', [ExportController::class, 'preview'])->name('export.preview');
    Route::get('/export/download/{type}', [ExportController::class, 'download'])->name('export.download');

    // === ADMIN ONLY ROUTES ===
    Route::middleware('role:admin')->group(function () {
        // Siswa
        Route::post('/admin/siswa', [SiswaController::class, 'store'])->name('siswa.store');
        Route::post('/admin/siswa/update', [SiswaController::class, 'update'])->name('siswa.update');
        Route::delete('/admin/siswa/hapus/{id}', [SiswaController::class, 'destroy'])->name('siswa.hapus');

        // Guru
        Route::post('/guru/store', [GuruController::class, 'store'])->name('guru.store');
        Route::post('/guru/update', [GuruController::class, 'update'])->name('guru.update');
        Route::delete('/guru/hapus/{id}', [GuruController::class, 'destroy'])->name('guru.hapus');

        // Mapel
        Route::post('/mapel/store', [MapelController::class, 'store'])->name('mapel.store');
        Route::post('/mapel/update', [MapelController::class, 'update'])->name('mapel.update');
        Route::delete('/mapel/hapus/{id}', [MapelController::class, 'destroy'])->name('mapel.hapus');

        // Kelas
        Route::post('/kelas/store', [KelasController::class, 'store'])->name('kelas.store');
        Route::post('/kelas/update', [KelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/hapus/{id}', [KelasController::class, 'destroy'])->name('kelas.hapus');

        // Mapel Diampu
        Route::post('/mapel-diampu/store', [MapelDiampuController::class, 'store'])->name('mapel_diampu.store');
        Route::post('/mapel-diampu/update', [MapelDiampuController::class, 'update'])->name('mapel_diampu.update');
        Route::delete('/mapel-diampu/hapus/{id}', [MapelDiampuController::class, 'destroy'])->name('mapel_diampu.hapus');

        // Pengguna
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna');
        Route::post('/pengguna/store', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::post('/pengguna/update', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::delete('/pengguna/hapus/{id}', [PenggunaController::class, 'destroy'])->name('pengguna.hapus');

        // Berita Moderation
        Route::patch('/berita/approve/{id}', [BeritaController::class, 'approve'])->name('berita.approve');
    });

    // === ADMIN & GURU ROUTES ===
    Route::middleware('role:admin,guru')->group(function () {
        // Nilai
        Route::post('/nilai/store', [NilaiController::class, 'store'])->name('nilai.store');
        Route::post('/nilai/update', [NilaiController::class, 'update'])->name('nilai.update');
        Route::delete('/nilai/hapus/{id}', [NilaiController::class, 'destroy'])->name('nilai.hapus');

        // Absensi
        Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::post('/absensi/bulk', [AbsensiController::class, 'bulkStore'])->name('absensi.bulk');
        Route::post('/absensi/update', [AbsensiController::class, 'update'])->name('absensi.update');
        Route::delete('/absensi/hapus/{id}', [AbsensiController::class, 'destroy'])->name('absensi.hapus');
    });
});

require __DIR__.'/auth.php';
