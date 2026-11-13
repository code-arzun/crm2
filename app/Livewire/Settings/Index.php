<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;

class Index extends Component
{
    // Properti Form
    public $company_name;
    public $company_address;
    public $company_phone;
    public $company_email;
    public $company_npwp;
    public $company_bank_name;
    public $company_bank_account;
    public $company_bank_holder;
    // Properti Rekening
    public $bank_accounts = [];
    public $new_bank_name = '';
    public $new_bank_account = '';
    public $new_bank_holder = '';

    public $ppn_enabled;
    public $ppn_persen;

    public $loyalty_enabled;
    public $loyalty_poin_per_rupiah;

    public function mount()
    {
        // Proteksi: Hanya Superadmin yang boleh ubah setting
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Hanya Superadmin yang dapat mengakses halaman ini.');
        }

        // Ambil data dari database
        $this->company_name = Setting::get('company_name', '');
        $this->company_address = Setting::get('company_address', '');
        $this->company_phone = Setting::get('company_phone', '');
        $this->company_email = Setting::get('company_email', '');
        $this->company_npwp = Setting::get('company_npwp', '');
        $this->company_bank_name = Setting::get('company_bank_name', 'BCA');
        $this->company_bank_account = Setting::get('company_bank_account', '123-456-7890');
        $this->company_bank_holder = Setting::get('company_bank_holder', 'PT Contoh Sejahtera');
        $this->bank_accounts = \App\Models\Setting::get('bank_accounts', []);
        
        $this->ppn_enabled = (bool) Setting::get('ppn_enabled', false);
        $this->ppn_persen = Setting::get('ppn_persen', 11);
        
        $this->loyalty_enabled = (bool) Setting::get('loyalty_enabled', true);
        $this->loyalty_poin_per_rupiah = Setting::get('loyalty_poin_per_rupiah', 10000);
    }

    public function addBankAccount()
    {
        $this->validate([
            'new_bank_name' => 'required|string|max:50',
            'new_bank_account' => 'required|string|max:50',
            'new_bank_holder' => 'required|string|max:100',
        ]);

        // Tambahkan ke array
        $this->bank_accounts[] = [
            'id' => uniqid(), // ID unik sementara untuk key
            'bank_name' => $this->new_bank_name,
            'account_number' => $this->new_bank_account,
            'holder' => $this->new_bank_holder,
            'is_active' => true, // Default aktif
        ];

        // Reset form
        $this->reset(['new_bank_name', 'new_bank_account', 'new_bank_holder']);
    }

    public function removeBankAccount($id)
    {
        // Filter array untuk membuang yang ID-nya cocok
        $this->bank_accounts = array_filter($this->bank_accounts, fn($bank) => $bank['id'] !== $id);
        // Re-index array agar urutannya rapi
        $this->bank_accounts = array_values($this->bank_accounts);
    }

    public function toggleBankAccount($id)
    {
        foreach ($this->bank_accounts as $index => $bank) {
            if ($bank['id'] === $id) {
                $this->bank_accounts[$index]['is_active'] = !$this->bank_accounts[$index]['is_active'];
                break;
            }
        }
    }

    public function save()
    {
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403);
        }

        $this->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_npwp' => 'nullable|string|max:50',
            'company_bank_name' => 'required|string',
            'company_bank_account' => 'required|string',
            'company_bank_holder' => 'required|string',
            'ppn_persen' => 'required|numeric|min:0|max:100',
            'loyalty_poin_per_rupiah' => 'required|numeric|min:1',
        ]);

        // Simpan ke database
        Setting::set('company_name', $this->company_name);
        Setting::set('company_address', $this->company_address);
        Setting::set('company_phone', $this->company_phone);
        Setting::set('company_email', $this->company_email);
        Setting::set('company_npwp', $this->company_npwp);
        Setting::set('company_bank_name', $this->company_bank_name);
        Setting::set('company_bank_account', $this->company_bank_account);
        Setting::set('company_bank_holder', $this->company_bank_holder);
        
        Setting::set('ppn_enabled', $this->ppn_enabled, 'bool');
        Setting::set('ppn_persen', $this->ppn_persen, 'number');
        
        Setting::set('loyalty_enabled', $this->loyalty_enabled, 'bool');
        Setting::set('loyalty_poin_per_rupiah', $this->loyalty_poin_per_rupiah, 'number');

        \App\Models\Setting::set('bank_accounts', $this->bank_accounts, 'json');

        $this->dispatch('notify', message: 'Pengaturan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.settings.index')->layout('layouts.app');
    }
}