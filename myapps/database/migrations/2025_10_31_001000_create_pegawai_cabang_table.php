<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_cabang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pegawai_id');
            $table->unsignedBigInteger('id_cabang');
            $table->timestamps();

            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
            $table->foreign('id_cabang')->references('id_cabang')->on('tabel_cabang')->onDelete('cascade');
            $table->unique(['pegawai_id', 'id_cabang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_cabang');
    }
};

