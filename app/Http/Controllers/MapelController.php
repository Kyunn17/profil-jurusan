<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $mapelQuery = Mapel::query();

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $mapelQuery->where(function ($q) use ($cari) {
                $q->where('nama_mapel', 'LIKE', "%{$cari}%")
                    ->orWhere('kategori', 'LIKE', "%{$cari}%");
            });
        }

        $mapel = $mapelQuery->get();
        $edit = $request->has('edit') ? Mapel::find($request->edit) : null;

        return view('admin.mapel', compact('mapel', 'edit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mapel' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'in:Produktif,Umum,Mulok'],
        ]);

        Mapel::create($validated);

        return redirect()->route('mapel')->with('success', 'Mapel berhasil ditambahkan!');
    }

    public function update(Request $request)
    {
        $mapel = Mapel::findOrFail($request->id_mapel);

        $validated = $request->validate([
            'id_mapel' => ['required', 'exists:mata_pelajaran,id_mapel'],
            'nama_mapel' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'in:Produktif,Umum,Mulok'],
        ]);

        unset($validated['id_mapel']);
        $mapel->update($validated);

        return redirect()->route('mapel')->with('success', 'Mapel berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Mapel::findOrFail($id)->delete();

        return redirect()->route('mapel')->with('success', 'Mapel berhasil dihapus!');
    }
}
