<?php

namespace App\Livewire\Catalog;

use App\Models\Product;
use Livewire\Component;

class Index extends Component
{
    public $search = '';
    public $cart = [];
    public $customer;

    public function mount()
    {
        $customer = auth()->user()->customer;
        
        // Jika customer dan profilnya belum lengkap, arahkan ke form lengkapi
        //if (auth()->user()->hasRole('customer') && (!$customer->alamat || !$customer->nama_perusahaan)) {
        //    redirect()->route('customer.lengkapi')->send();
        //}
    
        $this->customer = auth()->user()->customer;
        $this->cart = session()->get('cart', []);
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Ambil keranjang dari session
        // $cart = session()->get('cart', []);

        // Cek jika stok habis
        if ($product->tipe->value === 'barang' && $product->stok < 1) {
            $this->dispatch('notify', message: 'Stok produk habis.');
            return;
        }

        $hargaDinamis = $product->hargaUntukCustomer($this->customer);

        $cart = session()->get('cart', []);

        // Jika produk sudah ada di keranjang, tambah qty-nya
        if (isset($cart[$productId])) {
            $cart[$productId]['qty']++;
            $cart[$productId]['subtotal'] = $cart[$productId]['qty'] * $hargaDinamis;
        } else {
            // Jika belum ada, buat entri baru
            $cart[$productId] = [
                'id' => $product->id,
                'nama' => $product->nama,
                'harga' => $hargaDinamis,
                'qty' => 1,
                'subtotal' => $hargaDinamis
            ];
        }

        // Simpan kembali ke session
        session()->put('cart', $cart);
        $this->cart = $cart;

        // Beri tahu Navbar untuk update angka keranjang
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Produk ditambahkan ke keranjang.');
    }

    public function render()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->where(function($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('kode', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.catalog.index', [
            'products' => $products
        ])->layout('layouts.app');
    }
}