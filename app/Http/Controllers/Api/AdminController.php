<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(private AdminService $adminService) {}

    /**
     * Daftar semua admin
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */

    public function index()
    {
        return AdminResource::collection($this->adminService->getAll())
            ->additional(['success' => true, 'message' => 'Daftar data admin']);
    }


    /**
     * Detail data admin
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $admin = $this->adminService->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail data admin',
            'data'    => $admin
        ]);
    }


    /**
     * Tambah data admin
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengguna' => 'required|string|max:50',
            'username'      => 'required|string|max:50|unique:pengguna,username',
            'password'      => 'required|string|min:6',
        ]);

        $admin = $this->adminService->create($request->all());
        return (new AdminResource($admin))
            ->additional(['success' => true, 'message' => 'Data admin berhasil ditambahkan'])
            ->response()->setStatusCode(201);
    }
    

    /**
     * Update data admin
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_pengguna' => 'sometimes|string|max:50',
            'username'      => 'sometimes|string|max:50|unique:pengguna,username,' . $id . ',id_pengguna',
            'password'      => 'sometimes|string|min:6',
        ]);

        $admin = $this->adminService->update($id, $request->all());
        return (new AdminResource($admin))
            ->additional(['success' => true, 'message' => 'Data admin berhasil diupdate']);
    }


    /**
     * Hapus data admin
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $this->adminService->delete($id);
        return response()->json(['success' => true, 'message' => 'Data admin berhasil dihapus']);
    }
}
