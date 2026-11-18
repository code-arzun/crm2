<?php

namespace App\Livewire\Products;

use App\Exports\ProductTemplateExport;
use App\Imports\ProductsImport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $isModalOpen = false;
    
    // Properti Form
    public $productId, $kode, $nama, $tipe = 'barang', $category_id, $harga_default, $stok = 0, $satuan = 'pcs', $deskripsi;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::query()
            ->with('category')
            ->where('nama', 'like', '%' . $this->search . '%')
            ->orWhere('kode', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        $categories = Category::all();

        return view('livewire.products.index', [
            'products' => $products,
            'categories' => $categories
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->kode = $product->kode;
        $this->nama = $product->nama;
        $this->tipe = $product->tipe->value; // Ambil string value dari enum
        $this->category_id = $product->category_id;
        $this->harga_default = $product->harga_default;
        $this->stok = $product->stok;
        $this->satuan = $product->satuan;
        $this->deskripsi = $product->deskripsi;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'kode' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($this->productId)],
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:barang,jasa',
            'category_id' => 'nullable|exists:categories,id',
            'harga_default' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
        ]);

        Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'kode' => $this->kode,
                'nama' => $this->nama,
                'tipe' => $this->tipe,
                'category_id' => $this->category_id,
                'harga_default' => $this->harga_default,
                'stok' => $this->stok,
                'satuan' => $this->satuan,
                'deskripsi' => $this->deskripsi,
            ]
        );

        $this->dispatch('notify', message: $this->productId ? 'Produk berhasil diperbarui.' : 'Produk berhasil ditambahkan.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Product::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Produk berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->productId = null;
        $this->kode = 'PRD-' . strtoupper(\Illuminate\Support\Str::random(5)); // Auto-generate kode
        $this->nama = '';
        $this->tipe = 'barang';
        $this->category_id = '';
        $this->harga_default = 0;
        $this->stok = 0;
        $this->satuan = 'pcs';
        $this->deskripsi = '';
    }

    public $isImportModalOpen = false;
    public $file_excel;

    public function downloadTemplate()
    {
        return Excel::download(new ProductTemplateExport, 'Template_Import_Produk.xlsx');
    }

    public function import()
    {
        $this->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120', // Maks 5MB
        ]);

        try {
            Excel::import(new ProductsImport, $this->file_excel->getRealPath());
            
            $this->isImportModalOpen = false;
            $this->reset('file_excel');
            $this->dispatch('notify', message: 'Data produk berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            // Tampilkan error validasi
            $this->addError('file_excel', 'Gagal import. ' . implode(' | ', array_slice($errorMessages, 0, 3))); // Tampilkan maks 3 error
        } catch (\Exception $e) {
            $this->addError('file_excel', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}