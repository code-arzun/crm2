<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase; // Reset database setiap kali test jalan

    public function test_customer_bisa_membuat_pesanan()
    {
        // 1. Setup Data (Buat Role, User, Customer, Produk)
        Role::create(['name' => 'customer']);
        
        $user = User::factory()->create();
        $user->assignRole('customer');
        
        $customer = Customer::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Budi Test',
            'telepon' => '08123456789',
            'alamat' => 'Jl. Test',
            'nama_perusahaan' => 'PT Test',
        ]);

        $product = Product::create([
            'kode' => 'PRD-TEST',
            'nama' => 'Produk Test',
            'tipe' => 'barang',
            'harga_default' => 100000,
            'stok' => 10,
            'satuan' => 'pcs',
        ]);

        // 2. Simulasi Login dan Isi Keranjang di Session
        $cart = [
            $product->id => [
                'id' => $product->id,
                'nama' => $product->nama,
                'harga' => 100000,
                'qty' => 2,
                'subtotal' => 200000,
            ]
        ];

        // 3. Aksi: Tembak endpoint / panggil Livewire (kita uji komponennya)
        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Orders\CreateOrder::class)
            ->set('cart', $cart)
            ->set('metode_pengiriman', 'ekspedisi')
            ->set('catatan', 'Test Note')
            ->call('prosesPesanan')
            ->assertRedirect(route('orders.my'));

        // 4. Assert (Pastikan data masuk ke database)
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'subtotal' => 200000,
            'status' => 'menunggu_approval',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'qty' => 2,
            'subtotal' => 200000,
        ]);
    }
}