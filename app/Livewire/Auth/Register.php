<?php

namespace App\Livewire\Auth;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Register extends Component
{
    public $name = '';
    public $telepon = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'telepon' => 'required|string|max:20',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ];

    public function register()
    {
        $this->validate();

        // 1. Buat User
        $user = User::create([
            'name' => $this->name,
            'telepon' => $this->telepon,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => true,
        ]);

        // 2. Beri Role Customer
        $user->assignRole('customer');

        // 3. Buat Profil Customer (Data minimal dulu)
        // Auto-assign sales: ambil user dengan role sales yang punya customer paling sedikit
        $salesId = User::role('sales')
            ->withCount('handledCustomers')
            ->orderBy('handled_customers_count', 'asc')
            ->first()?->id;

        Customer::create([
            'user_id' => $user->id,
            'sales_id' => $salesId,
            'nama_lengkap' => $this->name,
            'telepon' => $this->telepon,
            'segment' => 'new',
            'is_active' => true,
        ]);

        // 4. Login otomatis
        Auth::login($user);

        // 5. Arahkan ke Dashboard (Nanti kita buat pengalihan ke form pelengkap)
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.auth.simple');
    }
}