<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        // 1. Cari atau buat Kategori baru berdasarkan nama di Excel
        $category = Category::firstOrCreate(
            ['nama' => $row['kategori']],
            ['slug' => Str::slug($row['kategori'])]
        );

        // 2. Simpan Produk
        return new Product([
            'kode' => $row['kode'],
            'nama' => $row['nama'],
            'category_id' => $category->id,
            'tipe' => strtolower($row['tipe']), // pastikan lowercase 'barang'/'jasa'
            'harga_default' => $row['harga_default'],
            'stok' => $row['stok'] ?? 0,
            'satuan' => $row['satuan'],
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|unique:products,kode',
            'nama' => 'required',
            'kategori' => 'required',
            'tipe' => 'required|in:barang,jasa',
            'harga_default' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'satuan' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode.unique' => 'Kode produk :input sudah ada di database.',
            'tipe.in' => 'Tipe produk harus "barang" atau "jasa".',
        ];
    }
}