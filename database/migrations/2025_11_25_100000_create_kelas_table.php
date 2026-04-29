<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->timestamps();
        });

        DB::table('kelas')->insert([
            ['nama_kelas' => 'Teknik Sepeda Motor X A'],
            ['nama_kelas' => 'Teknik Sepeda Motor X B'],
            ['nama_kelas' => 'Teknik Sepeda Motor XI A'],
            ['nama_kelas' => 'Teknik Sepeda Motor XI B'],
            ['nama_kelas' => 'Teknik Sepeda Motor XII A'],
            ['nama_kelas' => 'Teknik Sepeda Motor XII B'],
            ['nama_kelas' => 'Akutansi X A'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
