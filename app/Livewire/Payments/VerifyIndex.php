<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class VerifyIndex extends Component
{
    use WithPagination;

    public $activeTab = 'pending'; // Tab default saat dibuka

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    // Fungsi untuk pindah tab
    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage(); // Reset paginasi saat pindah tab
    }

    public function verify($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->verify(auth()->user());
        $payment->order->syncPaymentStatus();

        $this->dispatch('notify', message: 'Pembayaran berhasil diverifikasi.');
    }

    public function reject($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->reject(auth()->user(), 'Bukti transfer tidak valid.');

        $this->dispatch('notify', message: 'Pembayaran ditolak.');
    }

        public function render()
    {
        // Jika tab 'unpaid', kita query dari model Order
        if ($this->activeTab === 'unpaid') {
            $orders = \App\Models\Order::query()
                ->with('customer')
                // Status sudah dikonfirmasi tapi belum ada pembayaran yang diupload
                ->where('status', 'dikonfirmasi')
                ->whereDoesntHave('payments')
                ->latest()
                ->paginate(10);

            return view('livewire.payments.verify-index', [
                'orders' => $orders // Kirim variabel orders
            ])->layout('layouts.app');
        }

        // Jika tab 'pending' atau 'history', kita query dari model Payment
        $query = Payment::query()->with(['order.customer']);

        if ($this->activeTab === 'pending') {
            $query->where('status', 'pending')->latest();
        } else {
            $query->whereIn('status', ['verified', 'rejected'])->latest();
        }

        return view('livewire.payments.verify-index', [
            'payments' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}