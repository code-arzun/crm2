<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            ['Budi Santoso', '08123456789', 'PT Maju Jaya', 'Jl. Contoh No. 1', 'UMKM'],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_lengkap',
            'telepon',
            'nama_perusahaan',
            'alamat',
            'kategori_bisnis', // UMKM, Supplier, Retail, dll
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F46E5']]],
        ];
    }
}