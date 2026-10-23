<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Tambahkan kolom kategori_bisnis setelah nama_perusahaan
            $table->string('kategori_bisnis')->nullable()->after('nama_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('kategori_bisnis');
        });
    }
};