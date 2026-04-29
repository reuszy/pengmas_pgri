<?php

namespace App\Services;

use App\Models\Kelas;

class KelasService
{
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Kelas::orderBy('id', 'DESC')->get();
    }


    public function findOrFail(int $id): Kelas
    {
        return Kelas::findOrFail($id);
    }


    public function create(array $data): Kelas
    {
        return Kelas::create([
            'nama_kelas' => $data['nama_kelas'],
        ]);
    }


    public function update(int $id, array $data): Kelas
    {
        $kelas = $this->findOrFail($id);
        $kelas->update([
            'nama_kelas' => $data['nama_kelas'],
        ]);
        return $kelas->fresh();
    }

    
    public function delete(int $id): void
    {
        $kelas = $this->findOrFail($id);
        $kelas->delete();
    }
}