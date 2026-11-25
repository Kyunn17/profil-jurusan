<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $publishedQuery = Berita::with('user')
            ->where('status', 'published')
            ->orderBy('tanggal_post', 'desc');

        if ($request->filled('cari')) {
            $publishedQuery->where('judul', 'LIKE', "%{$request->cari}%");
        }

        $beritaPublished = $publishedQuery->get();

        $beritaPending = [];
        if (Auth::user()->role == 'admin') {
            $beritaPending = Berita::with('user')
                ->where('status', 'pending')
                ->orderBy('tanggal_post', 'desc')
                ->get();
        }

        $myBerita = [];
        if (Auth::user()->role != 'admin') {
            $myBerita = Berita::where('id_user', Auth::id())
                ->orderBy('tanggal_post', 'desc')
                ->get();
        }

        $edit = null;
        if ($request->has('edit')) {
            $edit = Berita::find($request->edit);
            if (! $edit || ($edit->id_user != Auth::id() && Auth::user()->role != 'admin')) {
                return redirect()->route('berita')->with('error', 'Akses Ditolak!');
            }
        }

        return view('admin.berita', compact('beritaPublished', 'beritaPending', 'myBerita', 'edit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $fotoBlob = file_get_contents($request->file('foto')->getRealPath());
        $statusAwal = Auth::user()->role == 'admin' ? 'published' : 'pending';

        Berita::create([
            'id_user' => Auth::id(),
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'foto' => $fotoBlob,
            'tanggal_post' => now(),
            'status' => $statusAwal,
        ]);

        $msg = Auth::user()->role == 'admin'
            ? 'Berita berhasil diterbitkan!'
            : 'Berita dikirim & menunggu konfirmasi Admin.';

        return redirect()->route('berita')->with('success', $msg);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id_berita' => ['required', 'exists:berita,id_berita'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $berita = Berita::findOrFail($validated['id_berita']);

        if ($berita->id_user != Auth::id() && Auth::user()->role != 'admin') {
            abort(403);
        }

        $fotoBlob = $berita->foto;
        if ($request->hasFile('foto')) {
            $fotoBlob = file_get_contents($request->file('foto')->getRealPath());
        }

        $berita->update([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'foto' => $fotoBlob,
            'status' => Auth::user()->role == 'admin' ? 'published' : 'pending',
        ]);

        return redirect()->route('berita')->with('success', 'Berita diperbarui!');
    }

    public function approve($id)
    {
        if (Auth::user()->role != 'admin') {
            abort(403);
        }

        Berita::findOrFail($id)->update(['status' => 'published']);

        return redirect()->route('berita')->with('success', 'Berita berhasil di-ACC!');
    }

    public function destroy($id)
    {
        $berita = Berita::find($id);

        if (! $berita || ($berita->id_user != Auth::id() && Auth::user()->role != 'admin')) {
            return redirect()->route('berita')->with('error', 'Dilarang menghapus berita orang lain!');
        }

        $berita->delete();

        return redirect()->route('berita')->with('success', 'Berita dihapus.');
    }
}
