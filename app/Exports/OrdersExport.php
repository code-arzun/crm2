<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable; // <-- Tambahkan ini

class OrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // PERBAIKAN 1: Tambahkan : Enumerable
    public function collection(): Enumerable
    {
        $query = Order::with(['customer', 'sales']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No. Order',
            'Tanggal',
            'Nama Customer',
            'Perusahaan',
            'Sales PIC',
            'Status Pesanan',
            'Status Pembayaran',
            'Metode Kirim',
            'Biaya Kirim',
            'Subtotal',
            'Diskon',
            'PPN',
            'Grand Total',
        ];
    }

    public function map($order): array
    {
        return [
            $order->kode_order,
            $order->created_at->format('d/m/Y H:i'),
            $order->customer->nama_lengkap ?? '-',
            $order->customer->nama_perusahaan ?? '-',
            $order->sales->name ?? 'Sistem',
            $order->status->label(),
            $order->payment_status->label(),
            $order->metode_pengiriman === 'ekspedisi' ? 'Ekspedisi' : 'Ambil Sendiri',
            $order->biaya_pengiriman,
            $order->subtotal,
            $order->diskon,
            $order->ppn,
            $order->total,
        ];
    }

    // PERBAIKAN 2: Tambahkan return type array
    public function styles(Worksheet $sheet): array
    {
        return [
            // Baris pertama (Header) ditebalkan
            1 => ['font' => ['bold' => true]],
        ];
    }
}