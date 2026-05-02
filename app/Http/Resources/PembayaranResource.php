<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PembayaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_pembayaran'    => $this->id_pembayaran,
            'nis'              => $this->nis,
            'nama_siswa'       => $this->siswa?->nama,
            'nama_kelas'       => $this->kelas?->nama_kelas,
            'jenis_pembayaran' => $this->jenis_pembayaran,
            'order_id'         => $this->order_id,
            'jumlah'           => $this->jumlah,
            'jumlah_rupiah'    => 'Rp ' . number_format($this->jumlah, 0, ',', '.'),
            'tanggal_bayar'    => $this->tanggal_bayar,
            'status'           => $this->status,
        ];
    }
}
