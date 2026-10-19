<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tier_id')->nullable()->constrained('customer_tiers')->nullOnDelete();
            $table->foreignId('sales_id')->nullable()->constrained('users')->nullOnDelete(); // auto-assign
            $table->string('nama_lengkap');
            $table->string('telepon')->index();
            $table->text('alamat')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->enum('segment', ['new', 'active', 'loyal', 'dormant'])->default('new');
            $table->decimal('total_transaksi', 15, 2)->default(0);
            $table->timestamp('last_order_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('customers'); }
};