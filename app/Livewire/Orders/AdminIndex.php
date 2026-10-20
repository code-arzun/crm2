<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class AdminIndex extends Component
{
    use WithPagination;

     public function mount()
    {
        // Jika user tidak punya role yang diizinkan, tendang keluar
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'sales'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function approve($id)
    {
        $order = Order::findOrFail($id);
        
        // Ubah status menjadi dikonfirmasi
        $order->update([
            'status' => 'dikonfirmasi'
        ]);

        // Catat siapa yang meng-approve
        $order->approvals()->create([
            'user_id' => auth()->id(),
            'aksi' => 'approve',
            'catatan' => 'Disetujui via dashboard admin'
        ]);

        if (!$order->invoice) {
            $order->invoice()->create([
                'nomor_invoice' => \App\Models\Invoice::generateNomor(),
                'issued_at' => now(),
            ]);
        }

        $this->dispatch('notify', message: 'Pesanan ' . $order->kode_order . ' disetujui & invoice berhasil dibuat.');
    }

    public function render()
    {
        // Admin melihat SEMUA pesanan
        $orders = Order::query()
            ->with('customer')
            ->where('kode_order', 'like', '%' . $this->search . '%')
            ->orWhereHas('customer', function($query) {
                $query->where('nama_lengkap', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.orders.admin-index', [
            'orders' => $orders
        ])->layout('layouts.app');
    }
}