<?php

namespace App\Livewire\Orders;

use App\Actions\Payments\VerifyPaymentAction;
use App\Actions\Shipping\ProcessShipmentAction;
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
    public function processShipment(ProcessShipmentAction $action)
    {
        if (!$this->selectedOrder) return;

        try {
            // Panggil Action Class
            $action->execute($this->selectedOrder, [
                'ekspedisi' => $this->ekspedisi,
                'no_resi' => $this->no_resi,
                'qty_kirim' => $this->qty_kirim,
            ], auth()->id());

            $this->isShipModalOpen = false;
            $this->dispatch('notify', message: 'Pengiriman berhasil diproses.');

        } catch (\Exception $e) {
            $this->addError('qty_kirim', $e->getMessage());
        }
    }

    public function verifyPayment($paymentId, VerifyPaymentAction $action)
    {
        $payment = \App\Models\Payment::findOrFail($paymentId);
        
        if ($payment->order_id !== $this->order->id) return;

        $action->execute($payment, auth()->user());

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

    // Properti untuk edit ongkir
    public $isOngkirModalOpen = false;
    public $edit_biaya_pengiriman = 0;
    public $edit_metode_pengiriman = 'ekspedisi';

    public function openOngkirModal()
    {
        $this->edit_biaya_pengiriman = $this->order->biaya_pengiriman;
        $this->edit_metode_pengiriman = $this->order->metode_pengiriman;
        $this->isOngkirModalOpen = true;
    }

    public function updateOngkir()
    {
        $this->validate([
            'edit_biaya_pengiriman' => 'required|numeric|min:0',
            'edit_metode_pengiriman' => 'required|in:ekspedisi,ambil_sendiri',
        ]);

        // Jika ambil sendiri, biaya otomatis 0
        $biaya = $this->edit_metode_pengiriman === 'ambil_sendiri' ? 0 : $this->edit_biaya_pengiriman;

        $this->order->update([
            'metode_pengiriman' => $this->edit_metode_pengiriman,
            'biaya_pengiriman' => $biaya,
        ]);

        // Hitung ulang total tagihan
        $ppnPersen = \App\Models\Setting::get('ppn_enabled', false) ? \App\Models\Setting::get('ppn_persen', 11) : 0;
        $this->order->recalculate($ppnPersen);

        $this->isOngkirModalOpen = false;
        $this->order->refresh();
        $this->dispatch('notify', message: 'Biaya pengiriman berhasil diperbarui.');
    }

    // Properti untuk Modal Diskon
    public $isDiskonModalOpen = false;
    public $input_diskon = 0;
    public $input_diskon_tipe = 'nominal';

    public function openDiskonModal()
    {
        // Hanya Admin, Superadmin, Finance yang bisa buka
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance'])) {
            abort(403);
        }

        // Isi nilai awal dari database jika sudah ada
        $this->input_diskon = $this->order->diskon;
        $this->input_diskon_tipe = $this->order->diskon_tipe ?? 'nominal';
        
        $this->isDiskonModalOpen = true;
    }

    public function simpanDiskon()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance'])) {
            abort(403);
        }

        $this->validate([
            'input_diskon' => 'required|numeric|min:0',
            'input_diskon_tipe' => 'required|in:nominal,persen',
        ]);

        // Validasi tambahan: jika persen, tidak boleh lebih dari 100
        if ($this->input_diskon_tipe === 'persen' && $this->input_diskon > 100) {
            $this->addError('input_diskon', 'Diskon persen tidak boleh lebih dari 100%.');
            return;
        }

        $this->order->update([
            'diskon' => $this->input_diskon,
            'diskon_tipe' => $this->input_diskon_tipe,
        ]);

        // Hitung ulang total tagihan berdasarkan setting PPN
        $ppnPersen = \App\Models\Setting::get('ppn_enabled', false) ? \App\Models\Setting::get('ppn_persen', 11) : 0;
        $this->order->recalculate($ppnPersen);
        
        // Cek apakah invoice perlu di-update nominalnya (jika sudah ada)
        if ($this->order->invoice) {
            // (Opsional: Jika invoice sudah terbit, sebaiknya buat invoice baru / revisi)
        }

        $this->isDiskonModalOpen = false;
        $this->order->refresh();
        $this->dispatch('notify', message: 'Diskon berhasil diterapkan.');
    }
}