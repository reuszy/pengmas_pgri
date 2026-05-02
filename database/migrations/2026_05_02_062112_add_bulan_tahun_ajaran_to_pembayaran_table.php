<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->tinyInteger('bulan')->nullable()->after('jenis_pembayaran');       // 1-12
            $table->string('tahun_ajaran', 20)->nullable()->after('bulan');            // "2025/2026"
        });

        // Update enum status agar include 'gagal'
        DB::statement("ALTER TABLE pembayaran MODIFY COLUMN status ENUM('lunas','belum','pending','gagal') DEFAULT 'belum'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['bulan', 'tahun_ajaran']);
        });

        DB::statement("ALTER TABLE pembayaran MODIFY COLUMN status ENUM('lunas','belum','pending') DEFAULT 'belum'");
    }
};
