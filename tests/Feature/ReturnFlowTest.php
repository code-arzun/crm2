<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnOrder;
use App\Models\ReturnItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ReturnFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_setujui_retur_dan_gudang_terima_barang()
    {
        // 1. Setup Role
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'warehouse']);
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $warehouse = User::factory()->create();
        $warehouse->assignRole('warehouse');

        // 2. Setup Data
        $product = Product::create([
            'kode' => 'PRD-001',
            'nama' => 'Kertas Test',
            'tipe' => 'barang',
            'harga_default' => 50000,
            'stok' => 5, // Stok sisa 5
            'satuan' => 'rim',
        ]);

        $customer = Customer::create([
            'nama_lengkap' => 'Budi',
            'telepon' => '08123',
            'credit_balance' => 0,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'kode_order' => 'ORD-RET-01',
            'status' => 'selesai',
            'jalur' => 'self_order',
            'payment_type' => 'transfer',
            'payment_status' => 'paid',
            'subtotal' => 500000,
            'total' => 500000,
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'nama_produk' => $product->nama,
            'qty' => 10,
            'harga' => 50000,
            'subtotal' => 500000,
        ]);

        // Buat data retur (Status diajukan)
        $return = ReturnOrder::create([
            'order_id' => $order->id,
            'status' => 'diajukan',
            'alasan' => 'Rusak',
        ]);

        $returnItem = ReturnItem::create([
            'return_id' => $return->id,
            'order_item_id' => $orderItem->id,
            'qty' => 2, // Retur 2 rim
            'alasan_retur' => 'rusak',
        ]);

        // 3. Aksi 1: Admin menyetujui retur (Uang masuk ke saldo)
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Returns\Index::class)
            ->call('openModal', $return->id)
            ->set('refund_method', 'saldo')
            ->set('biaya_retur', 0) // Tanpa potongan
            ->call('setujuiRetur');

        // Cek status retur jadi 'disetujui'
        $this->assertDatabaseHas('returns', [
            'id' => $return->id,
            'status' => 'disetujui',
            'refund_amount' => 100000, // 50.000 x 2
        ]);

        // Cek saldo customer bertambah 100.000
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'credit_balance' => 100000,
        ]);

        // 4. Aksi 2: Gudang menerima barang retur (Stok bertambah)
        \Livewire\Livewire::actingAs($warehouse)
            ->test(\App\Livewire\Returns\ReceiveIndex::class)
            ->call('terimaBarang', $return->id);

        // Cek status retur jadi 'selesai'
        $this->assertDatabaseHas('returns', [
            'id' => $return->id,
            'status' => 'selesai',
        ]);

        // Cek stok kembali dari 5 menjadi 7 (dikembalikan 2)
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stok' => 7, 
        ]);
    }
}