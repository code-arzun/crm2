<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount()
    {
        // Pastikan hanya role tertentu yang bisa akses dashboard analitik
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance'])) {
            // Jika customer/sales/gudang, arahkan ke halaman masing-masing
            if (auth()->user()->hasRole('customer')) {
                return redirect()->route('catalog.index');
            } elseif (auth()->user()->hasRole('warehouse')) {
                return redirect()->route('warehouse.shipments');
            }
        }
    }

    public function render()
    {
        // 1. Statistik Utama
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders = Order::count();
        $totalCustomers = Customer::count();
        $pendingPayments = \App\Models\Payment::where('status', 'pending')->count();

        // 2. Data Grafik Penjualan (6 Bulan Terakhir)
        $chartData = [];
        $chartLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M');
            
            $revenue = Order::where('payment_status', 'paid')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total');
                
            $chartData[] = $revenue;
        }

        // 3. Produk Terlaris (Top 5)
        $topProducts = \App\Models\OrderItem::select('nama_produk', \DB::raw('SUM(qty) as total_qty'))
            ->groupBy('nama_produk')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 4. Stok Menipis (di bawah 10)
        $lowStocks = Product::where('tipe', 'barang')
            ->where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'totalCustomers' => $totalCustomers,
            'pendingPayments' => $pendingPayments,
            'chartLabels' => json_encode($chartLabels),
            'chartData' => json_encode($chartData),
            'topProducts' => $topProducts,
            'lowStocks' => $lowStocks,
        ])->layout('layouts.app');
    }
}