<?php

namespace App\Http\Controllers;
use App\Models\TarifPembayaran;
use Illuminate\Http\Request;

class TarifPembayaranController extends Controller
{
    public function index()
    {
        return TarifPembayaran::all();
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pembayaran' => 'required|string|max:100',
            'nominal' => 'required|integer'
        ]);

        return TarifPembayaran::create($validated);
    }
    public function show($id)
    {
        return TarifPembayaran::findOrFail($id);
    }
    public function update(Request $request, $id)
    {
        $tarif = TarifPembayaran::findOrFail($id);

        $validated = $request->validate([
            'jenis_pembayaran' => 'sometimes|string|max:100',
            'nominal' => 'sometimes|integer'
        ]);

        $tarif->update($validated);

        return $tarif;
    }
    public function destroy($id)
    {
        return TarifPembayaran::destroy($id);
    }
}