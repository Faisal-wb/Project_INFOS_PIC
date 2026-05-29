<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use Illuminate\Http\Request;

class AdminLiburController extends Controller
{
    // Menampilkan daftar info libur (Halaman Admin)
    public function index(Request $request)
    {
        $semuaLibur = HariLibur::orderBy('tanggal', 'desc')->get();
        
        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $semuaLibur]);
        }
        
        return view('admin.libur.index', compact('semuaLibur'));
    }

    // Form tambah info
    public function create()
    {
        return view('admin.libur.create');
    }

    // Menyimpan data info libur baru
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'file_lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240'
        ]);

        $data = [
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->keterangan
        ];

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('info_gambar', 'public');
        }

        if ($request->hasFile('file_lampiran')) {
            $data['file_lampiran'] = $request->file('file_lampiran')->store('lampiran', 'public');
        }

        $libur = HariLibur::create($data);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Info libur berhasil ditambahkan!', 'data' => $libur], 201);
        }

        return redirect()->route('admin.libur.index')->with('success', 'Info libur berhasil ditambahkan!');
    }

    // Form edit info
    public function edit($id)
    {
        $libur = HariLibur::findOrFail($id);
        return view('admin.libur.edit', compact('libur'));
    }

    // Mengupdate data info libur
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'file_lampiran' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240'
        ]);

        $libur = HariLibur::findOrFail($id);
        
        $data = [
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->keterangan
        ];

        if ($request->hasFile('gambar')) {
            if ($libur->gambar && \Storage::disk('public')->exists($libur->gambar)) {
                \Storage::disk('public')->delete($libur->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('info_gambar', 'public');
        }

        if ($request->hasFile('file_lampiran')) {
            if ($libur->file_lampiran && \Storage::disk('public')->exists($libur->file_lampiran)) {
                \Storage::disk('public')->delete($libur->file_lampiran);
            }
            $data['file_lampiran'] = $request->file('file_lampiran')->store('lampiran', 'public');
        }

        $libur->update($data);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Info libur berhasil diperbarui!', 'data' => $libur]);
        }

        return redirect()->route('admin.libur.index')->with('success', 'Info libur berhasil diperbarui!');
    }

    // Menghapus data info libur
    public function destroy(Request $request, $id)
    {
        $libur = HariLibur::findOrFail($id);
        
        if ($libur->gambar && \Storage::disk('public')->exists($libur->gambar)) {
            \Storage::disk('public')->delete($libur->gambar);
        }
        if ($libur->file_lampiran && \Storage::disk('public')->exists($libur->file_lampiran)) {
            \Storage::disk('public')->delete($libur->file_lampiran);
        }

        $libur->delete();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Info libur berhasil dihapus!']);
        }

        return redirect()->route('admin.libur.index')->with('success', 'Info libur berhasil dihapus!');
    }
}
