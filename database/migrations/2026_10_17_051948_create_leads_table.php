<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete(); // jika sudah convert
            $table->string('nama');
            $table->string('telepon');
            $table->string('email')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('sumber')->nullable(); // pameran, website, referral
            $table->enum('status', ['baru', 'dihubungi', 'negosiasi', 'won', 'lost'])->default('baru');
            $table->text('catatan')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('tipe', ['telepon', 'wa', 'meeting', 'email', 'catatan']);
            $table->text('keterangan');
            $table->timestamp('activity_at');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
    }
};