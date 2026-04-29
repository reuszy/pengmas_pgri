<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiswaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'nis'           => $this->nis,
            'nama'          => $this->nama,
            'kelas'         => $this->kelas->nama_kelas ?? '-'
        ];
    }
}