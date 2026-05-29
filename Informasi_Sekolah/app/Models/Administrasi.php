<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrasi extends Model
{
    protected $table = 'administrasis';
    protected $fillable = ['nis', 'nama_siswa', 'kelas', 'status_bayar', 'asesmen', 'tanggal'];
}
