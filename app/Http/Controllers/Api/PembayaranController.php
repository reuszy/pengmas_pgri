<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PembayaranResource;
use App\Models\Pembayaran;
use App\Services\PembayaranService;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function __construct(private PembayaranService $pembayaranService) {}


    /**
     * Daftar semua pembayaran
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        $pembayaran = Pembayaran::with(['siswa', 'kelas'])->latest()->get();
        return PembayaranResource::collection($pembayaran)
            ->additional(['success' => true, 'message' => 'Daftar pembayaran']);
    }


    /**
     * Detail pembayaran
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(int $id)
    {
        $pembayaran = Pembayaran::with(['siswa', 'kelas'])->findOrFail($id);
        return (new PembayaranResource($pembayaran))
            ->additional(['success' => true, 'message' => 'Detail pembayaran']);
    }


    /**
     * Buat snap token Midtrans
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function snapToken(Request $request)
    {
        $request->validate([
            'nis'              => 'required|exists:siswa,nis',
            'jenis_pembayaran' => 'required|exists:tarif_pembayaran,jenis_pembayaran',
        ]);

        try {
            $result = $this->pembayaranService->buatSnapToken(
                $request->nis,
                $request->jenis_pembayaran
            );
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Webhook notifikasi dari Midtrans
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notification(Request $request)
    {
        $berhasil = $this->pembayaranService->konfirmasiPembayaran($request->all());

        if (!$berhasil) {
            return response()->json(['success' => false, 'message' => 'Invalid signature or failed'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Pembayaran dikonfirmasi']);
    }
}
