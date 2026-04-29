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
            'nominal_rupiah'   => 'Rp ' . number_format($this->nominal, 0, ',', '.'),
        ];
    }
}
