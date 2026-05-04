<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiswaResource;
use App\Services\SiswaService;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function __construct(private SiswaService $siswaService) {}

    /**
     * Daftar semua siswa
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index()
    {
        return SiswaResource::collection($this->siswaService->getAll())
            ->additional(['success' => true, 'message' => 'Daftar data siswa']);
    }

    /**
     * Detail data siswa
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $nis)
    {
        $siswa = $this->siswaService->findOrFail($nis);
        
        return response()->json([
            'success' => true,
            'message' => 'Detail data siswa',
            'data'    => $siswa
        ]);
    }

    /**
     * Tambah data siswa
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nis'           => 'required|string|max:20|unique:siswa,nis',
            'nama'          => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'id_kelas'      => 'required|exists:kelas,id',
            'nomor_telepon' => 'required|string|max:20',
            'email'         => 'required|email|unique:siswa,email',
            'password'      => 'required|string|min:6',
        ]);

        $siswa = $this->siswaService->create($request->all());
        return (new SiswaResource($siswa))
            ->additional(['success' => true, 'message' => 'Data siswa berhasil ditambahkan'])
            ->response()->setStatusCode(201);
    }

    /**
     * Update data siswa
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(Request $request, string $nis)
    {
        $request->validate([
            'nama'          => 'sometimes|string|max:50',
            'tanggal_lahir' => 'sometimes|date',
            'id_kelas'      => 'sometimes|exists:kelas,id',
            'nomor_telepon' => 'sometimes|string|max:20',
            'email'         => 'sometimes|email|unique:siswa,email,' . $nis . ',nis',
            'password'      => 'sometimes|string|min:6',
        ]);

        $siswa = $this->siswaService->update($nis, $request->all());
        return (new SiswaResource($siswa))
            ->additional(['success' => true, 'message' => 'Data siswa berhasil diupdate']);
    }

    /**
     * Hapus data siswa
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $nis)
    {
        $this->siswaService->delete($nis);
        return response()->json(['success' => true, 'message' => 'Data siswa berhasil dihapus']);
    }


    // Pengaturan Akun Siswa

    /**
     * Lihat profil siswa yang sedang login
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function profil()
    {
        $nis   = auth()->user()->username;
        $siswa = $this->siswaService->getProfil($nis);

        return (new SiswaResource($siswa))
            ->additional(['success' => true, 'message' => 'Profil siswa']);
    }


    /**
     * Update profil siswa yang sedang login
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function updateProfil(Request $request)
    {
        $nis = auth()->user()->username;

        $request->validate([
            'email'         => 'sometimes|email|unique:siswa,email,' . $nis . ',nis',
            'nomor_telepon' => 'sometimes|string|max:20',
        ]);

        $siswa = $this->siswaService->updateProfil($nis, $request->all());

        return (new SiswaResource($siswa))
            ->additional(['success' => true, 'message' => 'Profil berhasil diperbarui']);
    }

    /**
     * Ganti password siswa yang sedang login
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
            $nis = auth()->user()->username;
            $this->siswaService->gantiPassword(
                $nis,
                $request->password_lama,
                $request->password_baru,
            );

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}