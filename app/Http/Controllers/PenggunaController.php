<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $penggunaQuery = User::query();

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $penggunaQuery->where(function ($q) use ($cari) {
                $q->where('name', 'LIKE', "%{$cari}%")
                    ->orWhere('email', 'LIKE', "%{$cari}%");
            });
        }

        $pengguna = $penggunaQuery->get();
        $edit = $request->has('edit') ? User::find($request->edit) : null;

        return view('admin.pengguna', compact('pengguna', 'edit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,guru,siswa'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('pengguna')->with('success', 'User berhasil ditambahkan!');
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);

        $validated = $request->validate([
            'id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:admin,guru,siswa'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('pengguna')->with('success', 'Data User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if ((int) $id === (int) Auth::id()) {
            return redirect()->route('pengguna')->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        User::findOrFail($id)->delete();

        return redirect()->route('pengguna')->with('success', 'User berhasil dihapus!');
    }
}
