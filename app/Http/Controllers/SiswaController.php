<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $siswaQuery = Siswa::with('kelas');

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $siswaQuery->where(function ($q) use ($cari) {
                $q->where('nama', 'LIKE', "%{$cari}%")
                    ->orWhere('nisn', 'LIKE', "%{$cari}%");
            });
        }

        $siswa = $siswaQuery->get();
        $kelas = Kelas::all();

        $edit = $request->has('edit') ? Siswa::find($request->edit) : null;

        return view('admin.siswa', compact('siswa', 'kelas', 'edit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => ['required', 'numeric', 'unique:siswa,nisn'],
            'nama' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
        ]);

        Siswa::create($validated);

        return redirect()->route('siswa')->with('success', 'Data Siswa Berhasil Ditambahkan!');
    }

    public function update(Request $request)
    {
        $siswa = Siswa::findOrFail($request->id_siswa);

        $validated = $request->validate([
            'id_siswa' => ['required', 'exists:siswa,id_siswa'],
            'nisn' => ['required', 'numeric', Rule::unique('siswa', 'nisn')->ignore($siswa->id_siswa, 'id_siswa')],
            'nama' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
        ]);

        unset($validated['id_siswa']);
        $siswa->update($validated);

        return redirect()->route('siswa')->with('success', 'Data Siswa Berhasil Diperbarui!');
    }

    public function destroy($id)
    {
        Siswa::findOrFail($id)->delete();

        return redirect()->route('siswa')->with('success', 'Data Siswa Berhasil Dihapus!');
    }
}
