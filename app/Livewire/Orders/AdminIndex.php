<?php

namespace App\Livewire\Orders;

use App\Exports\OrdersExport;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class AdminIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterPeriode = 'semua';
    public $filterStatus = 'semua';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterPeriode()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function approve($id)
    {
        // Pastikan hanya admin/superadmin yang bisa approve (proteksi ganda)
        if (!auth()->user()->hasAnyRole(['superadmin', 'admin'])) {
            abort(403);
        }

        $order = Order::findOrFail($id);
        
        $order->update(['status' => 'dikonfirmasi']);

        $order->approvals()->create([
            'user_id' => auth()->id(),
            'aksi' => 'approve',
            'catatan' => 'Disetujui via dashboard admin'
        ]);

        if (!$order->invoice) {
            $order->invoice()->create([
                'nomor_invoice' => \App\Models\Invoice::generateNomor(),
                'issued_at' => now(),
            ]);
        }

        $this->dispatch('notify', message: 'Pesanan ' . $order->kode_order . ' berhasil disetujui.');
    }

    public function render()
    {
        $query = Order::query()->with('customer');

        // 1. Filter Pencarian (Kode Order / Nama Customer)
        if ($this->search) {
            $query->where(function($q) {
                $q->where('kode_order', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', fn($c) => $c->where('nama_lengkap', 'like', '%' . $this->search . '%'));
            });
        }

        // 2. Filter Status
        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        // 3. Filter Periode (Opsi B)
        if ($this->filterPeriode === 'hari_ini') {
            $query->whereDate('created_at', today());
        } elseif ($this->filterPeriode === 'bulan_ini') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } elseif ($this->filterPeriode === 'tahun_ini') {
            $query->whereYear('created_at', now()->year);
        }

        $orders = $query->latest()->paginate(10);

        return view('livewire.orders.admin-index', [
            'orders' => $orders
        ])->layout('layouts.app');
    }

    public $exportStartDate = '';
    public $exportEndDate = '';

    public function export()
    {
        $this->validate([
            'exportStartDate' => 'nullable|date',
            'exportEndDate' => 'nullable|date|after_or_equal:exportStartDate',
        ]);

        if ($this->exportStartDate && $this->exportEndDate) {
            $fileName = 'Data_Pesanan_' . $this->exportStartDate . '_-_' . $this->exportEndDate . '.xlsx';
        } else {
            $fileName = 'Data_Pesanan_' . now()->format('Ymd_His') . '.xlsx';
        }

        return Excel::download(new OrdersExport($this->exportStartDate, $this->exportEndDate), $fileName);
    }
}