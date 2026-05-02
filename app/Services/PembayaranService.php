<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TarifPembayaran;
use Illuminate\Support\Facades\Log;

class PembayaranService
{
    public function __construct(private MidtransService $midtransService) {}


    public function buatSnapToken(string $nis, int $bulan, string $tahunAjaran, string $jenisPembayaran = 'SPP Bulanan'): array
    {
        $siswa = Siswa::where('nis', $nis)->firstOrFail();
        $tarif = TarifPembayaran::where('jenis_pembayaran', $jenisPembayaran)->firstOrFail();

        // Cek apakah bulan ini sudah lunas atau pending
        $existing = Pembayaran::where('nis', $nis)
            ->where('bulan', $bulan)
            ->where('tahun_ajaran', $tahunAjaran)
            ->whereIn('status', ['lunas', 'pending'])
            ->first();

        if ($existing && $existing->status === 'lunas') {
            throw new \Exception('SPP bulan ini sudah lunas.');
        }

        // Jika ada pending sebelumnya, hapus dulu (user retry)
        if ($existing && $existing->status === 'pending') {
            $existing->delete();
        }

        $namaBulan = $this->namaBulan($bulan);
        $orderId   = $this->midtransService->generateOrderId($nis);

        Pembayaran::create([
            'nis'              => $siswa->nis,
            'id_kelas'         => $siswa->id_kelas,
            'jenis_pembayaran' => $jenisPembayaran,
            'bulan'            => $bulan,
            'tahun_ajaran'     => $tahunAjaran,
            'jumlah'           => $tarif->nominal,
            'status'           => 'pending',
            'order_id'         => $orderId,
        ]);

        $params = $this->midtransService->buildParams(
            $orderId,
            $tarif->nominal,
            $siswa->toArray(),
            $jenisPembayaran . ' - ' . $namaBulan
        );

        $snapToken = $this->midtransService->getSnapToken($params);

        return [
            'token'     => $snapToken,
            'order_id'  => $orderId,
        ];
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


    // Filter untuk halaman Admin
    public function filter(?string $status)
    {
        $query = Siswa::leftJoin('pembayaran', 'pembayaran.nis', '=', 'siswa.nis')
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