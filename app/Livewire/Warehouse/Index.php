<?php

namespace App\Livewire\Warehouse;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $bulk_qty = [];

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'warehouse'])) {
            abort(403);
        }
    }

    public function updatingSearch() { $this->resetPage(); }

    public function saveBulkStock()
    {
        $adaYangDisimpan = false;
        $jumlahProdukYangDiinput = 0;

        // 1. Hitung ada berapa produk yang diisi
        foreach ($this->bulk_qty as $qty) {
            if ($qty > 0) {
                $jumlahProdukYangDiinput++;
            }
        }

        if ($jumlahProdukYangDiinput == 0) {
            $this->dispatch('notify', message: 'Tidak ada perubahan stok.');
            return;
        }

        // 2. Tentukan tipe referensi
        $referensi = $jumlahProdukYangDiinput > 1 ? 'RESTOCK-MASSAL' : 'RESTOCK-SATUAN';

        // 3. Simpan
        foreach ($this->bulk_qty as $productId => $qty) {
            if ($qty > 0) {
                $product = Product::find($productId);
                if ($product) {
                    $product->increment('stok', $qty);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'tipe' => 'in',
                        'qty' => $qty,
                        'referensi' => $referensi,
                        'keterangan' => 'Penambahan stok via ' . ($referensi === 'RESTOCK-MASSAL' ? 'tabel massal' : 'tabel satuan'),
                        'user_id' => auth()->id(),
                    ]);
                    $adaYangDisimpan = true;
                }
            }
        }

        if ($adaYangDisimpan) {
            $this->bulk_qty = []; // Reset form
            $this->dispatch('notify', message: "Berhasil menyimpan $jumlahProdukYangDiinput produk.");
        }
    }

    public function render()
    {
        $products = Product::query()
            ->where('tipe', 'barang') 
            ->where(function($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('kode', 'like', '%' . $this->search . '%');
            })
            ->orderBy('stok', 'asc') 
            ->paginate(10);

        return view('livewire.warehouse.index', [
            'products' => $products
        ])->layout('layouts.app');
    }
}