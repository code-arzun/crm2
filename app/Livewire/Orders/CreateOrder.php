<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Livewire\Component;

class CreateOrder extends Component
{
    public $products;
    public $cart = []; // Array untuk menyimpan item pesanan
    
    public $selectedProductId = '';
    public $qty = 1;
    public $catatan = '';

    public function mount()
    {
        // Ambil produk yang aktif dan stoknya ada
        $this->products = Product::where('is_active', true)->get();
    }

    public function addToCart()
    {
        $this->validate([
            'selectedProductId' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1'
        ]);

        $product = Product::find($this->selectedProductId);

        // Cek apakah produk sudah ada di keranjang
        if (isset($this->cart[$product->id])) {
            $this->cart[$product->id]['qty'] += $this->qty;
            $this->cart[$product->id]['subtotal'] = $this->cart[$product->id]['qty'] * $product->harga_default;
        } else {
            // Jika belum, tambahkan baru
            $this->cart[$product->id] = [
                'id' => $product->id,
                'nama' => $product->nama,
                'harga' => $product->harga_default,
                'qty' => $this->qty,
                'subtotal' => $product->harga_default * $this->qty
            ];
        }

        // Reset form input
        $this->reset(['selectedProductId', 'qty']);
        $this->dispatch('notify', message: 'Produk ditambahkan ke keranjang.');
    }

    public function removeFromCart($id)
    {
        unset($this->cart[$id]);
    }

    public function getTotalProperty()
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function submitOrder()
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Keranjang belanja masih kosong.');
            return;
        }

        $customer = auth()->user()->customer;
        $total = $this->getTotalProperty();
        
        // Cek PPN dari Setting
        $ppnEnabled = Setting::get('ppn_enabled', false);
        $ppnPersen = Setting::get('ppn_persen', 11);
        $ppn = $ppnEnabled ? $total * ($ppnPersen / 100) : 0;
        $grandTotal = $total + $ppn;

        // Buat Order
        $order = Order::create([
            'customer_id' => $customer->id,
            'jalur' => 'self_order', // Customer pesan sendiri
            'status' => 'menunggu_approval', // Sesuai alur, admin/sales harus konfirmasi
            'payment_type' => 'transfer',
            'payment_status' => 'unpaid',
            'subtotal' => $total,
            'ppn' => $ppn,
            'total' => $grandTotal,
            'catatan' => $this->catatan,
        ]);

        // Simpan detail item ke Order Items
        foreach ($this->cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'nama_produk' => $item['nama'],
                'qty' => $item['qty'],
                'harga' => $item['harga'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        // Arahkan ke halaman Pesanan Saya
        return redirect()->route('orders.my')->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi admin.');
    }

    public function render()
    {
        return view('livewire.orders.create-order')->layout('layouts.app');
    }
}