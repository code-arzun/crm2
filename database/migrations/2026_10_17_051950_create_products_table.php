<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->enum('tipe', ['barang', 'jasa'])->default('barang');
            $table->decimal('harga_default', 15, 2);
            $table->integer('stok')->default(0);
            $table->string('satuan')->default('pcs');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

    }
    public function down(): void {
        Schema::dropIfExists('products');
    }
};