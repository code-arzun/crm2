<?php

namespace App\Livewire\CustomerProfile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    // Properti User
    public $name;
    public $email;
    public $telepon;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // Properti Customer
    public $nama_perusahaan;
    public $alamat;
    public $kategori_bisnis;

    public function mount()
    {
        $user = auth()->user();
        
        $this->name = $user->name;
        $this->email = $user->email;
        $this->telepon = $user->telepon;

        if ($user->hasRole('customer') && $user->customer) {
            $this->nama_perusahaan = $user->customer->nama_perusahaan;
            $this->alamat = $user->customer->alamat;
            $this->kategori_bisnis = $user->customer->kategori_bisnis;
        }
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        if ($user->hasRole('customer')) {
            $rules['nama_perusahaan'] = 'nullable|string|max:255';
            $rules['alamat'] = 'nullable|string';
            $rules['kategori_bisnis'] = 'nullable|string';
        }

        $this->validate($rules);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'telepon' => $this->telepon,
        ]);

        if ($user->hasRole('customer') && $user->customer) {
            $user->customer->update([
                'nama_lengkap' => $this->name,
                'telepon' => $this->telepon,
                'nama_perusahaan' => $this->nama_perusahaan,
                'alamat' => $this->alamat,
                'kategori_bisnis' => $this->kategori_bisnis,
            ]);
        }

        $this->dispatch('notify', message: 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini tidak cocok.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('notify', message: 'Password berhasil diubah.');
    }

    public function render()
    {
        return view('livewire.customer-profile.index')->layout('layouts.app');
    }
}