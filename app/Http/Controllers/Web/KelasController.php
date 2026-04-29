<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\KelasService;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function __construct(private KelasService $kelasService) {}

    public function showDaftarForm()
    {
        $kelas = $this->kelasService->getAll();
        return view('siswa.daftar', compact('kelas'));
    }

    public function index()
    {
        $kelas = $this->kelasService->getAll();
        return view('admin.data-kelas', compact('kelas'));
    }

    
    // store, update, destroy tetap return JSON karena dipanggil via AJAX
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique'   => 'Nama kelas sudah terdaftar.',
        ]);

        $kelas = $this->kelasService->create($request->all());
        return response()->json($kelas);
    }

    public function edit(int $id)
    {
        $kelas = $this->kelasService->findOrFail($id);
        return response()->json($kelas);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas,' . $id,
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique'   => 'Nama kelas sudah terdaftar.',
        ]);

        $kelas = $this->kelasService->update($id, $request->all());
        return response()->json($kelas);
    }

    public function destroy(int $id)
    {
        $this->kelasService->delete($id);
        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}