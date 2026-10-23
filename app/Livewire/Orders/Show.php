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

    public $nominal_bayar = 0;

    public function mount($id)
    {
        $this->order = Order::with(['items', 'invoice', 'payments'])
            ->where('customer_id', auth()->user()->customer?->id)
            ->findOrFail($id);
            
        // Set default nominal ke sisa tagihan
        $this->nominal_bayar = $this->order->sisaTagihan();
    }

    public function uploadBukti()
    {
        $this->validate([
            'nominal_bayar' => 'required|numeric|min:1|max:' . $this->order->sisaTagihan(),
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'nominal_bayar.max' => 'Nominal tidak boleh melebihi sisa tagihan.',
        ]);

        $path = $this->bukti_transfer->store('bukti-transfer', 'public');

        $this->order->payments()->create([
            'metode' => 'transfer',
            'jumlah' => $this->nominal_bayar, // Gunakan input user
            'bukti_transfer' => $path,
            'status' => 'pending',
        ]);

        $this->reset('bukti_transfer');
        $this->order->refresh(); 

        $this->dispatch('notify', message: 'Bukti transfer berhasil diunggah. Menunggu verifikasi.');
    }

    public function render()
    {
        return view('livewire.orders.show')->layout('layouts.app');
    }
}