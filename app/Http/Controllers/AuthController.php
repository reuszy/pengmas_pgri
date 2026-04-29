<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function daftar(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'tanggal_lahir' => 'required',
            'kelas' => 'required',
            'nis' => 'required|unique:users,nis',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'telepon' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'tanggal_lahir' => $request->tanggal_lahir,
            'kelas' => $request->kelas,
            'nis' => $request->nis,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telepon' => $request->telepon,
            'role' => 'siswa',
        ]);

        return redirect()->route('beranda')->with('success', 'Akun berhasil dibuat!');
    }

    public function showSiswaLogin()
    {
        return view('siswa.login');
    }

    public function siswaLoginProcess(Request $request)
    {
        $request->validate([
            'nis' => 'required',
            'password' => 'required'
        ], [
            'nis.required' => 'Silakan isi NIS dan Password!',
            'password.required' => 'Silakan isi NIS dan Password!'
        ]);

        $user = User::where('nis', $request->nis)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role === 'siswa') {
                Auth::login($user);
                return redirect()->route('dashboard.siswa');
            } else {
                return back()->withErrors(['login_error' => 'Anda bukan siswa!']);
            }
        }

        return back()->withErrors(['login_error' => 'NIS atau Password salah!']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function aturPasswordSubmit(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);

        // Ambil ID siswa dari session
        $siswaId = session('siswa_id');

        if (!$siswaId) {
            return redirect()->route('siswa.login')->with('error', 'Session kadaluarsa, silakan ulangi proses.');
        }

        // Ambil data siswa
        $siswa = User::find($siswaId);

        if (!$siswa) {
            return redirect()->route('siswa.login')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Update password
        $siswa->password = bcrypt($request->password);
        $siswa->save();

        // Hapus session biar aman
        session()->forget('siswa_id');

        return redirect()->route('siswa.login')->with('success', 'Password berhasil diubah. Silakan login.');
    }


    public function showAdminLogin()
    {
        return view('admin.login_admin');
    }

    public function adminLoginProcess(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role === 'admin') {
                Auth::login($user);
                return redirect()->route('dashboard.admin');
            } else {
                return back()->withErrors(['login_error' => 'Anda bukan admin!']);
            }
        }

        return back()->withErrors(['login_error' => 'Email atau Password salah!']);
    }

}
