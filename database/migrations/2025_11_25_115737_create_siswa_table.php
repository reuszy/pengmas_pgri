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
        Schema::create('siswa', function (Blueprint $table) {
            $table->string('nis', 20)->primary();
            $table->unsignedBigInteger('id_pengguna');
            $table->date('tanggal_lahir')->nullable();
        $table->unsignedBigInteger('id_kelas')->nullable();

            $table->string('nomor_telepon', 20)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('password', 255);

            // FK ke pengguna
            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->onDelete('cascade');

            // FK ke kelas
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
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
            $table->dropForeign(['id_kelas']);   // <--- drop FK id_kelas
        });

        Schema::dropIfExists('siswa');
    }
};
