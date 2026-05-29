<?php

namespace App\Http\Controllers;

use App\Models\JadwalRapot;
use Illuminate\Http\Request;

class AdminRapotController extends Controller
{
    /**
     * Menampilkan daftar jadwal pengambilan rapot (Halaman Admin).
     */
    public function index(Request $request)
    {
        $semuaJadwal = JadwalRapot::orderBy('tanggal', 'desc')->get();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $semuaJadwal]);
        }

        return view('admin.rapot.index', compact('semuaJadwal'));
    }

    /**
     * Form tambah jadwal rapot.
     */
    public function create()
    {
        return view('admin.rapot.create');
    }

    /**
     * Menyimpan data jadwal rapot baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'          => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'tanggal'        => 'required|date',
            'waktu_mulai'    => 'nullable|date_format:H:i',
            'waktu_selesai'  => 'nullable|date_format:H:i',
            'kelas'          => 'nullable|string|max:50',
            'lokasi'         => 'nullable|string|max:255',
        ]);

        $jadwal = JadwalRapot::create($request->only([
            'judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'kelas', 'lokasi'
        ]));

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Jadwal rapot berhasil ditambahkan!', 'data' => $jadwal], 201);
        }

        return redirect()->route('admin.rapot.index')->with('success', 'Jadwal rapot berhasil ditambahkan!');
    }

    /**
     * Form edit jadwal rapot.
     */
    public function edit($id)
    {
        $jadwal = JadwalRapot::findOrFail($id);
        return view('admin.rapot.edit', compact('jadwal'));
    }

    /**
     * Mengupdate data jadwal rapot.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul'          => 'required|string|max:255',
            'deskripsi'      => 'required|string',
            'tanggal'        => 'required|date',
            'waktu_mulai'    => 'nullable|date_format:H:i',
            'waktu_selesai'  => 'nullable|date_format:H:i',
            'kelas'          => 'nullable|string|max:50',
            'lokasi'         => 'nullable|string|max:255',
        ]);

        $jadwal = JadwalRapot::findOrFail($id);
        $jadwal->update($request->only([
            'judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'kelas', 'lokasi'
        ]));

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Jadwal rapot berhasil diperbarui!', 'data' => $jadwal]);
        }

        return redirect()->route('admin.rapot.index')->with('success', 'Jadwal rapot berhasil diperbarui!');
    }

    /**
     * Menghapus data jadwal rapot.
     */
    public function destroy(Request $request, $id)
    {
        $jadwal = JadwalRapot::findOrFail($id);
        $jadwal->delete();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Jadwal rapot berhasil dihapus!']);
        }

        return redirect()->route('admin.rapot.index')->with('success', 'Jadwal rapot berhasil dihapus!');
    }
}
