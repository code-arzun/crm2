<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        // Beri 1 baris contoh agar Admin paham formatnya
        return [
            ['PRD-001', 'Contoh Produk', 'Elektronik', 'barang', 100000, 10, 'pcs'],
        ];
    }

    public function headings(): array
    {
        return [
            'kode',
            'nama',
            'kategori', // Ketik nama kategori (misal: Elektronik)
            'tipe',     // barang / jasa
            'harga_default',
            'stok',
            'satuan',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 
                'fill' => [
                    'fillType' => 'solid', 
                    'startColor' => ['argb' => 'FF4F46E5']
                ]
            ],
        ];
    }
}