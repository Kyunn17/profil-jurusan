<?php

namespace App\Http\Controllers;

use App\Models\MapelDiampu;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $nilai = Nilai::with(['siswa', 'ampu.guru', 'ampu.mapel', 'ampu.kelas'])->get();
        $siswa = Siswa::with('kelas')->get();
        $ampu = MapelDiampu::with(['guru', 'mapel', 'kelas'])->get();

        $edit = $request->filled('edit') ? Nilai::find($request->edit) : null;

        return view('admin.nilai', compact('nilai', 'siswa', 'ampu', 'edit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => ['required', 'exists:siswa,id_siswa'],
            'id_ampu' => ['required', 'exists:mapel_diampu,id_ampu'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'nilai_tugas' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_uts' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $validated['nilai_akhir'] = round(((float) $validated['nilai_tugas'] + (float) $validated['nilai_uts']) / 2, 2);

        Nilai::create($validated);

        return redirect()->route('nilai')->with('success', 'Nilai ditambahkan');
    }

    public function update(Request $request)
    {
        $nilai = Nilai::findOrFail($request->id_nilai);

        $validated = $request->validate([
            'id_nilai' => ['required', 'exists:nilai,id_nilai'],
            'id_siswa' => ['required', 'exists:siswa,id_siswa'],
            'id_ampu' => ['required', 'exists:mapel_diampu,id_ampu'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'nilai_tugas' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_uts' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        unset($validated['id_nilai']);
        $validated['nilai_akhir'] = round(((float) $validated['nilai_tugas'] + (float) $validated['nilai_uts']) / 2, 2);

        $nilai->update($validated);

        return redirect()->route('nilai')->with('success', 'Nilai diupdate');
    }

    public function destroy($id)
    {
        Nilai::findOrFail($id)->delete();

        return redirect()->route('nilai')->with('success', 'Nilai dihapus');
    }
}
