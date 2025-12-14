<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->dropUnique(['email']); // Hapus unique constraint
        });
        
        // Tambahkan unique constraint baru yang mengizinkan null
        Schema::table('pegawai', function (Blueprint $table) {
            // Tidak bisa menggunakan unique() dengan nullable di beberapa DB
            // Kita akan handle uniqueness di application level
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->unique('email');
        });
    }
};

