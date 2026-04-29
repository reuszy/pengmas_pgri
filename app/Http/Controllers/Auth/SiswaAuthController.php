<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SiswaAuthService;
use App\Models\Kelas;
use Illuminate\Http\Request;

class SiswaAuthController extends Controller
{
    public function __construct(private SiswaAuthService $authService) {}

    public function showLoginForm()
    {
        return view('siswa.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis'      => 'required',
            'password' => 'required'
        ]);

        try {
            $siswa = $this->authService->login($request->nis, $request->password);

            session([
                'nis'  => $siswa->nis,
                'nama' => $siswa->nama,
            ]);

            return redirect('/siswa/dashboard');
        } catch (\Exception $e) {
            return back()->withErrors(['login_error' => $e->getMessage()]);
        }
    }

    public function showDaftarForm()
    {
        $kelas = Kelas::all();
        return view('siswa.daftar', compact('kelas'));
    }

    public function daftar(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'tanggal_lahir' => 'required',
            'kelas'         => 'required',
            'nis'           => 'required|unique:siswa,nis',
            'email'         => 'required|email|unique:siswa,email',
            'password'      => 'required',
            'telepon'       => 'required',
        ]);

        try {
            $this->authService->register($request->all());
            return redirect()->route('siswa.login')
                ->with('success', 'Pendaftaran berhasil! Silakan login.');
        } catch (\Exception $e) {
            return back()->withErrors(['daftar_error' => 'Pendaftaran gagal: ' . $e->getMessage()]);
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('beranda');
    }

    public function showLupaPasswordForm()
    {
        return view('siswa.lupa_password');
    }

    public function lupaPasswordSubmit(Request $request)
    {
        $request->validate([
            'nis'           => 'required',
            'tanggal_lahir' => 'required|date'
        ]);

        $siswa = $this->authService->findByNisAndTanggalLahir(
            $request->nis,
            $request->tanggal_lahir
        );

        if (!$siswa) {
            return back()->withErrors(['Data tidak cocok, periksa kembali NIS dan Tanggal Lahir.']);
        }

        session(['reset_siswa_nis' => $siswa->nis]);
        return redirect()->route('siswa.atur_password');
    }

    public function showAturPasswordForm()
    {
        return view('siswa.atur_pw');
    }

    public function aturPasswordSubmit(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed'
        ]);

        $nis = session('reset_siswa_nis');

        if (!$nis) {
            return redirect()->route('siswa.lupa_password')
                ->with('error', 'Sesi tidak valid, silakan ulangi.');
        }

        $this->authService->resetPassword($nis, $request->password);
        session()->forget('reset_siswa_nis');

        return redirect()->route('siswa.login');
    }
}