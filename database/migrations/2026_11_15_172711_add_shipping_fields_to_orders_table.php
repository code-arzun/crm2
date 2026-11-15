<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Metode: 'ambil_sendiri', 'ekspedisi'
            $table->string('metode_pengiriman')->default('ekspedisi')->after('total');
            // Biaya ongkir
            $table->decimal('biaya_pengiriman', 15, 2)->default(0)->after('metode_pengiriman');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['metode_pengiriman', 'biaya_pengiriman']);
        });
    }
};