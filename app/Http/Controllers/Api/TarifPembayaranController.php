<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TarifPembayaranResource;
use App\Services\TarifPembayaranService;
use Illuminate\Http\Request;

class TarifPembayaranController extends Controller
{
    public function __construct(private TarifPembayaranService $tarifService) {}


    /**
     * Daftar semua tarif pembayaran
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        return TarifPembayaranResource::collection($this->tarifService->getAll())
            ->additional(['success' => true, 'message' => 'Daftar tarif pembayaran']);
    }


    /**
     * Detail tarif pembayaran
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $tarif = $this->tarifService->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Detail data tarif pembayaran',
            'data'    => $tarif
        ]);
    }


    /**
     * Tambah tarif pembayaran
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pembayaran' => 'required|string|max:50|unique:tarif_pembayaran,jenis_pembayaran',
            'nominal'          => 'required|integer|min:0',
            'total_cicilan'    => 'required|integer|min:1|max:12',
        ], [
            'jenis_pembayaran.unique' => 'Jenis pembayaran sudah terdaftar.',
            'nominal.min'             => 'Nominal tidak boleh negatif.',
            'total_cicilan.min'       => 'Minimal 1x (lunas).',
        ]);

        $tarif = $this->tarifService->create($request->all());
        return (new TarifPembayaranResource($tarif))
            ->additional(['success' => true, 'message' => 'Tarif pembayaran berhasil ditambahkan'])
            ->response()->setStatusCode(201);
    }


    /**
     * Update tarif pembayaran
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'jenis_pembayaran' => 'sometimes|string|max:50|unique:tarif_pembayaran,jenis_pembayaran,' . $id . ',id_tarif',
            'nominal'          => 'sometimes|integer|min:0',
            'total_cicilan'    => 'sometimes|integer|min:1|max:12',
        ], [
            'jenis_pembayaran.unique' => 'Jenis pembayaran sudah terdaftar.',
            'nominal.min'             => 'Nominal tidak boleh negatif.',
            'total_cicilan.min'       => 'Minimal 1x (lunas).',
            'total_cicilan.max'       => 'Maksimal 12x cicilan.',
        ]);

        $tarif = $this->tarifService->update($id, $request->all());
        return (new TarifPembayaranResource($tarif))
            ->additional(['success' => true, 'message' => 'Tarif pembayaran berhasil diupdate']);
    }

    
    /**
     * Hapus tarif pembayaran
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $this->tarifService->delete($id);
        return response()->json(['success' => true, 'message' => 'Tarif pembayaran berhasil dihapus']);
    }
}
