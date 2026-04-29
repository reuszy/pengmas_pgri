<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\DataSiswa;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\TarifPembayaran;

class SiswaController extends Controller
{
    public function loginSubmit(Request $request)
    {
        $request->validate([
            'nis' => 'required',
            'password' => 'required'
        ]);

        $siswa = \App\Models\Siswa::where('nis', $request->nis)->first();

        if (!$siswa) {
            return back()->withErrors(['login_error' => 'NIS tidak ditemukan']);
        }

        if (!Hash::check($request->password, $siswa->password)) {
            return back()->withErrors(['login_error' => 'Password salah']);
        }

        // AMBIL NAMA DARI TABLE data_siswa
        $profil = \App\Models\DataSiswa::where('nis', $siswa->nis)->first();

        session([
            'nis' => $siswa->nis,
            'nama' => $profil ? $profil->nama : null
        ]);

        return redirect('/siswa/dashboard');
    }
    public function daftar(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'tanggal_lahir' => 'required',
            'kelas' => 'required',
            'nis' => 'required|unique:siswa,nis',
            'email' => 'required|email|unique:siswa,email',
            'password' => 'required',
            'telepon' => 'required',
        ]);

        try {
            $pengguna = \App\Models\Pengguna::create([
                'nama_pengguna' => $request->name,
                'username' => $request->nis,
                'password' => bcrypt($request->password),
                'role' => 'siswa'
            ]);

            Siswa::create([
                'nis' => $request->nis,
                'id_pengguna' => $pengguna->id_pengguna,
                'tanggal_lahir' => $request->tanggal_lahir,
                'id_kelas' => $request->kelas,
                'nomor_telepon' => $request->telepon,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            DataSiswa::create([
                'nis' => $request->nis,
                'nama' => $request->name,
                'tanggal_lahir' => $request->tanggal_lahir,
                'id_kelas' => $request->kelas,
                'nomor_telepon' => $request->telepon,
                'email' => $request->email,
            ]);

            return redirect()->route('siswa.login')
                ->with('success', 'Pendaftaran berhasil! Silakan login.');
        } catch (\Exception $e) {
            \Log::error('Error during registration:', ['error' => $e->getMessage()]);
            return back()->withErrors(['daftar_error' => 'Pendaftaran gagal: ' . $e->getMessage()]);
        }
    }

    public function showDaftarForm()
    {
        $kelas = Kelas::all();
        return view('siswa.daftar', compact('kelas'));
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('beranda');
    }

    public function dashboard()
    {
        $nis = session('nis');

        // ambil data lengkap dari DataSiswa
        $siswa = DataSiswa::where('nis', $nis)->first();
        $tarif = TarifPembayaran::all();
        $pembayaran = Pembayaran::where('nis', $nis)->get();

        $tagihan = [];
        foreach ($tarif as $t) {
            $sudah_bayar = $pembayaran->where('jenis_pembayaran', $t->jenis_pembayaran)
                ->where('status', 'lunas');
            $sisa = $t->nominal - $sudah_bayar->sum('jumlah');
            $tagihan[] = [
                'jenis_pembayaran' => $t->jenis_pembayaran,
                'nominal' => $t->nominal,
                'sudah_bayar' => $sudah_bayar->first()->status ?? "belum",
                'sisa' => $sisa
            ];
        }

        return view('siswa.dashboard', compact('siswa', 'tagihan'));
    }


    public function pembayaran()
    {
        $nis = session('nis');
        $siswa = \App\Models\DataSiswa::where('nis', $nis)->first();
        $tarif = TarifPembayaran::first();
        $pembayaran = Pembayaran::where('nis', $nis)
            ->where('jenis_pembayaran', $tarif->jenis_pembayaran)
            ->where('status', 'lunas')
            ->first()->status ?? "belum";

        return view('siswa.pembayaran', compact('siswa', 'tarif', 'pembayaran'));
    }

    public function pembayaranQris()
    {
        $nis = session('nis');
        $siswa = \App\Models\DataSiswa::where('nis', $nis)->first();
        $tarif = TarifPembayaran::first();
        $pembayaran = Pembayaran::where('nis', $nis)
            ->where('jenis_pembayaran', $tarif->jenis_pembayaran)
            ->where('status', 'lunas')
            ->first()->status ?? "belum";

        return view('siswa.pembayaran_qris', compact('siswa', 'tarif', 'pembayaran'));
    }

    public function lupaPassword()
    {
        return view('siswa.lupa_password');
    }


    public function lupaPasswordSubmit(Request $request)
    {
        $request->validate([
            'nis' => 'required',
            'tanggal_lahir' => 'required|date'
        ]);

        // Cari siswa berdasarkan NIS dan tanggal lahir di tabel DataSiswa
        $siswa = Siswa::where('nis', $request->nis)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->first();

        if (!$siswa) {
            return back()->withErrors(['Data tidak cocok, periksa kembali NIS dan Tanggal Lahir.']);
        }

        // Simpan ID siswa di session agar bisa dipakai di halaman atur password
        session(['reset_siswa_nis' => $siswa->nis]);

        return redirect()->route('siswa.atur_password');
    }


    //Atur Password

    public function aturPassword()
    {
        return view('siswa.atur_pw'); // pastikan nama file blade benar
    }

    // Menyimpan password baru
    public function aturPasswordSubmit(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed'
        ]);

        // Ambil ID dari session
        $siswa = Siswa::where('nis', session('reset_siswa_nis'))->first();

        if (!$siswa) {
            return redirect()->route('siswa.lupa_password')->with('error', 'Siswa tidak ditemukan.');
        }

        // ===========================
        // 1️⃣ UPDATE PASSWORD di TABEL SISWA
        // ===========================
        $siswa->password = bcrypt($request->password);
        $siswa->save();

        //hapus sesi
        session()->forget('reset_siswa_nis');

        return redirect()->route('siswa.login');
    }


    public function beranda()
    {
        return view('siswa.beranda');
    }

    public function index()
    {
        return Siswa::with('pengguna')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|integer|unique:siswa',
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'tanggal_lahir' => 'nullable|date',
            'kelas' => 'nullable|string|max:20',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|string|max:60',
            'password' => 'nullable|string|max:255'
        ]);


        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        return Siswa::create($validated);
    }
    public function show($nis)
    {
        return Siswa::with('pengguna')->findOrFail($nis);
    }
    public function update(Request $request, $nis)
    {
        $siswa = Siswa::findOrFail($nis);

        $validated = $request->all();

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $siswa->update($validated);

        return $siswa;
    }
    public function destroy($nis)
    {
        return Siswa::destroy($nis);
    }
}
