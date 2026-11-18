<?php

namespace App\Livewire\Customers;

use App\Exports\CustomerTemplateExport;
use App\Imports\CustomersImport;
use App\Models\Customer;
use App\Models\CustomerTier;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    
    // Properti untuk Modal & Form
    public $isModalOpen = false;
    public $customerId, $nama_lengkap, $telepon, $nama_perusahaan, $alamat, $tier_id;

    // Fungsi untuk mendeteksi pencarian
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = Customer::query()
            ->with('tier')
            ->where('nama_lengkap', 'like', '%' . $this->search . '%')
            ->orWhere('telepon', 'like', '%' . $this->search . '%')
            ->orWhere('nama_perusahaan', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        // Ambil data tier untuk dropdown di form
        $tiers = CustomerTier::all();

        return view('livewire.customers.index', [
            'customers' => $customers,
            'tiers' => $tiers
        ])->layout('layouts.app');
    }

    // Fungsi Buka Modal untuk Tambah
    public function create()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    // Fungsi Buka Modal untuk Edit
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        
        $this->customerId = $customer->id;
        $this->nama_lengkap = $customer->nama_lengkap;
        $this->telepon = $customer->telepon;
        $this->nama_perusahaan = $customer->nama_perusahaan;
        $this->alamat = $customer->alamat;
        $this->tier_id = $customer->tier_id;

        $this->isModalOpen = true;
    }

    // Fungsi Simpan (Tambah / Edit)
    public function store()
    {
        $this->validate([
            'nama_lengkap' => 'required|string|max:255',
            'telepon' => [
                'required', 'string', 'max:20',
                // Validasi telepon unik, tapi abaikan jika sedang edit data sendiri
                Rule::unique('customers', 'telepon')->ignore($this->customerId)
            ],
            'nama_perusahaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'tier_id' => 'nullable|exists:customer_tiers,id',
        ]);

        Customer::updateOrCreate(
            ['id' => $this->customerId], // Jika ada ID, update. Jika kosong, buat baru.
            [
                'nama_lengkap' => $this->nama_lengkap,
                'telepon' => $this->telepon,
                'nama_perusahaan' => $this->nama_perusahaan,
                'alamat' => $this->alamat,
                'tier_id' => $this->tier_id,
            ]
        );

        // session()->flash('message', $this->customerId ? 'Customer berhasil diupdate.' : 'Customer baru berhasil ditambahkan.');
        $this->dispatch('notify', message: $this->customerId ? 'Customer berhasil diupdate.' : 'Customer baru berhasil ditambahkan.');

        $this->closeModal();
    }

    // Fungsi Hapus
    public function delete($id)
    {
        Customer::findOrFail($id)->delete();
        // session()->flash('message', 'Customer berhasil dihapus.');
        $this->dispatch('notify', message: 'Customer berhasil dihapus.');
    }

    // Fungsi Tutup Modal & Reset
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->customerId = null;
        $this->nama_lengkap = '';
        $this->telepon = '';
        $this->nama_perusahaan = '';
        $this->alamat = '';
        $this->tier_id = '';
    }

    // Properti Import
    public $isImportModalOpen = false;
    public $file_excel;

    public function downloadTemplate()
    {
        return Excel::download(new CustomerTemplateExport, 'Template_Import_Customer.xlsx');
    }

    public function import()
    {
        $this->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new CustomersImport, $this->file_excel->getRealPath());
            
            $this->isImportModalOpen = false;
            $this->reset('file_excel');
            $this->dispatch('notify', message: 'Data customer berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            $this->addError('file_excel', 'Gagal import. ' . implode(' | ', array_slice($errorMessages, 0, 3)));
        } catch (\Exception $e) {
            $this->addError('file_excel', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}