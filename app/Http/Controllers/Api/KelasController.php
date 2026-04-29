<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KelasResource;
use App\Services\KelasService;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function __construct(private KelasService $kelasService) {}

    /**
     * Daftar semua kelas
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        $kelas = $this->kelasService->getAll();

        return KelasResource::collection($kelas)
            ->additional([
                'success' => true,
                'message' => 'Daftar data kelas'
            ]);
    }

    /**
     * Detail data kelas.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(int $id)
    {
        $kelas = $this->kelasService->findOrFail($id);

        return (new KelasResource($kelas))
            ->additional([
                'success' => true,
                'message' => 'Detail data kelas'
            ]);
    }

    /**
     * Tambah Data Kelas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique'   => 'Nama kelas sudah terdaftar.',
        ]);

        $kelas = $this->kelasService->create($request->all());

        return (new KelasResource($kelas))
            ->additional([
                'success' => true,
                'message' => 'Data kelas berhasil ditambahkan'
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update Data Kelas.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas,' . $id,
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique'   => 'Nama kelas sudah terdaftar.',
        ]);

        $kelas = $this->kelasService->update($id, $request->all());

        return (new KelasResource($kelas))
            ->additional([
                'success' => true,
                'message' => 'Data kelas berhasil diupdate'
            ]);
    }

    /**
     * Hapus Data Kelas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $this->kelasService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil dihapus'
        ]);
    }
}