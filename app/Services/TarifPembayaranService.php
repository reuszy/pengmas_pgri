<?php

namespace App\Services;

use App\Models\TarifPembayaran;

class TarifPembayaranService
{
    public function getAll()
    {
        return TarifPembayaran::orderBy('id_tarif', 'DESC')->get();
    }


    public function findOrFail(int $id): TarifPembayaran
    {
        return TarifPembayaran::findOrFail($id);
    }


    public function create(array $data): TarifPembayaran
    {
        return TarifPembayaran::create([
            'jenis_pembayaran' => $data['jenis_pembayaran'],
            'nominal'          => $data['nominal'],
            'total_cicilan'    => $data['total_cicilan'] ?? 1,
        ]);
    }


    public function update(int $id, array $data): TarifPembayaran
    {
        $tarif = $this->findOrFail($id);
        $tarif->update([
            'jenis_pembayaran' => $data['jenis_pembayaran'] ?? $tarif->jenis_pembayaran,
            'nominal'          => $data['nominal'] ?? $tarif->nominal,
            'total_cicilan'    => $data['total_cicilan'] ?? $tarif->total_cicilan,
        ]);
        return $tarif->fresh();
    }


    public function delete(int $id): void
    {
        $this->findOrFail($id)->delete();
    }
}