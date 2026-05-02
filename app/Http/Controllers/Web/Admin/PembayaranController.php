<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\PembayaranService;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function __construct(private PembayaranService $pembayaranService) {}


    public function filter(Request $request)
    {
        $status = $request->status;
        $bulan = $request->bulan ? (int)$request->bulan : null;
        $tahunAjaran = $request->tahun_ajaran;

        $hasil = $this->pembayaranService->filter($status, $bulan, $tahunAjaran);
        return response()->json($hasil);
    }
}
