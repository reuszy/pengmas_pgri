<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TarifPembayaran;
use App\Services\PembayaranService;
use App\Services\SiswaService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function __construct(
        private SiswaService $siswaService,
        private PembayaranService $pembayaranService,
    ) {}


    public function beranda()
    {
        return view('siswa.beranda');
    }

    public function dashboard(Request $request)
    {
        // Testing Only!
        if (app()->environment('local') && $request->has('test_date')) {
            Carbon::setTestNow($request->test_date);
        }

        $siswa        = $this->siswaService->findOrFail(session('nis'));
        $tarif        = $this->pembayaranService->getTarifAktif();
        $tahunAjaran  = $this->pembayaranService->getTahunAjaran();

        $tagihan = $this->pembayaranService->generateTagihanBulanan($siswa->nis, $tarif, $tahunAjaran);

        return view('siswa.dashboard', compact('siswa', 'tagihan', 'tahunAjaran'));
    }

    public function pembayaran()
    {
        $siswa        = $this->siswaService->findOrFail(session('nis'));
        $tarif        = $this->pembayaranService->getTarifAktif();
        $tahunAjaran  = $this->pembayaranService->getTahunAjaran();
        $tagihan      = $this->pembayaranService->generateTagihanBulanan($siswa->nis, $tarif, $tahunAjaran);

        return view('siswa.pembayaran', compact('siswa', 'tarif', 'tagihan', 'tahunAjaran'));
    }

    public function pembayaranQris()
    {
        $siswa        = $this->siswaService->findOrFail(session('nis'));
        $tarif        = $this->pembayaranService->getTarifAktif();
        $tahunAjaran  = $this->pembayaranService->getTahunAjaran();
        $tagihan      = $this->pembayaranService->generateTagihanBulanan($siswa->nis, $tarif, $tahunAjaran);

        return view('siswa.pembayaran_qris', compact('siswa', 'tarif', 'tagihan', 'tahunAjaran'));
    }


    public function pengaturan()
    {
        $siswa = $this->siswaService->getProfil(session('nis'));

        return view('siswa.pengaturan', compact('siswa'));
    }


    public function updateProfile(Request $request)
    {
        $request->validate([
            'email'         => 'required|email|unique:siswa,email,' . session('nis') . ',nis',
            'nomor_telepon' => 'required|string|max:20',
        ]);

        try {
            $siswa = $this->siswaService->updateProfil(session('nis'), $request->all());

            // Update session nama kalau berubah
            return redirect()->route('siswa.pengaturan')
                ->with('success', 'Profil berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function gantiPassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ], [
            'password_baru.confirmed' => 'Konfirmasi password baru tidak sesuai.',
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
        ]);

        try {
            $this->siswaService->gantiPassword(
                session('nis'),
                $request->password_lama,
                $request->password_baru,
            );

            return redirect()->route('siswa.pengaturan')
                ->with('success', 'Password berhasil diubah. Silakan login ulang.')
                ->withCookie(cookie()->forget('laravel_session'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}