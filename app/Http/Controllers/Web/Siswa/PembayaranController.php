<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Services\PembayaranService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function __construct(private PembayaranService $pembayaranService) {}


    public function getSnapToken(Request $request)
    {
        try {
            $nis          = session('nis');
            $bulan        = (int) $request->input('bulan', now()->month);
            $tahunAjaran  = $request->input('tahun_ajaran', $this->getTahunAjaran());

            $result = $this->pembayaranService->buatSnapToken($nis, $bulan, $tahunAjaran);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


     // Helper: tentukan tahun ajaran berdasarkan bulan sekarang
     // Juli-Desember = tahun/tahun+1, Januari-Juni = tahun-1/tahun
    private function getTahunAjaran(): string
    {
        $bulan = now()->month;
        $tahun = now()->year;

        if ($bulan >= 7) {
            return $tahun . '/' . ($tahun + 1);
        }
        return ($tahun - 1) . '/' . $tahun;
    }


    public function streamBuktiPdf(string $nis)
    {
        $pembayaran = $this->pembayaranService->getBuktiPembayaran($nis);

        if (!$pembayaran) {
            abort(404, 'Bukti pembayaran tidak ditemukan');
        }

        $pdf = Pdf::loadView('pembayaran.bukti-pdf', [
            'pembayaran'    => $pembayaran,
            'tanggal_cetak' => date('d-m-Y H:i:s'),
        ]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('bukti_pembayaran_' . $nis . '.pdf');
    }


    public function notification(Request $request)
    {
        $berhasil = $this->pembayaranService->konfirmasiPembayaran($request->all());

        if (!$berhasil) {
            return response()->json(['status' => 'invalid signature or failed'], 400);
        }

        return response()->json(['status' => 'ok']);
    }


    public function success()
    {
        $nis        = session('nis');
        $pembayaran = $this->pembayaranService->getBuktiPembayaran($nis);
        return view('siswa.pembayaran_qris_success', compact('pembayaran'));
    }


    public function bukti(int $id)
    {
        $pembayaran = Pembayaran::with(['siswa', 'kelas'])->findOrFail($id);
        return view('siswa.bukti_pembayaran', compact('pembayaran'));
    }
}
