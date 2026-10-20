<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public $order;
    public $bukti_transfer;

    public function mount($id)
    {
        $this->order = Order::with(['items', 'invoice', 'payments'])
            ->where('customer_id', auth()->user()->customer?->id)
            ->findOrFail($id);
    }

    public function uploadBukti()
    {
        $this->validate([
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $this->bukti_transfer->store('bukti-transfer', 'public');

        $this->order->payments()->create([
            'metode' => 'transfer',
            'jumlah' => $this->order->total,
            'bukti_transfer' => $path,
            'status' => 'pending',
        ]);

        $this->reset('bukti_transfer');
        $this->order->refresh(); 

        $this->dispatch('notify', message: 'Bukti transfer berhasil diunggah.');
    }

    public function render()
    {
        return view('livewire.orders.show')->layout('layouts.app');
    }
}