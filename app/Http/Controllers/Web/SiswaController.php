<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TarifPembayaran;
use App\Services\SiswaService;

class SiswaController extends Controller
{
    public function __construct(private SiswaService $siswaService) {}

    public function beranda()
    {
        return view('siswa.beranda');
    }

    public function dashboard()
    {
        $siswa    = $this->siswaService->findOrFail(session('nis'));
        $tarif    = TarifPembayaran::all();
        $pembayaran = Pembayaran::where('nis', session('nis'))->get();

        $tagihan = $tarif->map(function ($t) use ($pembayaran) {
            $sudahBayar = $pembayaran
                ->where('jenis_pembayaran', $t->jenis_pembayaran)
                ->where('status', 'lunas');

            return [
                'jenis_pembayaran' => $t->jenis_pembayaran,
                'nominal'          => $t->nominal,
                'sudah_bayar'      => $sudahBayar->first()->status ?? 'belum',
                'sisa'             => $t->nominal - $sudahBayar->sum('jumlah'),
            ];
        });

        return view('siswa.dashboard', compact('siswa', 'tagihan'));
    }

    public function pembayaran()
    {
        $siswa  = $this->siswaService->findOrFail(session('nis'));
        $tarif  = TarifPembayaran::first();
        $status = Pembayaran::where('nis', session('nis'))
            ->where('jenis_pembayaran', $tarif->jenis_pembayaran)
            ->where('status', 'lunas')
            ->first()->status ?? 'belum';

        return view('siswa.pembayaran', compact('siswa', 'tarif', 'status'));
    }

    public function pembayaranQris()
    {
        $siswa  = $this->siswaService->findOrFail(session('nis'));
        $tarif  = TarifPembayaran::first();
        $status = Pembayaran::where('nis', session('nis'))
            ->where('jenis_pembayaran', $tarif->jenis_pembayaran)
            ->where('status', 'lunas')
            ->first()->status ?? 'belum';

        return view('siswa.pembayaran_qris', compact('siswa', 'tarif', 'status'));
    }
}