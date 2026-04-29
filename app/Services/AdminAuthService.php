<?php

namespace App\Services;

use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use function PHPUnit\Framework\returnArgument;

class AdminAuthService
{
    public function login(string $username, string $password): bool
    {
        $pengguna = Pengguna::where('username', $username)
            ->where('role', 'admin')
            ->first();

        if ($pengguna && Hash::check($password, $pengguna->password)) {
            session([
                'admin_logged_in' => true,
                'admin_id'        => $pengguna->id_pengguna,
                'admin_nama'      => $pengguna->nama_pengguna,
            ]);

            return true;
        }

        return false;
    }

    
    public function logout(): void
    {
        session()->forget(['admin_logged_in', 'admin_id', 'admin_nama']);
    }


    public function check(): bool
    {
        return session('admin_logged_in', false);
    }

}