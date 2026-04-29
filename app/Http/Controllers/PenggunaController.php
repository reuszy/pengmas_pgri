<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        return Pengguna::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pengguna' => 'required|string|max:50',
            'username' => 'required|string|max:50',
            'password' => 'required|string',
            'role' => 'required|in:admin,siswa'
        ]);

        return Pengguna::create($validated);
    }

    public function show($id)
    {
        return Pengguna::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);
        $pengguna->update($request->all());
        return $pengguna;
    }
    public function destroy($id)
    {
        return Pengguna::destroy($id);
    }
}
