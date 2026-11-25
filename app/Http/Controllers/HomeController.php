<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        return view('admin.home', [
            'jumlahSiswa'   => DB::table('siswa')->count(),
            'jumlahGuru'    => DB::table('guru')->count(),
            'jumlahKelas'   => DB::table('kelas')->count(),
            'jumlahMapel'   => DB::table('mata_pelajaran')->count(),
            'jumlahNilai'   => DB::table('nilai')->count(),
            'jumlahAbsensi' => DB::table('absensi')->count(),
        ]);
    }
}
