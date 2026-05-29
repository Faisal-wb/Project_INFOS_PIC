<?php

namespace App\Http\Controllers;

use App\Models\KegiatanSekolah;
use Illuminate\Http\Request;

class AdminKegiatanController extends Controller
{
    /**
     * Menampilkan daftar kegiatan sekolah (Halaman Admin).
     */
    public function index(Request $request)
    {
        $semuaKegiatan = KegiatanSekolah::orderBy('tanggal', 'desc')->get();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $semuaKegiatan]);
        }

        return view('admin.kegiatan.index', compact('semuaKegiatan'));
    }

    /**
     * Form tambah kegiatan.
     */
    public function create()
    {
        return view('admin.kegiatan.create');
    }

    /**
     * Menyimpan data kegiatan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'          => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'tanggal'        => 'required|date',
            'waktu_mulai'    => 'nullable|date_format:H:i',
            'waktu_selesai'  => 'nullable|date_format:H:i',
            'lokasi'         => 'nullable|string|max:255',
        ]);

        $kegiatan = KegiatanSekolah::create($request->only([
            'judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'lokasi'
        ]));

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Kegiatan berhasil ditambahkan!', 'data' => $kegiatan], 201);
        }

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    /**
     * Form edit kegiatan.
     */
    public function edit($id)
    {
        $kegiatan = KegiatanSekolah::findOrFail($id);
        return view('admin.kegiatan.edit', compact('kegiatan'));
    }

    /**
     * Mengupdate data kegiatan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul'          => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'tanggal'        => 'required|date',
            'waktu_mulai'    => 'nullable|date_format:H:i',
            'waktu_selesai'  => 'nullable|date_format:H:i',
            'lokasi'         => 'nullable|string|max:255',
        ]);

        $kegiatan = KegiatanSekolah::findOrFail($id);
        $kegiatan->update($request->only([
            'judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'lokasi'
        ]));

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Kegiatan berhasil diperbarui!', 'data' => $kegiatan]);
        }

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui!');
    }

    /**
     * Menghapus data kegiatan.
     */
    public function destroy(Request $request, $id)
    {
        $kegiatan = KegiatanSekolah::findOrFail($id);
        $kegiatan->delete();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Kegiatan berhasil dihapus!']);
        }

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus!');
    }
}
