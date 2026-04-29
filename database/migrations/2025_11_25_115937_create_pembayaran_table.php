<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->string('nis', 20);
            $table->unsignedBigInteger('id_kelas');
            $table->string('jenis_pembayaran', 50);
            $table->string('order_id', 100)->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->integer('jumlah');
            $table->enum('status', ['lunas', 'belum', 'pending']);
            $table->timestamps();

            $table->foreign('nis')
                ->references('nis')
                ->on('siswa')
                ->onDelete('cascade');

            $table->foreign('id_kelas')
                ->references('id')
                ->on('kelas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
