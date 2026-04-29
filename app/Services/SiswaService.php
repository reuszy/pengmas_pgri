<?php

namespace App\Services;

use App\Models\Pengguna;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class SiswaService
{
    public function getAll()
    {
        return Siswa::with('kelas')->orderBy('nis')->get();
    }

    public function findOrFail(string $nis): Siswa
    {
        return Siswa::with('kelas')->findOrFail($nis);
    }

    public function create(array $data): Siswa
    {
        return DB::transaction(function () use ($data) {
            $pengguna = Pengguna::create([
                'nama_pengguna' => $data['nama'],
                'username'      => $data['nis'],
                'password'      => bcrypt($data['password']),
                'role'          => 'siswa'
            ]);

            $data['id_pengguna'] = $pengguna->id_pengguna;
            $data['password']    = bcrypt($data['password']);
            $data['tanggal_lahir'] = date('Y-m-d', strtotime($data['tanggal_lahir']));
            return Siswa::create($data);
        });
    }

    public function update(string $nis, array $data): Siswa
    {
        return DB::transaction(function () use ($nis, $data) {
            $siswa = $this->findOrFail($nis);

            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }
            if (!empty($data['tanggal_lahir'])) {
                $data['tanggal_lahir'] = date('Y-m-d', strtotime($data['tanggal_lahir']));
            }

            $siswa->update($data);

            if ($siswa->id_pengguna) {
                $penggunaData = [];
                if (isset($data['nama'])) {
                    $penggunaData['nama_pengguna'] = $data['nama'];
                }
                if (isset($data['password'])) {
                    $penggunaData['password'] = $data['password'];
                }
                if (!empty($penggunaData)) {
                    Pengguna::where('id_pengguna', $siswa->id_pengguna)->update($penggunaData);
                }
            }

            return $siswa->fresh();
        });
    }

    public function delete(string $nis): void
    {
        DB::transaction(function () use ($nis) {
            $siswa = $this->findOrFail($nis);
            $idPengguna = $siswa->id_pengguna;
            
            $siswa->delete();

            if ($idPengguna) {
                Pengguna::where('id_pengguna', $idPengguna)->delete();
            }
        });
    }
}