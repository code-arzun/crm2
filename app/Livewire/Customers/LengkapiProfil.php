<?php

namespace App\Livewire\Customers;

use Livewire\Component;

class LengkapiProfil extends Component
{
    public $nama_perusahaan = '';
    public $alamat = '';
    public $kategori_bisnis = '';

    public function mount()
    {
        $customer = auth()->user()->customer;
        
        // Jika sudah lengkap, tendang ke dashboard
        if ($customer->nama_perusahaan && $customer->alamat) {
            return redirect()->route('dashboard');
        }

        // Isi data lama jika ada
        $this->nama_perusahaan = $customer->nama_perusahaan ?? '';
        $this->alamat = $customer->alamat ?? '';
    }

    public function simpan()
    {
        $this->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kategori_bisnis' => 'required|string', // (Kita simpan ke kolom baru nanti, atau ke catatan)
        ]);

        $customer = auth()->user()->customer;
        $customer->update([
            'nama_perusahaan' => $this->nama_perusahaan,
            'alamat' => $this->alamat,
            'kategori_bisnis' => $this->kategori_bisnis, // (Butuh migration tambah kolom)
        ]);

        return redirect()->route('dashboard')->with('success', 'Profil berhasil dilengkapi!');
    }

    public function render()
    {
        return view('livewire.customers.lengkapi-profil')->layout('layouts.app');
    }
}