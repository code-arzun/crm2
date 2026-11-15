<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable;

class StocksExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection(): Enumerable
    {
        // Ambil semua produk bertipe barang
        return Product::with('category')
            ->where('tipe', 'barang')
            ->orderBy('stok', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Produk',
            'Nama Produk',
            'Kategori',
            'Sisa Stok',
            'Satuan',
            'Status',
        ];
    }

    public function map($product): array
    {
        // Logika Status Stok
        if ($product->stok == 0) {
            $status = 'Habis';
        } elseif ($product->stok <= 10) {
            $status = 'Menipis';
        } else {
            $status = 'Aman';
        }

        return [
            $product->kode,
            $product->nama,
            $product->category->nama ?? '-',
            $product->stok,
            $product->satuan,
            $status,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}