<?php

namespace App\Services;

use App\Models\Pengguna;
use App\Models\Siswa;
use App\Models\Kelas;
use Exception;
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


    // Profil siswa untuk 'Pengaturan Akun'
    public function getProfil(string $nis): Siswa
    {
        return Siswa::with('kelas')->where('nis', $nis)->firstOrFail();
    } 


    public function updateProfil(string $nis, array $data): Siswa
    {
        $siswa = $this->findOrFail($nis);

        $siswa->update([
            'email'         => $data['email'] ?? $siswa->email,
            'tanggal_lahir' => $data['tanggal_lahir'] ?? $siswa->tanggal_lahir,
            'nomor_telepon' => $data['nomor_telepon'] ?? $siswa->nomor_telepon,
        ]);

        return $siswa->fresh('kelas');
    }


    public function gantiPassword(string $nis, string $passwordLama, string $passwordBaru): void
    {
        $siswa = Siswa::with('pengguna')->where('nis', $nis)->firstOrFail();

        if (!$siswa->pengguna) {
            throw new Exception('Data akun tidak ditemukan.');
        }

        if (!\Illuminate\Support\Facades\Hash::check($passwordLama, $siswa->pengguna->password)) {
            throw new Exception('Password lama tidak sesuai.');
        }

        $siswa->pengguna->update([
            'password' => bcrypt($passwordBaru),
        ]);
    }
}