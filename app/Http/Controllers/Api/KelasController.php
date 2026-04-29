<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{

    /**
     * Daftar semua kelas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $kelas = Kelas::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar data kelas',
            'data'    => $kelas
        ], 200);
    }


    /**
     * Detail data kelas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Data kelas tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data kelas',
            'data'    => $kelas
        ], 200);
    }


    /**
     * Tambah Data Kelas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil ditambahkan',
            'data'    => $kelas
        ], 201);
    }


    /**
     * Update Data Kelas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Data kelas tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $kelas->update([
            'nama_kelas' => $request->nama_kelas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil diupdate',
            'data'    => $kelas
        ], 200);
    }


    /**
     * Hapus Data Kelas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Data kelas tidak ditemukan',
            ], 404);
        }

        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil dihapus',
        ], 200);
    }
}
