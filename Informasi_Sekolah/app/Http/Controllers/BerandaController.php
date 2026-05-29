<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use App\Models\KegiatanSekolah;
use App\Models\JadwalRapot;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BerandaController extends Controller
{
    /**
     * Menampilkan halaman beranda dengan semua pengumuman terbaru.
     * Data diambil dari 3 sumber: Info Libur, Kegiatan Sekolah, Jadwal Rapot.
     */
    public function index(Request $request)
    {
        // Ambil data dari masing-masing tabel
        $infoLibur = HariLibur::orderBy('tanggal', 'desc')->get();
        $kegiatan  = KegiatanSekolah::orderBy('tanggal', 'desc')->get();
        $jadwalRapot = JadwalRapot::orderBy('tanggal', 'desc')->get();

        // Gabungkan semua pengumuman jadi satu collection untuk "Pengumuman Terbaru"
        $pengumuman = collect();

        foreach ($infoLibur as $item) {
            $pengumuman->push([
                'id'        => $item->id,
                'judul'     => $item->judul,
                'deskripsi' => $item->deskripsi,
                'tanggal'   => Carbon::parse($item->tanggal),
                'tipe'      => 'libur',
            ]);
        }

        foreach ($kegiatan as $item) {
            $pengumuman->push([
                'id'        => $item->id,
                'judul'     => $item->judul,
                'deskripsi' => $item->deskripsi,
                'tanggal'   => Carbon::parse($item->tanggal),
                'tipe'      => 'kegiatan',
            ]);
        }

        foreach ($jadwalRapot as $item) {
            $pengumuman->push([
                'id'        => $item->id,
                'judul'     => $item->judul,
                'deskripsi' => $item->deskripsi,
                'tanggal'   => Carbon::parse($item->tanggal),
                'tipe'      => 'rapot',
            ]);
        }

        // Urutkan berdasarkan tanggal terbaru dan ambil 10 teratas
        $pengumuman = $pengumuman->sortByDesc('tanggal')->take(10)->values();

        // Kelompokkan berdasarkan tanggal untuk tampilan
        $pengumumanPerTanggal = $pengumuman->groupBy(function ($item) {
            $tanggal = $item['tanggal'];
            if ($tanggal->isToday()) {
                return 'Hari ini';
            }
            // Format: "Rabu, 28 Jan 26"
            return $tanggal->translatedFormat('l, d M y');
        });

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'info_libur'             => $infoLibur,
                    'kegiatan_sekolah'       => $kegiatan,
                    'jadwal_rapot'           => $jadwalRapot,
                    'pengumuman_terbaru'     => $pengumuman,
                    'pengumuman_per_tanggal' => $pengumumanPerTanggal,
                ],
            ]);
        }

        return view('beranda', compact(
            'infoLibur',
            'kegiatan',
            'jadwalRapot',
            'pengumumanPerTanggal'
        ));
    }

    /**
     * Menampilkan detail info libur sekolah.
     */
    public function showLibur($id)
    {
        $libur = HariLibur::findOrFail($id);
        return view('info.libur', compact('libur'));
    }

    /**
     * Menampilkan daftar semua info libur.
     */
    public function semuaLibur(Request $request)
    {
        $semuaLibur = HariLibur::orderBy('tanggal', 'desc')->paginate(10);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $semuaLibur]);
        }

        return view('info.semua-libur', compact('semuaLibur'));
    }

    /**
     * Menampilkan detail kegiatan sekolah.
     */
    public function showKegiatan($id)
    {
        $kegiatan = KegiatanSekolah::findOrFail($id);
        return view('info.kegiatan', compact('kegiatan'));
    }

    /**
     * Menampilkan daftar semua kegiatan sekolah.
     */
    public function semuaKegiatan(Request $request)
    {
        $semuaKegiatan = KegiatanSekolah::orderBy('tanggal', 'desc')->paginate(10);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $semuaKegiatan]);
        }

        return view('info.semua-kegiatan', compact('semuaKegiatan'));
    }

    /**
     * Menampilkan detail jadwal pengambilan rapot.
     */
    public function showRapot($id)
    {
        $jadwal = JadwalRapot::findOrFail($id);
        return view('info.rapot', compact('jadwal'));
    }

    /**
     * Menampilkan daftar semua jadwal rapot.
     */
    public function semuaRapot(Request $request)
    {
        $semuaJadwal = JadwalRapot::orderBy('tanggal', 'desc')->paginate(10);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $semuaJadwal]);
        }

        return view('info.semua-rapot', compact('semuaJadwal'));
    }
}
