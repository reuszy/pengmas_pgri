<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Core;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\TarifPembayaran;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        // Config::$clientKey = config('services.midtrans.clientKey'); // Tidak diperlukan untuk backend Snap
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');
    }

    public function createQrisTransaction(Request $request)
    {
        try {
            $nis = session('nis');
            \Log::info('Session nis:', ['nis' => $nis]);
            if (!$nis) {
                return response()->json(['error' => 'Session siswa tidak ditemukan'], 401);
            }

            $siswa = Siswa::where('nis', $nis)->first();
            if (!$siswa) {
                \Log::error('Siswa not found for nis: ' . $nis);
                return response()->json(['error' => 'Siswa tidak ditemukan'], 404);
            }
            \Log::info('Siswa found:', ['nama' => $siswa->nama]);

            $tarif = TarifPembayaran::where('jenis_pembayaran', 'SPP Bulanan')->first();
            if (!$tarif) {
                \Log::error('Tarif SPP Bulanan not found');
                return response()->json(['error' => 'Tarif pembayaran tidak ditemukan'], 404);
            }
            \Log::info('Tarif found:', ['nominal' => $tarif->nominal]);

            $orderId = 'SPP-' . $siswa->nis . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $tarif->nominal,
                ],
                'customer_details' => [
                    'first_name' => $siswa->nama,
                    'email' => $siswa->email ?? 'siswa@example.com',
                    'phone' => $siswa->no_hp ?? '',
                ],
                'item_details' => [[
                    'id' => 'SPP-' . date('Ym'),
                    'price' => $tarif->nominal,
                    'quantity' => 1,
                    'name' => 'SPP Bulanan ' . date('F Y'),
                ]],
            ];

            \Log::info('Params for Snap:', $params);

            // SIMPAN STATUS PENDING DULU
            Pembayaran::create([
                'nis' => $siswa->nis,
                'id_kelas' => $siswa->id_kelas,
                'jenis_pembayaran' => 'SPP Bulanan',
                'jumlah' => $tarif->nominal,
                'status' => 'pending',
                'order_id' => $orderId,
            ]);

            $snapToken = Snap::getSnapToken($params);
            \Log::info('Snap token generated:', ['token' => $snapToken]);

            return response()->json([
                'snap_token' => $snapToken
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in createQrisTransaction:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Gagal membuat snap token: ' . $e->getMessage()], 500);
        }
    }


    public function getSnapToken(Request $request)
    {
        $nis = session('nis');
        if (!$nis) {
            return response()->json(['error' => 'Session siswa tidak ditemukan'], 401);
        }

        $siswa = Siswa::where('nis', $nis)->firstOrFail();

        $tarif = TarifPembayaran::where('jenis_pembayaran', 'SPP Bulanan')->firstOrFail();

        $orderId = 'SPP-' . $siswa->nis . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $tarif->nominal,
            ],
            'customer_details' => [
                'first_name' => $siswa->nama,
                'email' => $siswa->email ?? 'siswa@example.com',
                'phone' => $siswa->no_hp ?? '',
            ],
            'item_details' => [[
                'id' => 'SPP-' . date('Ym'),
                'price' => $tarif->nominal,
                'quantity' => 1,
                'name' => 'SPP Bulanan ' . date('F Y'),
            ]],
            'enabled_payments' => ['credit_card', 'bank_transfer', 'echannel', 'bca_klikpay', 'bca_klikbca', 'bri_epay', 'cimb_clicks', 'danamon_online', 'mandiri_clickpay', 'indomaret', 'alfamart', 'akulaku']
        ];

        // SIMPAN STATUS PENDING DULU
        Pembayaran::create([
            'nis' => $siswa->nis,
            'id_kelas' => $siswa->id_kelas,
            'jenis_pembayaran' => 'SPP Bulanan',
            'jumlah' => $tarif->nominal,
            'status' => 'pending',
            'order_id' => $orderId,
        ]);

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken
        ]);
    }


    public function notification(Request $request)
    {
        $notification = $request->all();

        // Log notification untuk debug
        \Log::info('Midtrans Notification', $notification);

        // Verifikasi signature
        $order_id = $notification['order_id'];
        $status_code = $notification['status_code'];
        $gross_amount = $notification['gross_amount'];
        $server_key = config('services.midtrans.serverKey');
        $signature_key = hash('sha512', $order_id . $status_code . $gross_amount . $server_key);

        if ($signature_key !== $notification['signature_key']) {
            \Log::warning('Invalid Midtrans signature', ['expected' => $signature_key, 'received' => $notification['signature_key']]);
            return response()->json(['status' => 'invalid signature'], 400);
        }

        $transaction_status = $notification['transaction_status'];

        // Update status pembayaran berdasarkan order_id
        if (in_array($transaction_status, ['settlement', 'capture'])) {
            // Ekstrak NIS dari order_id
            $parts = explode('-', $order_id);
            if (count($parts) >= 2) {
                $nis = $parts[1];

                Pembayaran::where('nis', $nis)
                    ->where('status', 'pending')
                    ->delete();
                Pembayaran::create([
                    'nis' => $nis,
                    'id_kelas' => Siswa::where('nis', $nis)->first()->id_kelas,
                    'jenis_pembayaran' => 'SPP Bulanan',
                    'jumlah' => $gross_amount,
                    'tanggal_bayar' => now(),
                    'status' => 'lunas',
                    'order_id' => $order_id,
                ]);

                \Log::info('Pembayaran berhasil dibuat', ['nis' => $nis, 'order_id' => $order_id]);
            } else {
                \Log::warning('Order ID format tidak valid', ['order_id' => $order_id]);
            }
        } else {
            \Log::info('Transaction status not settlement/capture', ['status' => $transaction_status]);
        }

        return response()->json(['status' => 'ok']);
        // return to_route('siswa.pembayaran');
    }

    public function simulatePayment(Request $request)
    {
        // Ambil data siswa dari session
        $nis = session('nis');
        $siswa = Siswa::where('nis', $nis)->first();
        if (!$siswa) {
            return response()->json(['error' => 'Siswa tidak ditemukan'], 404);
        }

        // Ambil tarif SPP bulanan
        $tarif = TarifPembayaran::where('jenis_pembayaran', 'SPP Bulanan')->first();
        if (!$tarif) {
            return response()->json(['error' => 'Tarif tidak ditemukan'], 404);
        }

        // Buat order_id simulasi
        $order_id = 'SPP-' . $siswa->nis . '-' . time();

        // Simulasi notification data
        $notification = [
            'order_id' => $order_id,
            'status_code' => '200',
            'gross_amount' => (string) $tarif->nominal,
            'transaction_status' => 'settlement',
            'signature_key' => hash('sha512', $order_id . '200' . $tarif->nominal . config('services.midtrans.server_key')),
        ];

        // Panggil notification handler
        $this->processNotification($notification);

        return response()->json(['message' => 'Pembayaran berhasil disimulasikan', 'order_id' => $order_id]);
    }

    private function processNotification($notification)
    {
        // Log notification untuk debug
        \Log::info('Simulated Midtrans Notification', $notification);

        $transaction_status = $notification['transaction_status'];
        $order_id = $notification['order_id'];
        $gross_amount = $notification['gross_amount'];

        // Update status pembayaran berdasarkan order_id
        if (in_array($transaction_status, ['settlement', 'capture'])) {
            // Ekstrak NIS dari order_id
            $parts = explode('-', $order_id);
            if (count($parts) >= 2) {
                $nis = $parts[1];

                Pembayaran::create([
                    'nis' => $nis,
                    'id_kelas' => Siswa::where('nis', $nis)->first()->id_kelas,
                    'jenis_pembayaran' => 'SPP Bulanan',
                    'jumlah' => $gross_amount,
                    'tanggal_bayar' => now(),
                    'status' => 'lunas',
                    'order_id' => $order_id,
                ]);

                \Log::info('Pembayaran berhasil dibuat (simulasi)', ['nis' => $nis, 'order_id' => $order_id]);
            } else {
                \Log::warning('Order ID format tidak valid (simulasi)', ['order_id' => $order_id]);
            }
        } else {
            \Log::info('Transaction status not settlement/capture (simulasi)', ['status' => $transaction_status]);
        }
    }
}
