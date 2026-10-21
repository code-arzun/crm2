<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class CartIcon extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    // Mendengarkan event dari halaman katalog
    #[On('cart-updated')] 
    public function updateCount()
    {
        $cart = session()->get('cart', []);
        $this->count = array_sum(array_column($cart, 'qty'));
    }

    public function render()
    {
        return view('livewire.cart-icon');
    }
}