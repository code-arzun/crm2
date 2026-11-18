<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ShipmentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_gudang_bisa_mengirim_barang_dan_stok_berkurang()
    {
        // 1. Setup Role & User Gudang
        Role::create(['name' => 'warehouse']);
        $warehouseUser = User::factory()->create();
        $warehouseUser->assignRole('warehouse');

        // 2. Setup Produk, Customer, dan Order
        $product = Product::create([
            'kode' => 'PRD-001',
            'nama' => 'Laptop Test',
            'tipe' => 'barang',
            'harga_default' => 10000000,
            'stok' => 10, // Stok awal 10
            'satuan' => 'unit',
        ]);

        $customer = Customer::create([
            'nama_lengkap' => 'Budi',
            'telepon' => '08123',
            'alamat' => 'Jl. Test',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'kode_order' => 'ORD-SHIP-01',
            'status' => 'dikonfirmasi',
            'jalur' => 'self_order',
            'payment_type' => 'transfer',
            'payment_status' => 'paid',
            'subtotal' => 10000000,
            'total' => 10000000,
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'nama_produk' => $product->nama,
            'qty' => 3, // Pesan 3
            'harga' => 10000000,
            'subtotal' => 30000000,
        ]);

        // 3. Aksi: Gudang memproses pengiriman via Livewire
        \Livewire\Livewire::actingAs($warehouseUser)
            ->test(\App\Livewire\Warehouse\Shipments::class)
            ->call('openShipModal', $order->id)
            ->set('ekspedisi', 'JNE')
            ->set('no_resi', 'RESI123')
            ->set('qty_kirim', [$orderItem->id => 3]) // Kirim semua
            ->call('processShipment');

        // 4. Assert (Pastikan data berubah)
        // Cek stok berkurang dari 10 menjadi 7
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stok' => 7, 
        ]);

        // Cek status order berubah menjadi dikirim
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'dikirim',
        ]);

        // Cek riwayat mutasi stok tercatat
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'qty' => 3,
            'tipe' => 'out',
        ]);
    }
}