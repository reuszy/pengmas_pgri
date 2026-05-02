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
        'cicilan_ke',
        'total_cicilan',
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


    // Helper: Cicilan atau bukan
    public function getIsCicilanAttribute(): bool
    {
        return $this->total_cicilan > 1;
    }


    // Helper: Cicilan bulanan sudah lunas atau belum
    public function semuaCicilanLunas(): bool
    {
        if (!$this->is_cicilan) return $this->status == 'lunas';

        $lunas = Pembayaran::where('nis', $this->nis)
            ->where('bulan', $this->bulan)
            ->where('tahun_ajaran', $this->tahun_ajaran)
            ->where('status', 'lunas')
            ->count();

        return $lunas >= $this->total_cicilan;
    }
}
