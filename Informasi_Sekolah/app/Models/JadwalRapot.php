<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalRapot extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'kelas', 'lokasi'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }
}
