<?php

namespace App\Livewire\Public;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Catalog extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCategoryId() { $this->resetPage(); }

    public function render()
    {
        $query = Product::where('is_active', true);

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('kode', 'like', '%' . $this->search . '%');
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        return view('livewire.public.catalog', [
            'products' => $query->latest()->paginate(12),
            'categories' => Category::where('is_active', true)->get()
        ])->layout('layouts.public');
    }
}