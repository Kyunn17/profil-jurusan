<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\MapelDiampu;
use Illuminate\Http\Request;

class MapelDiampuController extends Controller
{
    public function index(Request $request)
    {
        $ampu = MapelDiampu::with(['guru', 'mapel', 'kelas'])->get();
        $guru = Guru::all();
        $mapel = Mapel::all();
        $kelas = Kelas::all();

        $edit = $request->has('edit') ? MapelDiampu::find($request->edit) : null;

        return view('admin.mapel_diampu', compact('ampu', 'guru', 'mapel', 'kelas', 'edit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_guru' => ['required', 'exists:guru,id_guru'],
            'id_mapel' => ['required', 'exists:mata_pelajaran,id_mapel'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
        ]);

        MapelDiampu::create($validated);

        return redirect()->route('mapel_diampu')->with('success', 'Data berhasil disimpan');
    }

    public function update(Request $request)
    {
        $ampu = MapelDiampu::findOrFail($request->id_ampu);

        $validated = $request->validate([
            'id_ampu' => ['required', 'exists:mapel_diampu,id_ampu'],
            'id_guru' => ['required', 'exists:guru,id_guru'],
            'id_mapel' => ['required', 'exists:mata_pelajaran,id_mapel'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
        ]);

        unset($validated['id_ampu']);
        $ampu->update($validated);

        return redirect()->route('mapel_diampu')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        MapelDiampu::findOrFail($id)->delete();

        return redirect()->route('mapel_diampu')->with('success', 'Data dihapus');
    }
}
