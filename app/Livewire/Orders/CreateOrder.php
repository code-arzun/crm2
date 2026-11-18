<?php

namespace App\Livewire\Orders;

use App\Actions\Orders\PlaceOrderAction;
use App\Models\Product;
use Livewire\Component;

class CreateOrder extends Component
{
    public $cart = [];
    public $catatan = '';
    public $searchProduct = '';
    public $searchResults = [];
    public $isConfirmModalOpen = false;
    public $metode_pengiriman  = 'ekspedisi';
    public $biaya_pengiriman  = 0;
    protected $rules = [
        'metode_pengiriman ' => 'required|in:ekspedisi,ambil_sendiri',
    ];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function updateQty($id, $qty)
    {
        if ($qty < 1) {
            $qty = 1;
        }

        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty'] = (int) $qty;
            $this->cart[$id]['subtotal'] = $this->cart[$id]['qty'] * $this->cart[$id]['harga'];
            session()->put('cart', $this->cart);
            $this->dispatch('cart-updated');
        }
    }

    public function removeFromCart($id)
    {
        if (isset($this->cart[$id])) {
            unset($this->cart[$id]);
            session()->put('cart', $this->cart);
            $this->dispatch('cart-updated');
            $this->dispatch('notify', message: 'Produk dihapus dari keranjang.');
        }
    }

    public function addProductFromSearch($productId)
    {
        $product = Product::findOrFail($productId);
        
        if ($product->tipe->value === 'barang' && $product->stok < 1) {
            $this->dispatch('notify', message: 'Stok produk habis.');
            return;
        }

        $hargaDinamis = $product->hargaUntukCustomer(auth()->user()->customer);

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
            // $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $product->harga_default;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $product->hargaDinamis;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'nama' => $product->nama,
                // 'harga' => $product->harga_default,
                'harga' => $product->$hargaDinamis,
                'qty' => 1,
                // 'subtotal' => $product->harga_default,
                'subtotal' => $product->$hargaDinamis
            ];
        }

        session()->put('cart', $this->cart);
        $this->dispatch('cart-updated');
        $this->searchProduct = ''; 
        $this->dispatch('notify', message: 'Produk ditambahkan ke keranjang.');
    }

    public function getTotalProperty()
    {
        return collect($this->cart)->sum('subtotal');
    }

// 1. Fungsi untuk membuka Modal Konfirmasi
    public function konfirmasiPesanan()
    {
        $customer = auth()->user()->customer;

        // Cek Hard Gate (kelengkapan profil)
        if (!$customer->alamat || !$customer->nama_perusahaan) {
            $this->addError('cart', 'Mohon lengkapi Alamat dan Nama Perusahaan Anda terlebih dahulu.');
            return;
        }

        if (empty($this->cart)) {
            $this->addError('cart', 'Keranjang belanja masih kosong.');
            return;
        }

        // Buka Modal
        $this->isConfirmModalOpen = true;
    }

    // 2. Fungsi untuk mengeksekusi pesanan (dipanggil dari dalam modal)
    public function prosesPesanan(PlaceOrderAction $action)
    {
        // 1. Buat pesanan via Action
        $order = $action->execute(
            auth()->user()->customer,
            $this->cart,
            [
                'metode_pengiriman' => $this->metode_pengiriman,
                'catatan' => $this->catatan,
            ]
        );

        // 2. Bersihkan Keranjang
        session()->forget('cart');
        $this->dispatch('cart-updated'); 
        $this->isConfirmModalOpen = false;

        // 3. Redirect
        return redirect()->route('orders.my')->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi admin.');
    }

    public function render()
    {
        // Selalu reset pencarian di awal render
        $this->searchResults = [];
        
        // Jika ketikan 2 karakter atau lebih, cari produknya
        if (strlen($this->searchProduct) >= 2) {
            $this->searchResults = Product::where('is_active', true)
                ->where(function($query) {
                    $query->where('nama', 'like', '%' . $this->searchProduct . '%')
                          ->orWhere('kode', 'like', '%' . $this->searchProduct . '%');
                })
                ->limit(5)
                ->get();
        }

        return view('livewire.orders.create-order')->layout('layouts.app');
    }
}