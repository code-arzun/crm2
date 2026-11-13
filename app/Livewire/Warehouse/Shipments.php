<?php

namespace App\Livewire\Warehouse;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class Shipments extends Component
{
    use WithPagination;

    // Filter
    public $search = '';
    public $filterShipStatus = 'semua';
    public $filterPeriode = 'semua';

    // Modal
    public $isShipModalOpen = false;
    public $selectedOrder = null;
    public $ekspedisi = 'JNE';
    public $no_resi = '';
    public $qty_kirim = [];

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'warehouse'])) {
            abort(403);
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPeriode() { $this->resetPage(); }

    public function openShipModal($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);
        $this->selectedOrder = $order;
        
        // Isi default qty_kirim dengan sisa yang belum dikirim
        $this->qty_kirim = [];
        foreach ($order->items as $item) {
            $sisa = $item->qty - $item->qty_terkirim;
            $this->qty_kirim[$item->id] = $sisa > 0 ? $sisa : 0;
        }

        $this->isShipModalOpen = true;
    }

    public function processShipment()
    {
        $order = $this->selectedOrder;
        
        if (!$order) return;

        // 1. Buat data Shipment
        $shipment = Shipment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'ekspedisi' => $this->ekspedisi,
            'no_resi' => $this->no_resi,
            'alamat_kirim' => $order->customer->alamat ?? 'Alamat belum diisi',
            'status' => 'dikirim',
            'shipped_at' => now(),
        ]);

        $adaYangDikirim = false;

        // 2. Loop item yang dikirim
        foreach ($order->items as $item) {
            $qtyKirim = (int) ($this->qty_kirim[$item->id] ?? 0);
            
            if ($qtyKirim > 0) {
                $adaYangDikirim = true;
                
                $shipment->items()->create([
                    'order_item_id' => $item->id,
                    'qty_dikirim' => $qtyKirim,
                ]);

                $item->increment('qty_terkirim', $qtyKirim);

                // POTONG STOK FISIK
                $product = $item->product;
                if ($product && $product->tipe->value === 'barang') {
                    $product->decrement('stok', $qtyKirim);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'tipe' => 'out',
                        'qty' => $qtyKirim,
                        'referensi' => $order->kode_order,
                        'keterangan' => 'Pengiriman barang (Resi: ' . $this->no_resi . ')',
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }

        if (!$adaYangDikirim) {
            $this->addError('qty_kirim', 'Tidak ada barang yang dipilih untuk dikirim.');
            return;
        }

        // 3. Update Status Order
        $order->refresh();
        $semuaTerkirim = $order->items->every(fn($i) => $i->qty_terkirim >= $i->qty);
        
        if ($semuaTerkirim) {
            $order->update(['status' => 'dikirim']);
        } else {
            $order->update(['status' => 'diproses']); 
        }

        $order->refresh()->cekSelesai();

        $this->isShipModalOpen = false;
        $this->dispatch('notify', message: 'Pengiriman berhasil diproses.');
    }

    public function updatingFilterShipStatus() { $this->resetPage(); }

    public function render()
    {
        // JANGAN pakai whereHas yang memfilter sisa, tampilkan semua pesanan aktif
        $query = Order::query()
            ->with(['customer', 'items', 'shipments'])
            ->whereIn('status', ['dikonfirmasi', 'diproses', 'dikirim', 'selesai']);

        // 1. Filter Pencarian
        if ($this->search) {
            $query->where(function($q) {
                $q->where('kode_order', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', fn($c) => $c->where('nama_lengkap', 'like', '%' . $this->search . '%'));
            });
        }

        // 2. Filter Status Pengiriman (BARU)
        if ($this->filterShipStatus === 'belum_kirim') {
            // Belum ada resi sama sekali
            $query->whereDoesntHave('shipments');
        } elseif ($this->filterShipStatus === 'dalam_pengiriman') {
            // Ada resi berstatus 'dikirim' dan semua barang sudah keluar gudang
            $query->whereHas('shipments', fn($q) => $q->where('status', 'dikirim'))
                  ->whereDoesntHave('items', fn($q) => $q->whereColumn('qty_terkirim', '<', 'qty'));
        } elseif ($this->filterShipStatus === 'sebagian') {
            // Masih ada sisa barang yang belum dikirim
            $query->whereHas('items', fn($q) => $q->whereColumn('qty_terkirim', '<', 'qty'))
                  ->whereHas('shipments'); // Tapi sudah pernah dikirim (ada resi)
        } elseif ($this->filterShipStatus === 'terkirim_semua') {
            // 1. Pastikan tidak ada sisa barang yang belum keluar gudang
            $query->whereDoesntHave('items', fn($q) => $q->whereColumn('qty_terkirim', '<', 'qty'))
                  
                  // 2. Pastikan TIDAK ADA resi yang masih berstatus 'dikirim' (di jalan)
                  ->whereDoesntHave('shipments', fn($q) => $q->where('status', 'dikirim'))
                  
                  // 3. Pastikan PUNYA resi yang berstatus 'diterima' (minimal 1)
                  ->whereHas('shipments', fn($q) => $q->where('status', 'diterima'));
        }


        // 3. Filter Periode
        if ($this->filterPeriode === 'hari_ini') {
            $query->whereDate('created_at', today());
        } elseif ($this->filterPeriode === 'bulan_ini') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($this->filterPeriode === 'tahun_ini') {
            $query->whereYear('created_at', now()->year);
        }

        return view('livewire.warehouse.shipments', [
            'orders' => $query->latest()->paginate(10)
        ])->layout('layouts.app');
    }
}