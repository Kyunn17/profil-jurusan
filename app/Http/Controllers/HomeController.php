<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Berita;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $user = Auth::user();

        $todayRecords = Absensi::whereDate('tanggal', $today)->get();
        $todayCounts = [
            'hadir' => $todayRecords->where('status', 'hadir')->count(),
            'izin' => $todayRecords->where('status', 'izin')->count(),
            'sakit' => $todayRecords->where('status', 'sakit')->count(),
            'alpha' => $todayRecords->where('status', 'alpha')->count(),
        ];

        $currentSiswa = $user->role === 'siswa' ? $user->resolveSiswa() : null;
        $todayAbsensi = $currentSiswa
            ? Absensi::where('id_siswa', $currentSiswa->id_siswa)->whereDate('tanggal', $today)->first()
            : null;

        $siswaList = ($user->role === 'siswa' && ! $currentSiswa)
            ? Siswa::with('kelas')->orderBy('nama')->get()
            : collect();

        $recentAbsensi = Absensi::with(['siswa', 'kelas'])
            ->when($user->role === 'siswa' && $currentSiswa, fn ($q) => $q->where('id_siswa', $currentSiswa->id_siswa))
            ->orderByDesc('tanggal')
            ->orderByDesc('id_absensi')
            ->limit(6)
            ->get();

        $recentBerita = Berita::with('user')
            ->where('status', 'published')
            ->orderByDesc('tanggal_post')
            ->limit(4)
            ->get();

        return view('admin.home', [
            'jumlahSiswa' => DB::table('siswa')->count(),
            'jumlahGuru' => DB::table('guru')->count(),
            'jumlahKelas' => DB::table('kelas')->count(),
            'jumlahMapel' => DB::table('mata_pelajaran')->count(),
            'jumlahNilai' => DB::table('nilai')->count(),
            'jumlahAbsensi' => DB::table('absensi')->count(),
            'today' => $today,
            'todayCounts' => $todayCounts,
            'currentSiswa' => $currentSiswa,
            'todayAbsensi' => $todayAbsensi,
            'siswaList' => $siswaList,
            'recentAbsensi' => $recentAbsensi,
            'recentBerita' => $recentBerita,
        ]);
    }
}
