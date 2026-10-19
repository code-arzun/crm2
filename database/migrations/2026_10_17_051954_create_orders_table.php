<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_order')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('jalur', ['self_order', 'sales_order'])->default('self_order');
            $table->enum('status', [
                'draft', 'menunggu_approval', 'dikonfirmasi',
                'diproses', 'dikirim', 'selesai', 'batal'
            ])->default('menunggu_approval');
            $table->enum('payment_type', ['transfer', 'kredit', 'midtrans', 'xendit', 'stripe'])->default('transfer');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->date('due_date')->nullable(); // untuk kredit
            $table->text('catatan')->nullable();
            $table->text('alasan_batal')->nullable();
            $table->timestamps();
            $table->index(['status', 'payment_status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};