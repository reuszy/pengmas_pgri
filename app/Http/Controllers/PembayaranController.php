<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TarifPembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required',
            'id_tarif' => 'required',
        ]);

        $tarif = TarifPembayaran::findOrFail($request->id_tarif);
        $siswa = Siswa::where('id_kelas', $request->id_kelas)->get();

        $pembayaran = null;
        foreach ($siswa as $s) {
            $pembayaran = Pembayaran::create([
                'nis' => $s->nis,
                'id_kelas' => $request->id_kelas,
                'jenis_pembayaran' => $tarif->jenis_pembayaran,
                'jumlah' => $tarif->nominal,
                'tanggal_bayar' => now(),
                'status' => 'lunas',
            ]);
        }
        
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('services.midtrans.serverKey');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $params = array(
        'transaction_details' => array(
                'order_id' => rand(),
                'gross_amount' => 180000,
            )
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        if ($pembayaran) {
            $pembayaran->snap_token = $snapToken;
            $pembayaran->save();
        }

        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil');
    }

    public function filterPembayaran(Request $request)
    {
        $status = $request->status;
        $tarif = TarifPembayaran::value('nominal');
        $tarifNumber = (int) str_replace(['Rp', '.', ',', ' '], '', $tarif);

        $query = Siswa::leftJoin('pengguna', 'pengguna.id_pengguna', '=', 'siswa.id_pengguna')
            ->leftJoin('pembayaran', 'pembayaran.nis', '=', 'siswa.nis')
            ->select(
                'siswa.nis',
                'pengguna.nama_pengguna as nama',
                'pembayaran.tanggal_bayar',
                'pembayaran.jumlah',
                'pembayaran.status'
            );

        if ($status == 'Lunas') {
            $query->where('pembayaran.status', 'Lunas');
        } elseif ($status == 'Belum Lunas') {
                $query->where(function ($q) {
                    $q->whereNull('pembayaran.status')
                    ->orWhere('pembayaran.status', 'Belum Lunas');
                });
        }

        $results = $query->orderBy('nama')->get();

        $results = $results->map(function ($item) use ($tarif) {
            if (!$item->jumlah) {
                $item->jumlah = $tarif;
            }
            if (!$item->status) {
                $item->status = 'Belum Lunas';
            }
            return $item;
        });

        return response()->json($results);
    }
    
    public function streamBuktiPdf($id)
    {
        $pembayaran = Pembayaran::with(['siswa.pengguna', 'kelas'])->where("nis", $id)->first();

        $data = [
            'pembayaran' => $pembayaran,
            'tanggal_cetak' => date('d-m-Y H:i:s')
        ];

        $pdf = Pdf::loadView('pembayaran.bukti-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('bukti_pembayaran_' . $pembayaran->nis . '.pdf');
    }
}
