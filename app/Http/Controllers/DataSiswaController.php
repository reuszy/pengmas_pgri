<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSiswa;

class DataSiswaController extends Controller
{
    public function index()
    {
        return DataSiswa::with('siswa')->get();
    }

    public function store(Request $request)
    {
        return DataSiswa::create($request->all());
    }

    public function show($id)
    {
        return DataSiswa::with('siswa')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = DataSiswa::findOrFail($id);
        $data->update($request->all());
        return $data;
    }

    public function destroy($id)
    {
        return DataSiswa::destroy($id);
    }
}
