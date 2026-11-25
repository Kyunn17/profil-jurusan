<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $kelasQuery = Kelas::query();

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $kelasQuery->where(function ($q) use ($cari) {
                $q->where('nama_kelas', 'LIKE', "%{$cari}%")
                    ->orWhere('jurusan', 'LIKE', "%{$cari}%");
            });
        }

        $kelas = $kelasQuery->get();
        $edit = $request->has('edit') ? Kelas::find($request->edit) : null;

        return view('admin.kelas', compact('kelas', 'edit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', 'string', 'max:100'],
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas')->with('success', 'Data Kelas berhasil ditambahkan!');
    }

    public function update(Request $request)
    {
        $kelas = Kelas::findOrFail($request->id_kelas);

        $validated = $request->validate([
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'nama_kelas' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', 'string', 'max:100'],
        ]);

        unset($validated['id_kelas']);
        $kelas->update($validated);

        return redirect()->route('kelas')->with('success', 'Data Kelas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Kelas::findOrFail($id)->delete();

        return redirect()->route('kelas')->with('success', 'Data Kelas berhasil dihapus!');
    }
}
