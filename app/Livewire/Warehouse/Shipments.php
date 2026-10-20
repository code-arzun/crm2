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

    public $orderId;
    public $ekspedisi = 'JNE';
    public $no_resi = '';
    public $qty_kirim = []; // Array untuk menyimpan input qty per item
    public $isModalOpen = false;

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'warehouse'])) {
            abort(403);
        }
    }

    public function openShipModal($orderId)
    {
        $order = Order::with('items')->findOrFail($orderId);
        $this->orderId = $orderId;
        
        // Isi default qty_kirim dengan sisa yang belum dikirim
        $this->qty_kirim = [];
        foreach ($order->items as $item) {
            $sisa = $item->qty - $item->qty_terkirim;
            $this->qty_kirim[$item->id] = $sisa > 0 ? $sisa : 0;
        }

        $this->isModalOpen = true;
    }

    public function processShipment()
    {
        $order = Order::with('items')->findOrFail($this->orderId);
        
        // 1. Buat data Shipment
        $shipment = Shipment::create([
            'order_id' => $order->id,
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
                
                // Catat di shipment_items
                $shipment->items()->create([
                    'order_item_id' => $item->id,
                    'qty_dikirim' => $qtyKirim,
                ]);

                // Update qty_terkirim di order_items
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

        // 3. Cek apakah semua barang sudah terkirim lunas?
        $order->refresh();
        $semuaTerkirim = $order->items->every(fn($i) => $i->qty_terkirim >= $i->qty);
        
        if ($semuaTerkirim) {
            $order->update(['status' => 'dikirim']);
        } else {
            // Status parsial (kita asumsikan status string 'dikirim' tapi belum selesai, 
            // atau kita bisa buat status baru di masa depan)
            $order->update(['status' => 'diproses']); 
        }

        $this->isModalOpen = false;
        $this->dispatch('notify', message: 'Pengiriman berhasil diproses.');
    }

    public $activeTab = 'antrean';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        if ($this->activeTab === 'antrean') {
            // Hanya yang belum selesai terkirim
            $orders = Order::query()
                ->with(['customer', 'items', 'shipments'])
                ->whereIn('status', ['dikonfirmasi', 'diproses'])
                ->whereHas('items', function($q) {
                    $q->whereColumn('qty_terkirim', '<', 'qty');
                })
                ->latest()
                ->paginate(10);
        } else {
            // Riwayat: Yang sudah selesai terkirim semua
            $orders = Order::query()
                ->with(['customer', 'items', 'shipments'])
                ->whereIn('status', ['dikirim', 'selesai'])
                ->latest()
                ->paginate(10);
        }

        return view('livewire.warehouse.shipments', [
            'orders' => $orders
        ])->layout('layouts.app');
    }
}