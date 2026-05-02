<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TarifPembayaran;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PembayaranService
{
    public function __construct(private MidtransService $midtransService) {}


    public function buatSnapToken(string $nis, int $bulan, string $tahunAjaran, string $jenisPembayaran = 'SPP Bulanan'): array
    {
        $siswa = Siswa::where('nis', $nis)->firstOrFail();
        $tarif = TarifPembayaran::where('jenis_pembayaran', $jenisPembayaran)->firstOrFail();

        // Cek apakah bulan ini sudah lunas
        if ($this->isBulanLunas($nis, $bulan, $tahunAjaran, $jenisPembayaran)) {
            throw new Exception('SPP bulan ini sudah lunas.');
        }

        // Tentukan cicilan ke berapa yang akan dibayar
        $cicilanKe = $this->getCicilanBerikutnya($nis, $bulan, $tahunAjaran, $jenisPembayaran);

        if (!$cicilanKe) {
            throw new Exception('Semua cicilan bulan ini sudah lunas.');
        }

        $existing = Pembayaran::where('nis', $nis)
            ->where('bulan', $bulan)
            ->where('tahun_ajaran', $tahunAjaran)
            ->whereIn('status', ['lunas', 'pending'])
            ->first();

        if ($existing && $existing->status === 'lunas') {
            throw new Exception('SPP bulan ini sudah lunas.');
        }

        // Hapus pending lama dari skema APAPUN untuk bulan ini (agar tidak ada tagihan pending ganda)
         Pembayaran::where('nis', $nis)
            ->where('bulan', $bulan)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status', 'pending')
            ->delete();

        $namaBulan = $this->namaBulan($bulan);
        $orderId   = $this->midtransService->generateOrderId($nis);

        // Label item untuk Midtrans
        $itemLabel = $tarif->total_cicilan > 1
            ? "{$jenisPembayaran} {$namaBulan} (Cicilan {$cicilanKe}/{$tarif->total_cicilan})"
            : "{$jenisPembayaran} {$namaBulan}";
            
        Pembayaran::create([
            'nis'              => $siswa->nis,
            'id_kelas'         => $siswa->id_kelas,
            'jenis_pembayaran' => $jenisPembayaran,
            'bulan'            => $bulan,
            'tahun_ajaran'     => $tahunAjaran,
            'jumlah'           => $tarif->nominal,
            'cicilan_ke'       => $cicilanKe,
            'total_cicilan'    => $tarif->total_cicilan,
            'status'           => 'pending',
            'order_id'         => $orderId,
        ]);

        $params = $this->midtransService->buildParams(
            $orderId,
            $tarif->nominal,
            $siswa->toArray(),
            $itemLabel
        );
        $snapToken = $this->midtransService->getSnapToken($params);

        return [
            'token'         => $snapToken,
            'order_id'      => $orderId,
            'cicilan_ke'    => $cicilanKe,
            'total_cicilan' => $tarif->total_cicilan,
            'nominal'       => $tarif->nominal,
        ];
    }


    public function getTahunAjaran(?int $bulan = null, ?int $tahun = null): string
    {
        $bulan = $bulan ?? now()->month;
        $tahun = $tahun ?? now()->year;

        if ($bulan >= 7) {
            return $tahun . '/' . ($tahun + 1);
        }
        return ($tahun - 1) . '/' . $tahun;
    }


    public function getTarifAktif(string $jenisPembayaran = 'SPP Bulanan'): ?TarifPembayaran
    {
        return TarifPembayaran::where('jenis_pembayaran', $jenisPembayaran)->first();
    }

    
    // Helper nama bulan
    public function namaBulan(int $bulan): string
    {
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',      6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',  9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $nama[$bulan] ?? '';
    }


    public function sudahBerjalan(int $bulanTagihan, int $bulanSekarang): bool
    {
        $indexTagihan  = ($bulanTagihan >= 7) ? $bulanTagihan - 7 : $bulanTagihan + 5;
        $indexSekarang = ($bulanSekarang >= 7) ? $bulanSekarang -7 : $bulanSekarang + 5;

        return $indexTagihan <= $indexSekarang;
    }


    public function generateTagihanBulanan(string $nis, ?TarifPembayaran $tarif, string $tahunAjaran): array
    {
        if (!$tarif) return [];

        $urutanBulan   = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];
        $bulanSekarang = now()->month;
        $isCicilan     = $tarif->total_cicilan > 1;
        
        // Ambil semua pembayaran siswa untuk tahun ajaran sekarang
        $semuaPembayaran = Pembayaran::where('nis', $nis)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();
            
        $semuaTarif = TarifPembayaran::all()->keyBy('jenis_pembayaran');

        $tagihan = [];

        foreach ($urutanBulan as $bulan) {
            if (!$this->sudahBerjalan($bulan, $bulanSekarang)) {
                continue;
            }

            $pembayaranBulan = $semuaPembayaran->where('bulan', $bulan);
            
            // Cari apakah sudah ada pembayaran lunas
            $lunasPayments = $pembayaranBulan->where('status', 'lunas');
            
            if ($lunasPayments->count() > 0) {
                // Bulan ini sudah mulai dibayar
                $firstPayment = $lunasPayments->first();
                $activeJenis = $firstPayment->jenis_pembayaran;
                $activeTarif = $semuaTarif->get($activeJenis) ?? $tarif;
                
                $targetCicilan = $firstPayment->total_cicilan ?? ($activeTarif->total_cicilan ?? 1);
                $cicilanLunas = $lunasPayments->count();
                $sudahLunasPenuh = ($targetCicilan === 1) || ($cicilanLunas >= $targetCicilan);
                
                // Cek apakah ada pending
                $adaPending = $pembayaranBulan->where('jenis_pembayaran', $activeJenis)->where('status', 'pending')->count() > 0;
                
                if ($sudahLunasPenuh) {
                    $status = 'lunas';
                } elseif ($adaPending) {
                    $status = 'pending';
                } else {
                    $status = 'cicilan';
                }
                
                $tagihan[] = [
                    'bulan'          => $bulan,
                    'nama_bulan'     => $this->namaBulan($bulan),
                    'nominal'        => $activeTarif->nominal,
                    'total_nominal'  => $activeTarif->nominal * $activeTarif->total_cicilan,
                    'status'         => $status,
                    'tahun_ajaran'   => $tahunAjaran,
                    'is_cicilan'     => $targetCicilan > 1,
                    'total_cicilan'  => $targetCicilan,
                    'cicilan_lunas'  => $sudahLunasPenuh ? $targetCicilan : $cicilanLunas,
                    'sisa_cicilan'   => $sudahLunasPenuh ? 0 : ($targetCicilan - $cicilanLunas),
                    'jenis_pembayaran' => $activeJenis,
                ];
            } else {
                // Belum ada pembayaran lunas sama sekali.
                // Tampilkan sesuai tab/skema yang sedang dibuka ($tarif).
                $pembayaranSkemaIni = $pembayaranBulan->where('jenis_pembayaran', $tarif->jenis_pembayaran);
                $adaPending = $pembayaranSkemaIni->where('status', 'pending')->count() > 0;
                
                $tagihan[] = [
                    'bulan'          => $bulan,
                    'nama_bulan'     => $this->namaBulan($bulan),
                    'nominal'        => $tarif->nominal,
                    'total_nominal'  => $tarif->nominal * $tarif->total_cicilan,
                    'status'         => $adaPending ? 'pending' : 'belum',
                    'tahun_ajaran'   => $tahunAjaran,
                    'is_cicilan'     => $isCicilan,
                    'total_cicilan'  => $tarif->total_cicilan,
                    'cicilan_lunas'  => 0,
                    'sisa_cicilan'   => $tarif->total_cicilan,
                    'jenis_pembayaran' => $tarif->jenis_pembayaran,
                ];
            }
        }

        return $tagihan;
    }


    public function konfirmasiPembayaran(array $notification): bool
    {
        Log::info('Midtrans notification received', $notification);

        $orderId     = $notification['order_id'] ?? null;
        $statusCode  = $notification['status_code'] ?? null;
        $grossAmount = $notification['gross_amount'] ?? null;
        $signatureKey = $notification['signature_key'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            Log::warning('Midtrans notification: data tidak lengkap', $notification);
            return false;
        }

        $signature = $this->midtransService->verifySignature($orderId, $statusCode, $grossAmount);

        if ($signature !== $signatureKey) {
            Log::warning('Invalid Midtrans signature', [
                'expected' => $signature,
                'received' => $signatureKey,
            ]);
            return false;
        }

        $transactionStatus = $notification['transaction_status'];
        $pembayaran = Pembayaran::where('order_id', $orderId)->first();

        if (!$pembayaran) {
            Log::warning('Pembayaran tidak ditemukan untuk order_id', ['order_id' => $orderId]);
            return false;
        }

        // Pembayaran berhasil
        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            $pembayaran->update([
                'status'        => 'lunas',
                'jumlah'        => $grossAmount,
                'tanggal_bayar' => now(),
            ]);

            Log::info('Pembayaran berhasil dikonfirmasi', [
                'nis'      => $pembayaran->nis,
                'order_id' => $orderId,
            ]);
            return true;
        }

        // Pembayaran gagal / expire / dibatalkan
        if (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
            $pembayaran->update(['status' => 'gagal']);

            Log::info('Pembayaran gagal/expire/cancel', [
                'order_id' => $orderId,
                'status'   => $transactionStatus,
            ]);
            return true;
        }

        // Status lain (pending, dll) — tidak perlu update
        Log::info('Midtrans status tidak diproses', ['status' => $transactionStatus]);
        return true;
    }


    // Ambil semua record cicilan pada bulan tertentu
    public function getCicilanBulanan(string $nis, int $bulan, string $tahunAjaran): Collection
    {
        return Pembayaran::where('nis', $nis)
        ->where('bulan', $bulan)
        ->where('tahun_ajaran', $tahunAjaran)
        ->orderBy('cicilan_ke')
        ->get();
    }


    // Cek cicilan berikutnya yang harus dibayar
    public function getCicilanBerikutnya(string $nis, int $bulan, string $tahunAjaran, string $jenisPembayaran): ?int
    {
        $tarif = TarifPembayaran::where('jenis_pembayaran', $jenisPembayaran)->firstOrFail();

        $cicilanLunas = Pembayaran::where('nis', $nis)
            ->where('bulan', $bulan)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('jenis_pembayaran', $jenisPembayaran)
            ->where('status', 'lunas')
            ->count();

        if ($cicilanLunas >= $tarif->total_cicilan) {
            return null; // semua cicilan lunas
        }

        return $cicilanLunas + 1;
    }


    // Cek apakah bulan ini sudah lunas
    public function isBulanLunas(string $nis, int $bulan, string $tahunAjaran, string $jenisPembayaran): bool
    {
        $pembayaran = Pembayaran::where('nis', $nis)
            ->where('bulan', $bulan)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status', 'lunas')
            ->get();

        foreach ($pembayaran->groupBy('jenis_pembayaran') as $jenis => $payments) {
            $t = TarifPembayaran::where('jenis_pembayaran', $jenis)->first();
            $targetCicilan = $t ? $t->total_cicilan : 1;
            
            $first = $payments->first();
            if ($first->total_cicilan === null || $first->total_cicilan === 1 || $payments->count() >= $targetCicilan) {
                return true;
            }
        }

        return false;
    }


    // Filter untuk halaman Admin
    public function filter(?string $status, ?int $bulan = null, ?string $tahunAjaran = null)
    {
        $query = Siswa::leftJoin('pembayaran', function ($join) use ($bulan, $tahunAjaran) {
                $join->on('pembayaran.nis', '=', 'siswa.nis');
                if ($bulan) {
                    $join->where('pembayaran.bulan', '=', $bulan);
                }
                if ($tahunAjaran) {
                    $join->where('pembayaran.tahun_ajaran', '=', $tahunAjaran);
                }
            })
            ->leftJoin('kelas', 'kelas.id', '=', 'siswa.id_kelas')
            ->select(
                'siswa.nis',
                'siswa.nama',
                'kelas.nama_kelas',
                'pembayaran.bulan',
                'pembayaran.tahun_ajaran',
                'pembayaran.tanggal_bayar',
                'pembayaran.jumlah',
                'pembayaran.status',
                'pembayaran.order_id',
            );

        if ($status === 'lunas') {
            $query->where('pembayaran.status', 'lunas');
        } elseif ($status === 'belum') {
            $query->where(function ($q) {
                $q->whereNull('pembayaran.status')
                  ->orWhere('pembayaran.status', 'belum');
            });
        }

        return $query->orderBy('siswa.nama')->get()->map(function ($item) {
            $item->nama_bulan = $item->bulan ? $this->namaBulan($item->bulan) : '-';
            $item->tahun_ajaran = $item->tahun_ajaran ?? '-';
            $item->status = $item->status ?? 'belum';
            return $item;
        });
    }


    // Stream Bukti PDF: Untuk Siswa
    public function getBuktiPembayaran(string $nis): ?Pembayaran
    {
        return Pembayaran::with(['siswa', 'kelas'])
            ->where('nis', $nis)
            ->where('status', 'lunas')
            ->latest()
            ->first();
    }
}