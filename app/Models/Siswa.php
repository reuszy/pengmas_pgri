<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nis',
        'id_pengguna',
        'tanggal_lahir',
        'id_kelas',
        'nomor_telepon',
        'email',
        'password'
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }


    public function dataSiswa()
    {
        return $this->hasOne(DataSiswa::class, 'nis', 'nis');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'nis', 'nis');
    }

}
