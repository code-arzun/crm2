<?php

namespace App\Livewire\Payments;

use App\Models\Order;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class VerifyIndex extends Component
{
    use WithPagination;

    // Properti Filter
    public $search = '';
    public $filterPeriode = 'semua'; // <-- Ini yang hilang tadi
    public $filterPayStatus = 'semua';

    // Properti Modal
    public $isPayModalOpen = false;
    public $selectedOrder = null;

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin', 'finance'])) {
            abort(403);
        }
    }

    // Fungsi Reset Paginasi saat filter berubah
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPeriode() { $this->resetPage(); } // <-- Ini juga
    public function updatingFilterPayStatus() { $this->resetPage(); }

    public function openPayModal($orderId)
    {
        $this->selectedOrder = Order::with(['payments' => function($q) {
            $q->where('status', 'pending');
        }, 'customer'])->findOrFail($orderId);
        
        $this->isPayModalOpen = true;
    }

    public function verify($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->verify(auth()->user());
        $payment->order->refresh()->cekSelesai();
        
        $this->isPayModalOpen = false;
        $this->selectedOrder = null;
        $this->dispatch('notify', message: 'Pembayaran berhasil diverifikasi.');
    }

    public function reject($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->reject(auth()->user(), 'Bukti transfer tidak valid.');
        
        $this->isPayModalOpen = false;
        $this->selectedOrder = null;
        $this->dispatch('notify', message: 'Pembayaran ditolak.');
    }

    public function render()
    {
        $query = Order::query()->with(['customer', 'payments']);

        // 1. Filter Pencarian
        if ($this->search) {
            $query->where(function($q) {
                $q->where('kode_order', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', fn($c) => $c->where('nama_lengkap', 'like', '%' . $this->search . '%'));
            });
        }

        // 2. Filter Status Pembayaran
        if ($this->filterPayStatus === 'pending') {
            $query->whereHas('payments', fn($q) => $q->where('status', 'pending'));
        } elseif ($this->filterPayStatus === 'unpaid') {
            $query->whereDoesntHave('payments', fn($q) => $q->whereIn('status', ['pending', 'verified']));
        } elseif ($this->filterPayStatus === 'partial') {
            $query->where('payment_status', 'partial');
        } elseif ($this->filterPayStatus === 'paid') {
            $query->where('payment_status', 'paid');
        }

        // 3. Filter Periode
        if ($this->filterPeriode === 'hari_ini') {
            $query->whereDate('created_at', today());
        } elseif ($this->filterPeriode === 'bulan_ini') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($this->filterPeriode === 'tahun_ini') {
            $query->whereYear('created_at', now()->year);
        }

        return view('livewire.payments.verify-index', [
            'orders' => $query->latest()->paginate(10)
        ])->layout('layouts.app');
    }
}