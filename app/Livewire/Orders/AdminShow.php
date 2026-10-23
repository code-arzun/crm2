<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;

class AdminShow extends Component
{
    public $order;

    public function mount($id)
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance', 'sales'])) {
            abort(403);
        }

        $this->order = Order::with(['items', 'customer', 'payments.verifier', 'shipments'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.orders.admin-show')->layout('layouts.app');
    }
}