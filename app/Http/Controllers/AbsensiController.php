<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $selectedDate = $request->input('tanggal', $today);
        $selectedKelas = $request->input('kelas');

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $siswa = Siswa::with('kelas')->orderBy('nama')->get();

        $user = Auth::user();
        $currentSiswa = $user->role === 'siswa' ? $user->resolveSiswa() : null;
        $todayAbsensi = $currentSiswa
            ? Absensi::where('id_siswa', $currentSiswa->id_siswa)->whereDate('tanggal', $today)->first()
            : null;

        $rollCallSiswa = collect();
        $existingStatuses = collect();
        $todayCounts = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
        ];

        if (in_array($user->role, ['admin', 'guru'], true)) {
            $rollCallQuery = Siswa::with('kelas')->orderBy('nama');
            if ($selectedKelas) {
                $rollCallQuery->where('id_kelas', $selectedKelas);
            }
            $rollCallSiswa = $rollCallQuery->get();

            $dateRecords = Absensi::whereDate('tanggal', $selectedDate)->get();
            $existingStatuses = $dateRecords->pluck('status', 'id_siswa');

            foreach (['hadir', 'izin', 'sakit', 'alpha'] as $st) {
                $todayCounts[$st] = $dateRecords->where('status', $st)->count();
            }
        }

        $absensiQuery = Absensi::with(['siswa', 'kelas'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id_absensi');

        if ($user->role === 'siswa' && $currentSiswa) {
            $absensiQuery->where('id_siswa', $currentSiswa->id_siswa);
        }

        if ($request->filled('filter_kelas')) {
            $absensiQuery->where('id_kelas', $request->filter_kelas);
        }

        if ($request->filled('filter_tanggal')) {
            $absensiQuery->whereDate('tanggal', $request->filter_tanggal);
        }

        $absensi = $absensiQuery->get();
        $edit = $request->has('edit') ? Absensi::find($request->edit) : null;

        return view('admin.absensi', compact(
            'absensi',
            'siswa',
            'kelas',
            'edit',
            'today',
            'selectedDate',
            'selectedKelas',
            'currentSiswa',
            'todayAbsensi',
            'rollCallSiswa',
            'existingStatuses',
            'todayCounts'
        ));
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:hadir,izin,sakit'],
            'id_siswa' => ['nullable', 'exists:siswa,id_siswa'],
        ]);

        $user = $request->user();
        $siswa = $user->resolveSiswa();

        if (! $siswa) {
            if (! $request->filled('id_siswa')) {
                return redirect()->back()->with('error', 'Pilih data siswa Anda terlebih dahulu sebelum absen.');
            }

            $siswa = Siswa::findOrFail($validated['id_siswa']);

            if (Schema::hasColumn('users', 'id_siswa')) {
                $user->update(['id_siswa' => $siswa->id_siswa]);
            }
        }

        $today = now()->toDateString();

        $alreadyCheckedIn = Absensi::where('id_siswa', $siswa->id_siswa)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($alreadyCheckedIn) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi hari ini.');
        }

        Absensi::create([
            'id_siswa' => $siswa->id_siswa,
            'id_kelas' => $siswa->id_kelas,
            'tanggal' => $today,
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Absensi hari ini berhasil dicatat!');
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'statuses' => ['required', 'array'],
            'statuses.*' => ['nullable', 'in:hadir,izin,sakit,alpha'],
        ]);

        $studentIds = array_keys($validated['statuses']);
        $students = Siswa::whereIn('id_siswa', $studentIds)->get()->keyBy('id_siswa');

        foreach ($validated['statuses'] as $idSiswa => $status) {
            if (! $status || ! isset($students[$idSiswa])) {
                continue;
            }

            $student = $students[$idSiswa];

            Absensi::updateOrCreate(
                [
                    'id_siswa' => $student->id_siswa,
                    'tanggal' => $validated['tanggal'],
                ],
                [
                    'id_kelas' => $student->id_kelas,
                    'status' => $status,
                ]
            );
        }

        return redirect()
            ->route('absensi', array_filter([
                'tanggal' => $validated['tanggal'],
                'kelas' => $request->input('kelas'),
            ]))
            ->with('success', 'Presensi harian berhasil disimpan!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => ['required', 'exists:siswa,id_siswa'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:hadir,izin,sakit,alpha'],
        ]);

        Absensi::create($validated);

        return redirect()->route('absensi')->with('success', 'Data berhasil disimpan');
    }

    public function update(Request $request)
    {
        $absensi = Absensi::findOrFail($request->id_absensi);

        $validated = $request->validate([
            'id_absensi' => ['required', 'exists:absensi,id_absensi'],
            'id_siswa' => ['required', 'exists:siswa,id_siswa'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:hadir,izin,sakit,alpha'],
        ]);

        unset($validated['id_absensi']);
        $absensi->update($validated);

        return redirect()->route('absensi')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        Absensi::findOrFail($id)->delete();

        return redirect()->route('absensi')->with('success', 'Data dihapus');
    }
}
