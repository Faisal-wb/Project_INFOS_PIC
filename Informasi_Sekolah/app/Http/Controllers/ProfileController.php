<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil user yang sedang login.
     */
    public function show()
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Mengupdate data profil user.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'no_telp'  => 'nullable|string|max:20',
            'about_me' => 'nullable|string|max:1000',
        ]);

        $user->name     = $request->name;
        $user->no_telp  = $request->no_telp;
        $user->about_me = $request->about_me;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Mengupdate foto profil user.
     */
    public function updateFoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'foto_profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Hapus foto lama jika ada
        if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        // Simpan foto baru
        $path = $request->file('foto_profile')->store('foto_profile', 'public');
        $user->foto_profile = $path;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Menghapus foto profil user.
     */
    public function deleteFoto()
    {
        $user = Auth::user();

        if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        $user->foto_profile = null;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Foto profil berhasil dihapus!');
    }
}
