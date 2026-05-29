<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdministrasiController extends Controller
{
    // API endpoint for students to check their payment status
    public function check(Request $request)
    {
        $request->validate([
            'nis' => 'required|string',
            'asesmen' => 'required|string'
        ]);

        $data = \App\Models\Administrasi::where('nis', $request->nis)
            ->where('asesmen', $request->asesmen)
            ->first();

        if ($data) {
            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data administrasi untuk NIS dan asesmen tersebut tidak ditemukan.'
            ], 404);
        }
    }

    // Admin: List all data
    public function index(Request $request)
    {
        $kelas = $request->query('kelas');
        $query = \App\Models\Administrasi::query();
        
        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        $data = $query->orderBy('kelas')->orderBy('nama_siswa')->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // Admin: Import Excel file
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'kelas' => 'required|string',
            'asesmen' => 'required|string',
            'tanggal' => 'required|date'
        ]);

        try {
            // Optional: you can clear old data for this class if you want a fresh import
            // \App\Models\Administrasi::where('kelas', $request->kelas)->delete();

            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\AdministrasiImport($request->kelas, $request->asesmen, $request->tanggal), 
                $request->file('file')
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Data administrasi kelas ' . $request->kelas . ' berhasil diimport!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengimport data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Admin: Delete all data for a specific class and asesmen
    public function deleteBatch(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string',
            'asesmen' => 'required|string'
        ]);

        \App\Models\Administrasi::where('kelas', $request->kelas)
            ->where('asesmen', $request->asesmen)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua data administrasi kelas ' . $request->kelas . ' untuk asesmen ' . $request->asesmen . ' berhasil dihapus!'
        ]);
    }
}
