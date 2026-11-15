<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // public function collection(): Enumerable
    // {
    //     $query = Payment::with(['order.customer', 'verifier']);

    //     if ($this->startDate && $this->endDate) {
    //         $query->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
    //     }

    //     return $query->latest()->get();
    // }

    public function collection(): Enumerable
    {
        // Gunakan query Payment (bukan Order)
        // Jadi yang diexport hanya yang ada bukti transfernya
        $query = Payment::with(['order.customer', 'verifier'])
            ->whereIn('status', ['pending', 'verified']); // Abaikan yang ditolak

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Bayar',
            'No. Order',
            'Customer',
            'Perusahaan',
            'Metode',
            'Nominal',
            'Status',
            'Diverifikasi Oleh',
            'Tanggal Verifikasi',
        ];
    }

    // public function map($payment): array
    // {
    //     return [
    //         $payment->created_at->format('d/m/Y H:i'),
    //         $payment->order->kode_order,
    //         $payment->order->customer->nama_lengkap ?? '-',
    //         $payment->order->customer->nama_perusahaan ?? '-',
    //         strtoupper($payment->metode->value),
    //         $payment->jumlah,
    //         strtoupper($payment->status->value),
    //         $payment->verifier->name ?? '-',
    //         $payment->verified_at ? $payment->verified_at->format('d/m/Y H:i') : '-',
    //     ];
    // }

    public function map($payment): array
    {
        return [
            $payment->created_at->format('d/m/Y H:i'),
            $payment->order->kode_order,
            $payment->order->customer->nama_lengkap ?? '-',
            $payment->order->customer->nama_perusahaan ?? '-',
            strtoupper($payment->metode->value), // misal: TRANSFER
            $payment->jumlah, // Nominal yang ditransfer
            strtoupper($payment->status->value), // harusnya PENDING atau VERIFIED
            $payment->verifier->name ?? '-',
            $payment->verified_at ? $payment->verified_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}