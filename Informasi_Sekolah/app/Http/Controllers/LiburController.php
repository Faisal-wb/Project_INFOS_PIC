<?php
namespace App\Http\Controllers;

use App\Models\HariLibur;
use App\Models\Komentar;
use Illuminate\Http\Request;

class LiburController extends Controller
{
    public function index()
    {
        $semuaLibur = HariLibur::orderBy('tanggal', 'asc')->get();
        $semuaKomentar = Komentar::orderBy('created_at', 'desc')->get();

        if (request()->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $semuaKomentar
            ]);
        }

        return view('libur.index', compact('semuaLibur', 'semuaKomentar'));
    }

    public function getKomentar(Request $request)
    {
        $query = Komentar::query();
        if ($request->has('info_id')) {
            $query->where('info_id', $request->info_id);
        }
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        $komentar = $query->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $komentar
        ]);
    }

    public function simpanKomentar(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'isi' => 'required|string',
            'info_id' => 'required|integer',
            'kategori' => 'required|string'
        ]);

        Komentar::create([
            'nama' => $request->nama,
            'isi' => $request->isi,
            'info_id' => $request->info_id,
            'kategori' => $request->kategori
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'Berhasil',
                'pesan' => 'Komentar berhasil dikirim!'
            ], 200);
        }

        return redirect()->back()->with('sukses', 'Komentar berhasil dikirim!');
    }

    public function hapusKomentar(Request $request, $id)
    {
        $komentar = Komentar::findOrFail($id);
        $komentar->delete();
        
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'Berhasil',
                'pesan' => 'Komentar berhasil dihapus!'
            ]);
        }
        return redirect()->back();
    }
}
