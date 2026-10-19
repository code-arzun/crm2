<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Harga khusus per customer atau per tier
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('tier_id')->nullable()->constrained('customer_tiers')->cascadeOnDelete();
            $table->decimal('harga', 15, 2);
            $table->date('berlaku_dari')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'customer_id']);
            $table->index(['product_id', 'tier_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_prices');
    }
};