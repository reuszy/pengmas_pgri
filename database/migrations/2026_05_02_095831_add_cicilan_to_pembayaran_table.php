<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->tinyInteger('cicilan_ke')->unsigned()->nullable()->after('jumlah');
            $table->tinyInteger('total_cicilan')->unsigned()->nullable()->after('cicilan_ke');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['cicilan_ke', 'total_cicilan']);
        });
    }
};
