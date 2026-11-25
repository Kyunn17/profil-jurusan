<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $guruQuery = Guru::query();

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $guruQuery->where(function ($q) use ($cari) {
                $q->where('nama', 'LIKE', "%{$cari}%")
                    ->orWhere('nip', 'LIKE', "%{$cari}%");
            });
        }

        $guru = $guruQuery->get();
        $edit = $request->has('edit') ? Guru::find($request->edit) : null;

        return view('admin.guru', compact('guru', 'edit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:50', 'unique:guru,nip'],
            'nama' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'email' => ['nullable', 'email', 'max:255', 'unique:guru,email'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        Guru::create($validated);

        return redirect()->route('guru')->with('success', 'Data Guru berhasil ditambahkan!');
    }

    public function update(Request $request)
    {
        $guru = Guru::findOrFail($request->id_guru);

        $validated = $request->validate([
            'id_guru' => ['required', 'exists:guru,id_guru'],
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('guru', 'nip')->ignore($guru->id_guru, 'id_guru')],
            'nama' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('guru', 'email')->ignore($guru->id_guru, 'id_guru')],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        unset($validated['id_guru']);
        $guru->update($validated);

        return redirect()->route('guru')->with('success', 'Data Guru berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Guru::findOrFail($id)->delete();

        return redirect()->route('guru')->with('success', 'Data Guru berhasil dihapus!');
    }
}
