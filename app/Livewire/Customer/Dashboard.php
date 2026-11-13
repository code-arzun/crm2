<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $customer = auth()->user()->customer;
        
        // Ambil pesanan aktif (belum selesai/batal)
        $activeOrders = Order::where('customer_id', $customer->id)
            ->whereIn('status', ['menunggu_approval', 'dikonfirmasi', 'diproses', 'dikirim'])
            ->latest()
            ->get();

        // Hitung total tagihan yang belum lunas
        $totalTagihan = $activeOrders->sum(fn($order) => $order->sisaTagihan());

        // Ambil pesanan yang sedang dikirim (untuk info resi)
        $shippingOrders = $activeOrders->filter(fn($order) => $order->shipments->count() > 0);

        return view('livewire.customers.dashboard', [
            'activeOrders' => $activeOrders,
            'totalTagihan' => $totalTagihan,
            'shippingOrders' => $shippingOrders,
        ])->layout('layouts.app');
    }
}