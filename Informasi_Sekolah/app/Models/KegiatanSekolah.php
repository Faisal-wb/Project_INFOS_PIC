<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanSekolah extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'lokasi', 'gambar'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }
}
