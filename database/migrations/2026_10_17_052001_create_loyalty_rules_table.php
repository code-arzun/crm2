<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loyalty_rules', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['voucher', 'poin', 'tier', 'freeship']);
            $table->json('kondisi'); // {min_transaksi, tier_id, first_order, member_sejak}
            $table->json('reward');  // {tipe, nilai, poin}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('loyalty_rules');
    }
};