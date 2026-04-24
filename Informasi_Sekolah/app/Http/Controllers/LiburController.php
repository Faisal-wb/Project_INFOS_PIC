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

        return view('libur.index', compact('semuaLibur', 'semuaKomentar'));
    }

    public function simpanKomentar(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'isi' => 'required|string'
        ]);

        Komentar::create([
            'nama' => $request->nama,
            'isi' => $request->isi
        ]);

                if ($request->expectsJson()) {
            return response()->json([
                'status' => 'Berhasil',
                'pesan' => 'Komentar berhasil dikirim!'
            ], 200);
        }

        return redirect()->back()->with('sukses', 'Komentar berhasil dikirim!');

    }
}
