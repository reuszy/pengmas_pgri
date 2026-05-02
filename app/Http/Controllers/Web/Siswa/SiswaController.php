<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TarifPembayaran;
use App\Services\PembayaranService;
use App\Services\SiswaService;

class SiswaController extends Controller
{
    public function __construct(
        private SiswaService $siswaService,
        private PembayaranService $pembayaranService,
    ) {}

    public function beranda()
    {
        return view('siswa.beranda');
    }

    public function dashboard()
    {
        $siswa        = $this->siswaService->findOrFail(session('nis'));
        $tarif        = TarifPembayaran::where('jenis_pembayaran', 'SPP Bulanan')->first();
        $tahunAjaran  = $this->getTahunAjaran();

        // Generate tagihan 12 bulan (Juli-Juni)
        $tagihan = $this->generateTagihanBulanan($siswa->nis, $tarif, $tahunAjaran);

        return view('siswa.dashboard', compact('siswa', 'tagihan', 'tahunAjaran'));
    }

    public function pembayaran()
    {
        $siswa        = $this->siswaService->findOrFail(session('nis'));
        $tarif        = TarifPembayaran::where('jenis_pembayaran', 'SPP Bulanan')->first();
        $tahunAjaran  = $this->getTahunAjaran();
        $tagihan      = $this->generateTagihanBulanan($siswa->nis, $tarif, $tahunAjaran);

        return view('siswa.pembayaran', compact('siswa', 'tarif', 'tagihan', 'tahunAjaran'));
    }

    public function pembayaranQris()
    {
        $siswa        = $this->siswaService->findOrFail(session('nis'));
        $tarif        = TarifPembayaran::where('jenis_pembayaran', 'SPP Bulanan')->first();
        $tahunAjaran  = $this->getTahunAjaran();
        $tagihan      = $this->generateTagihanBulanan($siswa->nis, $tarif, $tahunAjaran);

        return view('siswa.pembayaran_qris', compact('siswa', 'tarif', 'tagihan', 'tahunAjaran'));
    }


    /**
     * Generate tagihan 12 bulan untuk tahun ajaran (Juli–Juni)
     */
    private function generateTagihanBulanan(string $nis, ?TarifPembayaran $tarif, string $tahunAjaran): array
    {
        if (!$tarif) {
            return [];
        }

        // Urutan bulan dalam tahun ajaran: Juli(7)–Desember(12), Januari(1)–Juni(6)
        $urutanBulan = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];

        // Ambil semua pembayaran siswa untuk tahun ajaran ini
        $pembayaran = Pembayaran::where('nis', $nis)
            ->where('tahun_ajaran', $tahunAjaran)
            ->whereIn('status', ['lunas', 'pending'])
            ->get()
            ->keyBy('bulan');

        $tagihan = [];
        foreach ($urutanBulan as $bulan) {
            $record = $pembayaran->get($bulan);
            $status = $record->status ?? 'belum';

            $tagihan[] = [
                'bulan'            => $bulan,
                'nama_bulan'       => $this->pembayaranService->namaBulan($bulan),
                'nominal'          => $tarif->nominal,
                'status'           => $status,
                'tahun_ajaran'     => $tahunAjaran,
            ];
        }

        return $tagihan;
    }


    /**
     * Tentukan tahun ajaran berdasarkan bulan sekarang
     * Juli-Desember = tahun/tahun+1, Januari-Juni = tahun-1/tahun
     */
    private function getTahunAjaran(): string
    {
        $bulan = now()->month;
        $tahun = now()->year;

        if ($bulan >= 7) {
            return $tahun . '/' . ($tahun + 1);
        }
        return ($tahun - 1) . '/' . $tahun;
    }
}