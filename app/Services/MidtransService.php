<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct() 
    {
        Config::$serverKey      = config('services.midtrans.serverKey');
        Config::$isProduction   = config('services.midtrans.isProduction');
        Config::$isSanitized    = config('services.midtrans.isSanitized');
        Config::$is3ds          = config('services.midtrans.is3ds');
    }


    public function getSnapToken(array $params):string 
    {
        return Snap::getSnapToken($params);
    }


    public function buildParams(string $orderId, int $nominal, array $siswa, string $jenisPembayaran): array 
    {
        return [
            'transaction_details' => [
                'order_id'      => $orderId,
                'gross_amount'  => $nominal,
            ],
            
            'customer_details' => [
                'first_name'    => $siswa['nama'],
                'email'         => $siswa['email'] ?? 'siswa@gmail.com',
                'phone'         => $siswa['nomor_telepon'] ?? '',
            ],

            'item_details' => [[
                'id'        => 'SPP-' . date('Ym'),
                'price'     => $nominal,
                'quantity'  => 1,
                'name'      => $jenisPembayaran . ' ' . date('Ym')
            ]],
        ];
    }


    public function verifySignature(string $orderId, string $statusCode, string $grossAmount): string
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . config('services.midtrans.serverKey'));
    }


    public function generateOrderId(string $nis): string
    {
        return 'SPP-' . $nis . '-' . time();
    }
}