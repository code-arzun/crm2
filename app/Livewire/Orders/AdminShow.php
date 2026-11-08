<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;

class AdminShow extends Component
{
    public $order;

    public function mount($id)
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance', 'sales', 'warehouse'])) {
            abort(403);
        }

        $this->order = Order::with(['items.product', 'customer', 'payments.verifier', 'shipments.items'])->findOrFail($id);
    }

    public function approve()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance'])) {
            abort(403);
        }

        if ($this->order->status->value === 'menunggu_approval') {
            $this->order->update(['status' => 'dikonfirmasi']);
            $this->order->approvals()->create([
                'user_id' => auth()->id(),
                'aksi' => 'approve',
                'catatan' => 'Disetujui via halaman detail'
            ]);
            if (!$this->order->invoice) {
                $this->order->invoice()->create([
                    'nomor_invoice' => \App\Models\Invoice::generateNomor(),
                    'issued_at' => now(),
                ]);
            }
            $this->order->refresh();
            $this->dispatch('notify', message: 'Pesanan berhasil disetujui.');
        }
    }

    public function render()
    {
        return view('livewire.orders.admin-show')->layout('layouts.app');
    }

        // Properti untuk Modal Pengiriman
    public $isShipModalOpen = false;
    public $ekspedisi = 'JNE';
    public $no_resi = '';
    public $qty_kirim = [];

    // Fungsi Buka Modal
    public function openShipModal()
    {
        // Pastikan status pesanan sudah disetujui
        if (!in_array($this->order->status->value, ['dikonfirmasi', 'diproses'])) {
            $this->dispatch('notify', message: 'Pesanan belum disetujui.');
            return;
        }

        // Isi default qty_kirim dengan sisa yang belum dikirim
        $this->qty_kirim = [];
        foreach ($this->order->items as $item) {
            $sisa = $item->qty - $item->qty_terkirim;
            $this->qty_kirim[$item->id] = $sisa > 0 ? $sisa : 0;
        }

        $this->isShipModalOpen = true;
    }

    // Fungsi Proses Pengiriman
    public function processShipment()
    {
        $order = $this->order;
        
        // 1. Buat data Shipment
        $shipment = \App\Models\Shipment::create([
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

                    \App\Models\StockMovement::create([
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
            $order->update(['status' => 'diproses']); 
        }
        
        $order->refresh()->cekSelesai();

        $this->isShipModalOpen = false;
        $this->order->refresh(); // Refresh data agar tabel terupdate
        
        $this->dispatch('notify', message: 'Pengiriman berhasil diproses.');
    }

        public function verifyPayment($paymentId)
    {
        $payment = \App\Models\Payment::findOrFail($paymentId);
        
        if ($payment->order_id !== $this->order->id) {
            return;
        }

        // Panggil fungsi verify() di model Payment
        $payment->verify(auth()->user());
        
        // Sinkronisasi status order (Lunas / Sebagian)
        $payment->order->syncPaymentStatus();

        $this->order->refresh();
        $this->dispatch('notify', message: 'Pembayaran berhasil diverifikasi.');
    }

    public function rejectPayment($paymentId)
    {
        $payment = \App\Models\Payment::findOrFail($paymentId);
        
        if ($payment->order_id !== $this->order->id) {
            return;
        }

        $payment->reject(auth()->user(), 'Bukti transfer tidak valid.');

        $this->order->refresh();
        $this->dispatch('notify', message: 'Pembayaran ditolak.');
    }
}