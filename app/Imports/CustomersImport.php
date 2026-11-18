<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

class CustomersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        // 1. Buat User otomatis untuk customer ini (Password default: password)
        // Email dibuat otomatis dari nomor telepon agar unik
        $email = $row['telepon'] . '@customer.com';
        
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $row['nama_lengkap'],
                'telepon' => $row['telepon'],
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        
        // Beri role customer (pastikan RoleSeeder sudah dijalankan)
        if (!$user->hasRole('customer')) {
            $user->assignRole('customer');
        }

        // 2. Simpan Profil Customer
        return new Customer([
            'user_id' => $user->id,
            'nama_lengkap' => $row['nama_lengkap'],
            'telepon' => $row['telepon'],
            'nama_perusahaan' => $row['nama_perusahaan'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'kategori_bisnis' => $row['kategori_bisnis'] ?? 'UMKM',
            'segment' => 'new',
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required',
            'telepon' => 'required|unique:customers,telepon',
            'nama_perusahaan' => 'nullable',
            'alamat' => 'nullable',
            'kategori_bisnis' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'telepon.unique' => 'Nomor telepon :input sudah terdaftar.',
        ];
    }
}