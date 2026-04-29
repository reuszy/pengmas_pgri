<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nama', 50)->after('nis')->nullable();
        });

        DB::statement('
            UPDATE siswa s
            JOIN data_siswa ds ON s.nis = ds.nis
            SET s.nama = ds.nama
        ');

        Schema::dropIfExists('data_siswa');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->id('id_data');
            $table->string('nis', 20);
            $table->string('nama', 50);
            $table->date('tanggal_lahir');
            $table->string('nomor_telepon', 20);
            $table->string('email', 60);
            $table->foreignId('id_kelas')->nullable()->constrained('kelas');
        });

        DB::statement('
            INSERT INTO data_siswa (nis, nama, tanggal_lahir, nomor_telepon, email, id_kelas)
            SELECT nis, nama, tanggal_lahir, nomor_telepon, email, id_kelas FROM siswa
        ');

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('nama');
        });
    }
};
