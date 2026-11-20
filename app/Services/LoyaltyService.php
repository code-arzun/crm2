<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\LoyaltyTransaction;
use App\Models\Setting;

class LoyaltyService
{
    /**
     * Memberikan poin ke customer setelah pesanan selesai.
     */
    public function awardPointsForOrder(Order $order)
    {
        // Cek apakah loyalty aktif
        if (!Setting::get('loyalty_enabled', false)) {
            return;
        }

        $customer = $order->customer;

        // Cek apakah order ini sudah pernah dikasih poin (mencegah duplikasi)
        $existingTransaction = LoyaltyTransaction::where('order_id', $order->id)
            ->where('tipe', 'earn')
            ->exists();

        if ($existingTransaction) {
            return;
        }

        // Hitung poin: Total Belanja / Nilai Tukar Poin (misal 10.000)
        $poinPerRupiah = Setting::get('loyalty_poin_per_rupiah', 10000);
        $poinDidapat = floor($order->total / $poinPerRupiah);

        if ($poinDidapat > 0) {
            // 1. Catat transaksi poin
            LoyaltyTransaction::create([
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'tipe' => 'earn',
                'poin' => $poinDidapat,
                'keterangan' => 'Poin dari pesanan ' . $order->kode_order,
            ]);

            // 2. Tambahkan ke total poin customer
            $customerPoint = $customer->points()->firstOrCreate(['customer_id' => $customer->id]);
            $customerPoint->increment('total_poin', $poinDidapat);
        }
    }

    /**
     * Memproses penukaran poin saat checkout.
     */
    public function redeemPoints(Customer $customer, int $poinYangDitukar, Order $order)
    {
        if ($poinYangDitukar <= 0) return 0;

        $customerPoint = $customer->points()->first();

        // Pastikan poin cukup
        if (!$customerPoint || $customerPoint->total_poin < $poinYangDitukar) {
            throw new \Exception('Poin tidak mencukupi.');
        }

        // Hitung nilai Rupiah dari poin (1 poin = Rp 1, atau sesuaikan)
        // Misal: 1 poin = Rp 100
        $nilaiTukar = 100; 
        $diskonRupiah = $poinYangDitukar * $nilaiTukar;

        // 1. Kurangi poin customer
        $customerPoint->decrement('total_poin', $poinYangDitukar);

        // 2. Catat transaksi redeem
        LoyaltyTransaction::create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'tipe' => 'redeem',
            'poin' => -$poinYangDitukar,
            'nominal' => $diskonRupiah,
            'keterangan' => 'Tukar poin untuk pesanan ' . $order->kode_order,
        ]);

        return $diskonRupiah;
    }
}