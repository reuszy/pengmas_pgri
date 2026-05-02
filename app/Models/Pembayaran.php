<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'nis',
        'id_kelas',
        'jenis_pembayaran',
        'bulan',
        'tahun_ajaran',
        'order_id',
        'jumlah',
        'tanggal_bayar',
        'status',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }


    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }


    public function tarif()
    {
        return $this->belongsTo(TarifPembayaran::class, 'jenis_pembayaran', 'jenis_pembayaran');
    }
}
