<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TarifPembayaran;

class TarifPembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TarifPembayaran::create([
            'jenis_pembayaran' => 'SPP Bulanan',
            'nominal' => 180000,
        ]);

        // Tambahkan tarif lain jika diperlukan
        // TarifPembayaran::create([
        //     'jenis_pembayaran' => 'SPP Semester',
        //     'nominal' => 500000,
        // ]);
    }
}