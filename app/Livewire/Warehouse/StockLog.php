<?php

namespace App\Livewire\Warehouse;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class StockLog extends Component
{
    use WithPagination;

    public $activeTab = 'masuk'; // Default tab saat dibuka
    public $search = '';
    public $filterProduct = '';
     public $filterCategory = '';
    public $filterPeriode = 'semua';

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'warehouse'])) {
            abort(403);
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage(); // Reset halaman ke 1 saat pindah tab
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterProduct() { $this->resetPage(); }
    public function updatingFilterPeriode() { $this->resetPage(); }
    public function updatingFilterCategory() { $this->resetPage(); }

    public function render()
    {
        $query = StockMovement::query()->with(['product.category', 'user'])->latest();

        // 1. LOGIKA TAB (MASUK vs KELUAR)
        if ($this->activeTab === 'masuk') {
            // Tipe 'in' (restock) dan 'return' (retur barang)
            $query->whereIn('tipe', ['in', 'return']);
        } else {
            // Tipe 'out' (pengiriman) dan 'adjustment' (penyesuaian)
            $query->whereIn('tipe', ['out', 'adjustment']);
        }

        // 2. Filter Produk
        if ($this->filterProduct) {
            $query->where('product_id', $this->filterProduct);
        } else if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('kode', 'like', '%' . $this->search . '%');
            });
        }

        // 3. Filter Kategori
        if ($this->filterCategory) {
            $query->whereHas('product.category', function($q) {
                $q->where('id', $this->filterCategory);
            });
        }

        if ($this->filterProduct) {
            $query->where('product_id', $this->filterProduct);
        }

        // 4. Filter Periode
        if ($this->filterPeriode === 'hari_ini') {
            $query->whereDate('created_at', today());
        } elseif ($this->filterPeriode === 'bulan_ini') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($this->filterPeriode === 'tahun_ini') {
            $query->whereYear('created_at', now()->year);
        }

        return view('livewire.warehouse.stock-log', [
            'logs' => $query->paginate(10),
            'products' => Product::where('tipe', 'barang')->orderBy('nama')->get(),
            'categories' => \App\Models\Category::orderBy('nama')->get(),
        ])->layout('layouts.app');
    }
}