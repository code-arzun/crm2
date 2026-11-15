<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel returns dijadikan "Header"
        Schema::table('returns', function (Blueprint $table) {
            $table->string('kode_retur')->unique()->after('id');
            $table->integer('total_qty_diretur')->default(0)->after('kode_retur');
            // Drop kolom yang akan dipindah ke return_items
            $table->dropForeign(['order_item_id']);
            $table->dropColumn(['order_item_id', 'qty', 'alasan_retur']);
        });

        // 2. Buat tabel return_items (Detail)
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->integer('qty');
            $table->string('alasan_retur')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_items');
        
        Schema::table('returns', function (Blueprint $table) {
            $table->foreignId('order_item_id')->nullable()->constrained('order_items');
            $table->integer('qty')->default(0);
            $table->string('alasan_retur')->nullable();
            $table->dropColumn(['kode_retur', 'total_qty_diretur']);
        });
    }
};