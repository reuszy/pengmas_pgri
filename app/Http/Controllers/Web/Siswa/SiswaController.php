<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TarifPembayaran;
use App\Services\PembayaranService;
use App\Services\SiswaService;
use Carbon\Carbon;
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

}