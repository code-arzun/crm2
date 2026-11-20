<?php

namespace App\Livewire\Public;

use App\Models\Product;
use App\Models\OrderItem;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        // 1. Produk Terlaris (Berdasarkan total qty terjual)
        $topProducts = Product::where('is_active', true)
            ->where('tipe', 'barang')
            ->withCount(['orderItems as total_terjual' => function($query) {
                $query->select(\DB::raw('sum(qty)'));
            }])
            ->orderByDesc('total_terjual')
            ->limit(8)
            ->get();

        // 2. Produk Terbaru
        $newProducts = Product::where('is_active', true)
            ->latest()
            ->limit(8)
            ->get();

        return view('livewire.public.home', [
            'topProducts' => $topProducts,
            'newProducts' => $newProducts,
            'companyName' => \App\Models\Setting::get('company_name', 'PT Contoh Sejahtera'),
            'companyAddress' => \App\Models\Setting::get('company_address'),
        ])->layout('layouts.public');
    }
}