<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSiswa extends Model
{
    use HasFactory;

    protected $table = 'data_siswa';
    protected $primaryKey = 'id_data';

    public $timestamps = false;

    protected $fillable = [
        'nis',
        'nama',
        'tanggal_lahir',
        'id_kelas',
        'kelas',
        'nomor_telepon',
        'email',
    ];

    public function siswa()
{
    return $this->belongsTo(Siswa::class, 'nis', 'nis');
}

public function kelas()
{
    return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
}



}
