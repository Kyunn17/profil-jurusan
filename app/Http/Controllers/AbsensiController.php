<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $absensi = Absensi::with(['siswa', 'kelas'])->get();
        $siswa = Siswa::with('kelas')->get();
        $kelas = Kelas::all();

        $edit = $request->has('edit') ? Absensi::find($request->edit) : null;

        return view('admin.absensi', compact('absensi', 'siswa', 'kelas', 'edit'));
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
