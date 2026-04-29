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
        'nama',
        'tanggal_lahir',
        'id_kelas',
        'nomor_telepon',
        'email',
        'password'
    ];


    protected $hidden = [
        'password',
    ];


    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }


    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'nis', 'nis');
    }

}
