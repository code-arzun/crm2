<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $elektronik = Category::where('slug', 'elektronik')->first();
        $atk = Category::where('slug', 'atk')->first();
        $furniture = Category::where('slug', 'furniture')->first();
        $jasa = Category::where('slug', 'jasa')->first();

        $products = [
            // Elektronik (barang)
            ['kode' => 'ELK-001', 'nama' => 'Laptop Dell Latitude 5440', 'tipe' => 'barang', 'harga_default' => 15000000, 'stok' => 10, 'satuan' => 'unit', 'category_id' => $elektronik->id],
            ['kode' => 'ELK-002', 'nama' => 'Monitor LG 24 inch', 'tipe' => 'barang', 'harga_default' => 2500000, 'stok' => 20, 'satuan' => 'unit', 'category_id' => $elektronik->id],
            ['kode' => 'ELK-003', 'nama' => 'Keyboard Mechanical Logitech', 'tipe' => 'barang', 'harga_default' => 850000, 'stok' => 50, 'satuan' => 'pcs', 'category_id' => $elektronik->id],
            ['kode' => 'ELK-004', 'nama' => 'Mouse Wireless Logitech', 'tipe' => 'barang', 'harga_default' => 250000, 'stok' => 100, 'satuan' => 'pcs', 'category_id' => $elektronik->id],
            ['kode' => 'ELK-005', 'nama' => 'Printer Epson L3250', 'tipe' => 'barang', 'harga_default' => 3200000, 'stok' => 15, 'satuan' => 'unit', 'category_id' => $elektronik->id],

            // ATK (barang)
            ['kode' => 'ATK-001', 'nama' => 'Kertas HVS A4 80gsm', 'tipe' => 'barang', 'harga_default' => 55000, 'stok' => 500, 'satuan' => 'rim', 'category_id' => $atk->id],
            ['kode' => 'ATK-002', 'nama' => 'Pulpen Snowman', 'tipe' => 'barang', 'harga_default' => 3000, 'stok' => 1000, 'satuan' => 'pcs', 'category_id' => $atk->id],
            ['kode' => 'ATK-003', 'nama' => 'Map Plastik', 'tipe' => 'barang', 'harga_default' => 2500, 'stok' => 800, 'satuan' => 'pcs', 'category_id' => $atk->id],
            ['kode' => 'ATK-004', 'nama' => 'Stapler Kenko', 'tipe' => 'barang', 'harga_default' => 25000, 'stok' => 200, 'satuan' => 'pcs', 'category_id' => $atk->id],
            ['kode' => 'ATK-005', 'nama' => 'Binder Clip', 'tipe' => 'barang', 'harga_default' => 8000, 'stok' => 300, 'satuan' => 'box', 'category_id' => $atk->id],

            // Furniture (barang)
            ['kode' => 'FUR-001', 'nama' => 'Meja Kerja Minimalis', 'tipe' => 'barang', 'harga_default' => 1500000, 'stok' => 30, 'satuan' => 'unit', 'category_id' => $furniture->id],
            ['kode' => 'FUR-002', 'nama' => 'Kursi Ergonomis', 'tipe' => 'barang', 'harga_default' => 2200000, 'stok' => 25, 'satuan' => 'unit', 'category_id' => $furniture->id],
            ['kode' => 'FUR-003', 'nama' => 'Lemari Arsip', 'tipe' => 'barang', 'harga_default' => 1800000, 'stok' => 15, 'satuan' => 'unit', 'category_id' => $furniture->id],
            ['kode' => 'FUR-004', 'nama' => 'Rak Buku', 'tipe' => 'barang', 'harga_default' => 750000, 'stok' => 40, 'satuan' => 'unit', 'category_id' => $furniture->id],
            ['kode' => 'FUR-005', 'nama' => 'Whiteboard 120x240cm', 'tipe' => 'barang', 'harga_default' => 550000, 'stok' => 20, 'satuan' => 'unit', 'category_id' => $furniture->id],

            // Jasa
            ['kode' => 'JSA-001', 'nama' => 'Instalasi Jaringan', 'tipe' => 'jasa', 'harga_default' => 500000, 'stok' => 0, 'satuan' => 'paket', 'category_id' => $jasa->id],
            ['kode' => 'JSA-002', 'nama' => 'Maintenance Server', 'tipe' => 'jasa', 'harga_default' => 2000000, 'stok' => 0, 'satuan' => 'bulan', 'category_id' => $jasa->id],
            ['kode' => 'JSA-003', 'nama' => 'Pelatihan Karyawan', 'tipe' => 'jasa', 'harga_default' => 5000000, 'stok' => 0, 'satuan' => 'hari', 'category_id' => $jasa->id],
            ['kode' => 'JSA-004', 'nama' => 'Konsultasi IT', 'tipe' => 'jasa', 'harga_default' => 750000, 'stok' => 0, 'satuan' => 'jam', 'category_id' => $jasa->id],
            ['kode' => 'JSA-005', 'nama' => 'Desain Website', 'tipe' => 'jasa', 'harga_default' => 8000000, 'stok' => 0, 'satuan' => 'proyek', 'category_id' => $jasa->id],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['kode' => $p['kode']], $p);
        }

        $this->command->info('ProductSeeder: ' . count($products) . ' produk dibuat.');
    }
}