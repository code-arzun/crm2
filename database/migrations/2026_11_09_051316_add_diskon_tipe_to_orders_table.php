<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom untuk menyimpan tipe diskon (persen/nominal)
            $table->enum('diskon_tipe', ['nominal', 'persen'])->default('nominal')->after('diskon');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('diskon_tipe');
        });
    }
};