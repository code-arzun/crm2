<?php

namespace App\Livewire\Returns;

use App\Actions\Returns\ApproveReturnAction;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ReturnOrder;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'semua';

    // Properti Modal
    public $isModalOpen = false;
    public $selectedReturn = null;
    
    // Properti Form Refund
    public $refund_method = 'saldo'; // saldo / transfer
    public $biaya_retur = 0;
    public $refund_proof; // Upload bukti jika transfer

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance'])) {
            abort(403);
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function openModal($id)
    {
        // Load relasi items dan orderItem
        $this->selectedReturn = ReturnOrder::with(['order.customer', 'items.orderItem.product'])->findOrFail($id);
        
        $this->refund_method = 'saldo';
        $this->biaya_retur = 0;
        $this->refund_proof = null;
        $this->isModalOpen = true;
    }

    public function tolakRetur()
    {
        $this->selectedReturn->update([
            'status' => 'ditolak',
            'approved_by' => auth()->id(),
        ]);

        $this->isModalOpen = false;
        $this->dispatch('notify', message: 'Pengajuan retur ditolak.');
    }

    public function setujuiRetur(ApproveReturnAction $action)
    {
        $refundProofPath = null;
        
        // Handle upload file di sini (Livewire)
        if ($this->refund_method === 'transfer' && $this->refund_proof) {
            $refundProofPath = $this->refund_proof->store('refund-proofs', 'public');
        }

        // Panggil Action
        $action->execute($this->selectedReturn, auth()->user(), [
            'biaya_retur' => $this->biaya_retur,
            'refund_method' => $this->refund_method,
            'refund_proof_path' => $refundProofPath,
        ]);

        $this->isModalOpen = false;
        $this->dispatch('notify', message: 'Retur disetujui. Menunggu barang diterima Gudang.');
    }

    public function render()
    {
        $query = ReturnOrder::query()->with(['order.customer', 'orderItem.product'])->latest();

        if ($this->search) {
            $query->whereHas('order', function($q) {
                $q->where('kode_order', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', fn($c) => $c->where('nama_lengkap', 'like', '%' . $this->search . '%'));
            });
        }

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.returns.index', [
            'returns' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}