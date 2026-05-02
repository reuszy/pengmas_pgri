<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TarifPembayaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_tarif'         => $this->id_tarif,
            'jenis_pembayaran' => $this->jenis_pembayaran,
            'nominal'          => $this->nominal,
            'total_cicilan'    => $this->total_cicilan,
            'keterangan'       => $this->total_cicilan > 1
                                    ? "Cicilan {$this->total_cicilan}x @ Rp " . number_format($this->nominal, 0, ',', '.')
                                    : 'Bayar Lunas',
            'nominal_rupiah'   => 'Rp ' . number_format($this->nominal, 0, ',', '.'),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
