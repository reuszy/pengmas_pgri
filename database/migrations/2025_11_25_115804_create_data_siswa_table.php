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
        Schema::create('data_siswa', function (Blueprint $table) {
            $table->id('id_data');

            // Data siswa
            $table->string('nis', 20);
            $table->string('nama', 50);
            $table->date('tanggal_lahir');  
            $table->string('nomor_telepon', 20);
            $table->string('email', 60);

            // Tambahan: id_kelas
            $table->unsignedBigInteger('id_kelas')->nullable();  // ✔ kelas disimpan di sini

            // Foreign Key ke tabel siswa
            $table->foreign('nis')
                ->references('nis')
                ->on('siswa')
                ->onDelete('cascade');

            // Foreign Key ke tabel kelas
            $table->foreign('id_kelas')
                ->references('id')
                ->on('kelas')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_siswa', function (Blueprint $table) {
            $table->dropForeign(['nis']);
            $table->dropForeign(['id_kelas']);
        });

        Schema::dropIfExists('data_siswa');
    }
};
