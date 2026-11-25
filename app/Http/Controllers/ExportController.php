<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Berita;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MapelDiampu;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    private const VALID_TYPES = [
        'siswa',
        'guru',
        'kelas',
        'mapel',
        'mapel_diampu',
        'nilai',
        'absensi',
        'berita',
        'pengguna',
    ];

    public function preview($type)
    {
        $result = $this->getDataByType($type);

        return view('admin.preview_export', [
            'title' => ucfirst(str_replace('_', ' ', $type)),
            'type' => $type,
            'headers' => $result['headers'],
            'rows' => $result['rows'],
        ]);
    }

    public function download($type)
    {
        $result = $this->getDataByType($type);
        $fileName = 'data-' . $type . '-' . date('Y-m-d') . '.csv';

        return $this->generateCSV($fileName, $result['rows'], $result['headers']);
    }

    private function getDataByType($type)
    {
        if (! in_array($type, self::VALID_TYPES, true)) {
            abort(404);
        }

        if (in_array($type, ['pengguna', 'berita'], true) && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $headers = [];
        $rows = [];

        switch ($type) {
            case 'siswa':
                $headers = ['NISN', 'Nama Lengkap', 'Gender', 'Kelas', 'Alamat', 'No HP'];
                $data = Siswa::with('kelas')->get();
                foreach ($data as $d) {
                    $rows[] = [
                        "'" . $d->nisn,
                        $d->nama,
                        $d->jenis_kelamin,
                        $d->kelas->nama_kelas ?? 'Tanpa Kelas',
                        $d->alamat ?? '-',
                        "'" . ($d->no_hp ?? '-'),
                    ];
                }
                break;

            case 'guru':
                $headers = ['NIP', 'Nama Guru', 'Gender', 'Email', 'No HP'];
                $data = Guru::all();
                foreach ($data as $d) {
                    $rows[] = [
                        "'" . ($d->nip ?? '-'),
                        $d->nama,
                        $d->jenis_kelamin,
                        $d->email ?? '-',
                        "'" . ($d->no_hp ?? '-'),
                    ];
                }
                break;

            case 'kelas':
                $headers = ['Nama Kelas', 'Jurusan'];
                $data = Kelas::all();
                foreach ($data as $d) {
                    $rows[] = [$d->nama_kelas, $d->jurusan ?? '-'];
                }
                break;

            case 'mapel':
                $headers = ['Mata Pelajaran', 'Kategori'];
                $data = Mapel::all();
                foreach ($data as $d) {
                    $rows[] = [$d->nama_mapel, $d->kategori ?? '-'];
                }
                break;

            case 'mapel_diampu':
                $headers = ['Guru Pengampu', 'Mata Pelajaran', 'Kelas'];
                $data = MapelDiampu::with(['guru', 'mapel', 'kelas'])->get();
                foreach ($data as $d) {
                    $rows[] = [
                        $d->guru->nama ?? 'Guru Dihapus',
                        $d->mapel->nama_mapel ?? 'Mapel Dihapus',
                        $d->kelas->nama_kelas ?? 'Kelas Dihapus',
                    ];
                }
                break;

            case 'nilai':
                $headers = ['Nama Siswa', 'Kelas', 'Mapel', 'Semester', 'Tugas', 'UTS', 'Nilai Akhir'];
                $data = Nilai::with(['siswa', 'ampu.kelas', 'ampu.mapel'])->get();
                foreach ($data as $d) {
                    $rows[] = [
                        $d->siswa->nama ?? 'Siswa Dihapus',
                        $d->ampu->kelas->nama_kelas ?? '-',
                        $d->ampu->mapel->nama_mapel ?? '-',
                        $d->semester,
                        $d->nilai_tugas,
                        $d->nilai_uts,
                        $d->nilai_akhir,
                    ];
                }
                break;

            case 'absensi':
                $headers = ['Tanggal', 'Nama Siswa', 'Kelas', 'Status'];
                $data = Absensi::with(['siswa', 'kelas'])->get();
                foreach ($data as $d) {
                    $rows[] = [
                        $d->tanggal,
                        $d->siswa->nama ?? 'Siswa Dihapus',
                        $d->kelas->nama_kelas ?? '-',
                        $d->status,
                    ];
                }
                break;

            case 'berita':
                $headers = ['Tanggal', 'Judul', 'Penulis', 'Status', 'Isi Singkat'];
                $data = Berita::with('user')->orderBy('tanggal_post', 'desc')->get();
                foreach ($data as $d) {
                    $rows[] = [
                        $d->tanggal_post,
                        $d->judul,
                        $d->user->name ?? 'Admin',
                        $d->status,
                        mb_substr(strip_tags($d->isi), 0, 50) . '...',
                    ];
                }
                break;

            case 'pengguna':
                $headers = ['Nama User', 'Email', 'Role'];
                $data = User::all();
                foreach ($data as $d) {
                    $rows[] = [$d->name, $d->email, $d->role ?? 'User'];
                }
                break;
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function generateCSV($fileName, $rows, $headers)
    {
        $callback = function () use ($rows, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
