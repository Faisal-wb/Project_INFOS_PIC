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
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'file_lampiran'  => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240'
        ]);

        $data = $request->only([
            'judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'kelas', 'lokasi'
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('info_gambar', 'public');
        }

        if ($request->hasFile('file_lampiran')) {
            $data['file_lampiran'] = $request->file('file_lampiran')->store('lampiran', 'public');
        }

        $jadwal = JadwalRapot::create($data);

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
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'file_lampiran'  => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240'
        ]);

        $jadwal = JadwalRapot::findOrFail($id);
        
        $data = $request->only([
            'judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'kelas', 'lokasi'
        ]);

        if ($request->hasFile('gambar')) {
            if ($jadwal->gambar && \Storage::disk('public')->exists($jadwal->gambar)) {
                \Storage::disk('public')->delete($jadwal->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('info_gambar', 'public');
        }

        if ($request->hasFile('file_lampiran')) {
            if ($jadwal->file_lampiran && \Storage::disk('public')->exists($jadwal->file_lampiran)) {
                \Storage::disk('public')->delete($jadwal->file_lampiran);
            }
            $data['file_lampiran'] = $request->file('file_lampiran')->store('lampiran', 'public');
        }

        $jadwal->update($data);

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
        
        if ($jadwal->gambar && \Storage::disk('public')->exists($jadwal->gambar)) {
            \Storage::disk('public')->delete($jadwal->gambar);
        }
        if ($jadwal->file_lampiran && \Storage::disk('public')->exists($jadwal->file_lampiran)) {
            \Storage::disk('public')->delete($jadwal->file_lampiran);
        }

        $jadwal->delete();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Jadwal rapot berhasil dihapus!']);
        }

        return redirect()->route('admin.rapot.index')->with('success', 'Jadwal rapot berhasil dihapus!');
    }
}
