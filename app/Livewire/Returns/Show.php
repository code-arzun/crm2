<?php

namespace App\Livewire\Returns;

use App\Models\ReturnOrder;
use Livewire\Component;

class Show extends Component
{
    public $return;

    public function mount($id)
    {
        $this->return = ReturnOrder::with(['order.customer', 'orderItem.product', 'approver'])->findOrFail($id);

        // Proteksi: Customer hanya bisa lihat return miliknya sendiri
        if (auth()->user()->hasRole('customer') && $this->return->order->customer_id !== auth()->user()->customer?->id) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.returns.show')->layout('layouts.app');
    }
}