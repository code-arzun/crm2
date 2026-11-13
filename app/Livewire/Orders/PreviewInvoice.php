<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;

class PreviewInvoice extends Component
{
    public $order;

    public function mount($id)
    {
        $order = Order::with(['items', 'customer', 'invoice'])->findOrFail($id);

        // Keamanan: Pastikan yang buka adalah Admin/Finance, atau Customer pemilik pesanan
        if (auth()->user()->hasRole('customer') && $order->customer_id !== auth()->user()->customer?->id) {
            abort(403);
        }

        if (!$order->invoice) {
            abort(404, 'Invoice belum diterbitkan.');
        }

        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.orders.preview-invoice')->layout('layouts.app');
    }
}