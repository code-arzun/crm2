<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Siap multi-warehouse (nanti tinggal tambah warehouse_id)
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('warehouse_id')->nullable(); // reserved untuk nanti
            $table->integer('qty')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'warehouse_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('stocks');
    }
};