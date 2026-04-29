<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->unsignedBigInteger('id_admin')->primary();

            $table->foreign('id_admin')
                ->references('id_pengguna')
                ->on('pengguna')
                ->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
