<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

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

        $this->dispatch('notify', message: $this->productId ? 'Produk diupdate.' : 'Produk ditambahkan.');
        $this->closeModal();
    }

    public function delete($id)
    {
        Product::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Produk dihapus.');
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
}