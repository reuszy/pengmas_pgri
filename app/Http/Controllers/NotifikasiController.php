<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        return Notifikasi::with('pembayaran')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pembayaran' => 'required|exists:pembayaran,id_pembayaran',
            'pesan' => 'required|string'
        ]);

        return Notifikasi::create($validated);
    }

    public function show($id)
    {
        return Notifikasi::with('pembayaran')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $notifikasi = Notifikasi::findOrFail($id);

        $validated = $request->validate([
            'id_pembayaran' => 'sometimes|exists:pembayaran,id_pembayaran',
            'pesan' => 'sometimes|string'
        ]);

        $notifikasi->update($validated);

        return $notifikasi;
    }

    public function destroy($id)
    {
        return Notifikasi::destroy($id);
    }
}
