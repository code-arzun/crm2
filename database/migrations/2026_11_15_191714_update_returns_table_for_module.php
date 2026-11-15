<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            // Tambahan untuk detail retur
            $table->string('alasan_retur')->nullable()->after('alasan'); // Kategori alasan (Rusak, Salah Kirim)
            $table->string('metode_retur')->default('ekspedisi')->after('alasan_retur'); // ekspedisi, ambil_sendiri
            $table->decimal('biaya_retur', 15, 2)->default(0)->after('metode_retur'); // Biaya kirim balik
            $table->string('refund_method')->nullable()->after('refund_amount'); // transfer, saldo
            $table->string('refund_proof')->nullable()->after('refund_method'); // Bukti transfer refund
        });

        // Tambah kolom saldo kredit di tabel customers
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('credit_balance', 15, 2)->default(0)->after('total_transaksi');
        });

        // Tambah setting batas waktu retur
        \Illuminate\Support\Facades\DB::table('settings')->insert([
            ['key' => 'retur_max_days', 'value' => '7', 'tipe' => 'number', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'retur_enabled', 'value' => '1', 'tipe' => 'bool', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn(['alasan_retur', 'metode_retur', 'biaya_retur', 'refund_method', 'refund_proof']);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('credit_balance');
        });
        \Illuminate\Support\Facades\DB::table('settings')->whereIn('key', ['retur_max_days', 'retur_enabled'])->delete();
    }
};