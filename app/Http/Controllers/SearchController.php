<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.search', ['query' => $request->input('q')]);
    }

    public function liveSearch(Request $request)
    {
        try {
            $query = trim((string) $request->get('q', ''));
            $results = [];

            if ($query !== '') {
                $siswa = Siswa::with('kelas')
                    ->where(function ($q) use ($query) {
                        $q->where('nama', 'LIKE', "%{$query}%")
                            ->orWhere('nisn', 'LIKE', "%{$query}%");
                    })
                    ->limit(5)
                    ->get();

                foreach ($siswa as $s) {
                    $results[] = [
                        'category' => 'Siswa',
                        'icon' => 'fas fa-user-graduate',
                        'color' => '#38bdf8',
                        'title' => $s->nama,
                        'desc' => $s->nisn . ' - ' . ($s->kelas->nama_kelas ?? '-'),
                        'url' => route('siswa', ['cari' => $s->nama]),
                    ];
                }

                $guru = Guru::where(function ($q) use ($query) {
                    $q->where('nama', 'LIKE', "%{$query}%")
                        ->orWhere('nip', 'LIKE', "%{$query}%");
                })
                    ->limit(5)
                    ->get();

                foreach ($guru as $g) {
                    $results[] = [
                        'category' => 'Guru',
                        'icon' => 'fas fa-chalkboard-teacher',
                        'color' => '#a78bfa',
                        'title' => $g->nama,
                        'desc' => 'NIP: ' . ($g->nip ?? '-'),
                        'url' => route('guru', ['cari' => $g->nama]),
                    ];
                }

                $mapel = Mapel::where('nama_mapel', 'LIKE', "%{$query}%")
                    ->limit(5)
                    ->get();

                foreach ($mapel as $m) {
                    $results[] = [
                        'category' => 'Mapel',
                        'icon' => 'fas fa-book',
                        'color' => '#10b981',
                        'title' => $m->nama_mapel,
                        'desc' => $m->kategori ?? 'Umum',
                        'url' => route('mapel', ['cari' => $m->nama_mapel]),
                    ];
                }

                $kelas = Kelas::where('nama_kelas', 'LIKE', "%{$query}%")
                    ->limit(5)
                    ->get();

                foreach ($kelas as $k) {
                    $results[] = [
                        'category' => 'Kelas',
                        'icon' => 'fas fa-school',
                        'color' => '#f59e0b',
                        'title' => $k->nama_kelas,
                        'desc' => $k->jurusan ?? '-',
                        'url' => route('kelas', ['cari' => $k->nama_kelas]),
                    ];
                }

                $beritaQuery = Berita::where('judul', 'LIKE', "%{$query}%");
                if (Auth::user()->role !== 'admin') {
                    $beritaQuery->where('status', 'published');
                }
                $berita = $beritaQuery->limit(5)->get();

                foreach ($berita as $b) {
                    $results[] = [
                        'category' => 'Berita',
                        'icon' => 'fas fa-newspaper',
                        'color' => '#ef4444',
                        'title' => $b->judul,
                        'desc' => mb_substr(strip_tags($b->isi), 0, 40) . '...',
                        'url' => route('berita', ['cari' => $b->judul]),
                    ];
                }

                if (Auth::user()->role === 'admin') {
                    $users = User::where(function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('email', 'LIKE', "%{$query}%");
                    })
                        ->limit(5)
                        ->get();

                    foreach ($users as $u) {
                        $results[] = [
                            'category' => 'User',
                            'icon' => 'fas fa-user-shield',
                            'color' => '#ec4899',
                            'title' => $u->name,
                            'desc' => 'Role: ' . ($u->role ?? 'User'),
                            'url' => route('pengguna', ['cari' => $u->name]),
                        ];
                    }
                }
            }

            return response()->json($results);
        } catch (\Throwable $e) {
            Log::error('Live search error', ['message' => $e->getMessage()]);

            return response()->json([
                [
                    'category' => 'ERROR SYSTEM',
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => '#ef4444',
                    'title' => 'Terjadi Kesalahan di Server!',
                    'desc' => 'Terjadi kesalahan pada server.',
                    'url' => '#',
                ],
            ], 500);
        }
    }
}
