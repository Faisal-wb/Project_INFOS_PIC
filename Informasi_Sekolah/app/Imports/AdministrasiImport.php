<?php

namespace App\Imports;

use App\Models\Administrasi;
use Maatwebsite\Excel\Concerns\ToModel;

class AdministrasiImport implements ToModel, \Maatwebsite\Excel\Concerns\WithHeadingRow
{
    protected $kelas;
    protected $asesmen;
    protected $tanggal;

    public function __construct($kelas, $asesmen, $tanggal)
    {
        $this->kelas = $kelas;
        $this->asesmen = $asesmen;
        $this->tanggal = $tanggal;
    }

    public function model(array $row)
    {
        // Skip empty rows
        if (!isset($row['nis'])) {
            return null;
        }

        return Administrasi::updateOrCreate(
            [
                'nis' => $row['nis'],
                'kelas' => $this->kelas,
                'asesmen' => $this->asesmen
            ],
            [
                'nama_siswa' => $row['nama_siswa'] ?? $row['nama'] ?? 'Tanpa Nama',
                'status_bayar' => $row['status_bayar'] ?? $row['status'] ?? 'Belum Lunas',
                'asesmen' => $this->asesmen,
                'tanggal' => $this->tanggal
            ]
        );
    }
}
