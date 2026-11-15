<?php

namespace App\Livewire\Returns;

use App\Models\ReturnOrder;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class ReceiveIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'semua';

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'warehouse'])) {
            abort(403);
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function terimaBarang($id)
    {
        $return = ReturnOrder::with('items.orderItem.product')->findOrFail($id);
        
        if ($return->status->value !== 'disetujui') return;

        // 1. Update status retur menjadi selesai
        $return->update(['status' => 'selesai']);

        // 2. Loop semua item dan kembalikan stoknya
        foreach ($return->items as $retItem) {
            $product = $retItem->orderItem->product;
            if ($product && $product->tipe->value === 'barang') {
                $product->increment('stok', $retItem->qty);
                
                StockMovement::create([
                    'product_id' => $product->id,
                    'tipe' => 'return',
                    'qty' => $retItem->qty,
                    'referensi' => $return->order->kode_order,
                    'keterangan' => 'Retur barang diterima gudang',
                    'user_id' => auth()->id(),
                ]);
            }
        }

        $this->dispatch('notify', message: 'Barang retur diterima & stok diperbarui.');
    }

    // Properti untuk Modal Detail
    public $isDetailModalOpen = false;
    public $selectedReturnDetail = null;

    public function openDetailModal($id)
    {
        $this->selectedReturnDetail = ReturnOrder::with(['items.orderItem.product', 'order.customer'])->findOrFail($id);
        $this->isDetailModalOpen = true;
    }

    public function render()
    {
        $query = ReturnOrder::query()
            ->with(['order.customer', 'orderItem.product'])
            ->whereIn('status', ['disetujui', 'selesai']) // Tampilkan yang disetujui & selesai
            ->latest();

        // Filter Pencarian
        if ($this->search) {
            $query->whereHas('order', function($q) {
                $q->where('kode_order', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', fn($c) => $c->where('nama_lengkap', 'like', '%' . $this->search . '%'));
            });
        }

        // Filter Status
        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.returns.receive-index', [
            'returns' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}