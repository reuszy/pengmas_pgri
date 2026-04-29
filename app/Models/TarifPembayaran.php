<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifPembayaran extends Model
{
    use HasFactory;

    protected $table = 'tarif_pembayaran';
    protected $primaryKey = 'id_tarif';

    protected $fillable = [
        'jenis_pembayaran',
        'nominal'
    ];

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'jenis_pembayaran', 'jenis_pembayaran');
    }
}
