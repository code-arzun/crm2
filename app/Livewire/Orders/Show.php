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

    public function batalkanPesanan()
    {
        if ($this->order->status->value === 'menunggu_approval') {
            $this->order->update(['status' => 'batal']);
            $this->order->refresh();
            $this->dispatch('notify', message: 'Pesanan berhasil dibatalkan.');
        }
    }

    public function konfirmasiDiterima($shipmentId)
    {
        $shipment = \App\Models\Shipment::findOrFail($shipmentId);
        
        if ($shipment->order_id !== $this->order->id) {
            return;
        }

        $shipment->update([
            'status' => 'diterima',
            'received_at' => now(),
        ]);

        $this->order->refresh();
        
        // TAMBAHKAN BARIS INI: Cek apakah pesanan sudah selesai
        $this->order->cekSelesai();
        $this->order->refresh();

        $this->dispatch('notify', message: 'Status pengiriman diperbarui menjadi Diterima.');
    }

        public $isReturModalOpen = false;
    public $retur_qty = [];
    public $retur_alasan = '';
    public $retur_kategori = 'rusak';

    public function openReturModal()
    {
        $this->retur_qty = [];
        foreach ($this->order->items as $item) {
            $this->retur_qty[$item->id] = 0;
        }
        $this->retur_alasan = '';
        $this->isReturModalOpen = true;
    }

    public function ajukanRetur()
    {
        $adaYangDiretur = false;
        $totalQty = 0;

        // 1. Hitung total qty yang diajukan
        foreach ($this->retur_qty as $itemId => $qty) {
            if ($qty > 0) {
                $adaYangDiretur = true;
                $totalQty += $qty;
            }
        }

        if (!$adaYangDiretur) {
            $this->addError('retur_qty', 'Pilih minimal satu barang untuk diretur.');
            return;
        }

        // 2. Buat Header Retur
        $return = \App\Models\ReturnOrder::create([
            'order_id' => $this->order->id,
            'alasan' => $this->retur_alasan,
            'status' => 'diajukan',
            'total_qty_diretur' => $totalQty,
            'metode_retur' => 'ekspedisi', // Default sementara
        ]);

        // 3. Simpan Detail Barang Retur (return_items)
        foreach ($this->retur_qty as $itemId => $qty) {
            if ($qty > 0) {
                \App\Models\ReturnItem::create([
                    'return_id' => $return->id,
                    'order_item_id' => $itemId,
                    'qty' => $qty,
                    'alasan_retur' => $this->retur_kategori,
                ]);
            }
        }

        $this->isReturModalOpen = false;
        $this->dispatch('notify', message: 'Pengajuan retur berhasil dikirim.');
    }
}