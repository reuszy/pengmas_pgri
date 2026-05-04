<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Pengguna;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaAuthService
{
    public function login(string $nis, string $password): Siswa
    {
        $siswa = Siswa::where('nis', $nis)->first();

        if (!$siswa) {
            throw new Exception('NIS tidak ditemukan');
        }

        if (!$siswa->pengguna || !Hash::check($password, $siswa->pengguna->password)) {
            throw new Exception('Password salah');
        }

        return $siswa;
    }

    public function register(array $data): Siswa
    {
        $pengguna = Pengguna::create([
            'nama_pengguna' => $data['name'],
            'username'      => $data['nis'],
            'password'      => bcrypt($data['password']),
            'role'          => 'siswa'
        ]);

        return Siswa::create([
            'nis' => $data['nis'],
            'id_pengguna' => $pengguna->id_pengguna,
            'nama' => $data['name'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'id_kelas' => $data['kelas'],
            'nomor_telepon' => $data['telepon'],
            'email' => $data['email'],
        ]);
    }

    public function resetPassword(string $nis, string $password): void
    {
        $siswa = Siswa::with('pengguna')->where('nis', $nis)->firstOrFail();

        $siswa->pengguna->update([
            'password'  => bcrypt($password),
        ]);
    }

    public function findByNisAndTanggalLahir(string $nis, string $tanggalLahir): ?Siswa
    {
        return Siswa::where('nis', $nis)
            ->where('tanggal_lahir', $tanggalLahir)
            ->first();
    }
}