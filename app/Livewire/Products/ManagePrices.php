<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Customer;
use App\Models\CustomerTier;
use Livewire\Component;
use Livewire\WithPagination;

class ManagePrices extends Component
{
    use WithPagination;

    public $productId;
    public $product;
    
    // Properti Form
    public $customer_id = '';
    public $tier_id = '';
    public $harga = '';
    public $berlaku_dari = '';
    public $berlaku_sampai = '';

    public function mount($productId)
    {
        // Hanya admin/sales yang bisa mengakses
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'sales'])) {
            abort(403);
        }

        $this->productId = $productId;
        $this->product = Product::findOrFail($productId);
    }

    public function simpanHarga()
    {
        $this->validate([
            'harga' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'tier_id' => 'nullable|exists:customer_tiers,id',
            'berlaku_dari' => 'nullable|date',
            'berlaku_sampai' => 'nullable|date|after_or_equal:berlaku_dari',
        ]);

        // Pastikan minimal salah satu (customer / tier) dipilih
        if (empty($this->customer_id) && empty($this->tier_id)) {
            $this->addError('customer_id', 'Pilih minimal satu: Customer atau Tier.');
            return;
        }

        ProductPrice::create([
            'product_id' => $this->productId,
            'customer_id' => $this->customer_id ?: null,
            'tier_id' => $this->tier_id ?: null,
            'harga' => $this->harga,
            'berlaku_dari' => $this->berlaku_dari ?: null,
            'berlaku_sampai' => $this->berlaku_sampai ?: null,
        ]);

        $this->reset(['customer_id', 'tier_id', 'harga', 'berlaku_dari', 'berlaku_sampai']);
        $this->dispatch('notify', message: 'Harga khusus berhasil ditambahkan.');
    }

    public function hapusHarga($id)
    {
        ProductPrice::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Harga khusus dihapus.');
    }

    public function render()
    {
        // Ambil daftar harga khusus untuk produk ini
        $prices = ProductPrice::with(['customer', 'tier'])
            ->where('product_id', $this->productId)
            ->latest()
            ->paginate(10);

        return view('livewire.products.manage-prices', [
            'prices' => $prices,
            'customers' => Customer::all(),
            'tiers' => CustomerTier::all()
        ])->layout('layouts.app');
    }
}