<?php

namespace App\Services;

use App\Models\Pengguna;

class AdminService
{
    public function getAll()
    {
        return Pengguna::where('role', 'admin')->get();
    }


    public function findOrFail(int $id): Pengguna
    {
        return Pengguna::where('id_pengguna', $id)
            ->where('role', 'admin')
            ->firstOrFail();
    }


    public function create(array $data): Pengguna
    {
        return Pengguna::create([
            'nama_pengguna' => $data['nama_pengguna'],
            'username'      => $data['username'],
            'password'      => bcrypt($data['password']),
            'role'          => 'admin',
        ]);
    }


    public function update(int $id, array $data): Pengguna
    {
        $admin = $this->findOrFail($id);

        $updateData = [
            'nama_pengguna' => $data['nama_pengguna'] ?? $admin->nama_pengguna,
            'username'      => $data['username'] ?? $admin->username,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }

        $admin->update($updateData);
        return $admin->fresh();
    }


    public function delete(int $id): void
    {
        $this->findOrFail($id)->delete();
    }
}