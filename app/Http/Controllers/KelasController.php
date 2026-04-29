<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function showDaftarForm()
    {
        $kelas = Kelas::all(); 
        return view('siswa.daftar', compact('kelas'));
    }

    public function index(){
        $kelas = Kelas::orderBy('id', 'DESC')->get();
        return view('admin.data-kelas', compact('kelas'));
    }

    public function store(Request $request){
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah terdaftar.'
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
        ]);

        return response()->json($kelas);
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        return response()->json($kelas);
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas,' . $id,
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah terdaftar.'
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
        ]);

        return response()->json($kelas);
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}