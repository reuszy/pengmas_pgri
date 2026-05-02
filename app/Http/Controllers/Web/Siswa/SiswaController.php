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
        // http://localhost/siswa/dashboard?test_date=2025-09-15

        $siswa        = $this->siswaService->findOrFail(session('nis'));
        $tarif        = $this->pembayaranService->getTarifAktif();
        $tahunAjaran  = $this->pembayaranService->getTahunAjaran();

        $tagihan = $this->pembayaranService->generateTagihanBulanan($siswa->nis, $tarif, $tahunAjaran);

        return view('siswa.dashboard', compact('siswa', 'tagihan', 'tahunAjaran'));
    }

    public function pembayaran(Request $request)
    {
        if (app()->environment('local') && $request->has('test_date')) {
            Carbon::setTestNow($request->test_date);
        }
        // http://localhost/siswa/dashboard?test_date=2025-09-15

        $siswa        = $this->siswaService->findOrFail(session('nis'));
        $tahunAjaran  = $this->pembayaranService->getTahunAjaran();
        $semuaTarif   = TarifPembayaran::orderBy('total_cicilan')->get();
        $jenisPembayaran = $request->get('jenis_pembayaran', $semuaTarif->first()->jenis_pembayaran ?? 'SPP Bulanan');
        $tarif        = $this->pembayaranService->getTarifAktif($jenisPembayaran);
        $tagihan      = $this->pembayaranService->generateTagihanBulanan($siswa->nis, $tarif, $tahunAjaran);

        return view ('siswa.pembayaran', compact(
            'siswa',
            'tarif',
            'tagihan',
            'tahunAjaran',
            'semuaTarif',        
            'jenisPembayaran',
        ));
    }

    public function pembayaranQris(Request $request)
    {
        $siswa           = $this->siswaService->findOrFail(session('nis'));
        $tahunAjaran     = $this->pembayaranService->getTahunAjaran();
        $semuaTarif      = TarifPembayaran::orderBy('total_cicilan')->get();
        $jenisPembayaran = $request->get('jenis_pembayaran', $semuaTarif->first()->jenis_pembayaran ?? 'SPP Bulanan');
        $tarif           = $this->pembayaranService->getTarifAktif($jenisPembayaran);
        $tagihan         = $this->pembayaranService->generateTagihanBulanan($siswa->nis, $tarif, $tahunAjaran);

        return view('siswa.pembayaran_qris', compact
        (
            'siswa',
            'tarif',
            'tagihan',
            'tahunAjaran',
            'semuaTarif',
            'jenisPembayaran'
        ));
    }

}