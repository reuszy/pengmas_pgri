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
        $hasil = $this->pembayaranService->filter($request->status);
        return response()->json($hasil);
    }
}
