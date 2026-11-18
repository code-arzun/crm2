<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Customer;

class PlaceOrderAction
{
    /**
     * Eksekusi pembuatan pesanan baru dari keranjang (cart).
     *
     * @param Customer $customer
     * @param array $cart
     * @param array $data ['metode_pengiriman', 'catatan']
     * @return Order
     */
    public function execute(Customer $customer, array $cart, array $data): Order
    {
        // 1. Hitung Total
        $subtotal = collect($cart)->sum('subtotal');
        
        $ppnEnabled = Setting::get('ppn_enabled', false);
        $ppnPersen = Setting::get('ppn_persen', 11);
        $ppn = $ppnEnabled ? $subtotal * ($ppnPersen / 100) : 0;
        
        $grandTotal = $subtotal + $ppn; // Biaya pengiriman 0 di awal, Admin yang isi nanti

        // 2. Buat Header Order
        $order = Order::create([
            'customer_id' => $customer->id,
            'jalur' => 'self_order',
            'status' => 'menunggu_approval',
            'payment_type' => 'transfer',
            'payment_status' => 'unpaid',
            'subtotal' => $subtotal,
            'ppn' => $ppn,
            'metode_pengiriman' => $data['metode_pengiriman'],
            'biaya_pengiriman' => 0, 
            'total' => $grandTotal,
            'catatan' => $data['catatan'],
        ]);

        // 3. Buat Detail Order Items
        foreach ($cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'nama_produk' => $item['nama'],
                'qty' => $item['qty'],
                'harga' => $item['harga'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        return $order;
    }
}