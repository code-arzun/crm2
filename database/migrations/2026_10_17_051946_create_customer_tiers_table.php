<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customer_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Bronze, Silver, Gold
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('min_transaksi', 15, 2)->default(0); // syarat naik tier
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('customer_tiers'); }
};