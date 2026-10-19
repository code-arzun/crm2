<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class MyOrders extends Component
{
    use WithPagination;

    public function render()
    {
        // Ambil data customer dari user yang sedang login
        $customer = auth()->user()->customer;

        // Ambil order milik customer tersebut
        $orders = Order::query()
            ->where('customer_id', $customer?->id)
            ->latest()
            ->paginate(10);

        return view('livewire.orders.my-orders', [
            'orders' => $orders
        ])->layout('layouts.app');
    }
}