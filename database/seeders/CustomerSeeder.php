<?php

namespace Database\Seeders;

use App\Enums\CustomerSegment;
use App\Models\Customer;
use App\Models\CustomerTier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $bronze = CustomerTier::where('nama', 'Bronze')->first();
        $silver = CustomerTier::where('nama', 'Silver')->first();
        $gold = CustomerTier::where('nama', 'Gold')->first();

        $customers = [
            ['nama_lengkap' => 'Budi Santoso', 'telepon' => '081234567001', 'nama_perusahaan' => 'PT Maju Jaya', 'tier_id' => $gold->id, 'segment' => CustomerSegment::LOYAL],
            ['nama_lengkap' => 'Siti Aminah', 'telepon' => '081234567002', 'nama_perusahaan' => 'CV Sejahtera', 'tier_id' => $silver->id, 'segment' => CustomerSegment::ACTIVE],
            ['nama_lengkap' => 'Ahmad Hidayat', 'telepon' => '081234567003', 'nama_perusahaan' => 'PT Teknologi Nusantara', 'tier_id' => $silver->id, 'segment' => CustomerSegment::ACTIVE],
            ['nama_lengkap' => 'Dewi Lestari', 'telepon' => '081234567004', 'nama_perusahaan' => 'Toko Bangunan Indah', 'tier_id' => $bronze->id, 'segment' => CustomerSegment::NEW],
            ['nama_lengkap' => 'Rudi Hartono', 'telepon' => '081234567005', 'nama_perusahaan' => 'PT Global Mandiri', 'tier_id' => $gold->id, 'segment' => CustomerSegment::LOYAL],
            ['nama_lengkap' => 'Ani Wijaya', 'telepon' => '081234567006', 'nama_perusahaan' => 'CV Kreatif Digital', 'tier_id' => $bronze->id, 'segment' => CustomerSegment::NEW],
            ['nama_lengkap' => 'Joko Susilo', 'telepon' => '081234567007', 'nama_perusahaan' => 'UD Sumber Rejeki', 'tier_id' => $bronze->id, 'segment' => CustomerSegment::DORMANT],
            ['nama_lengkap' => 'Rina Marlina', 'telepon' => '081234567008', 'nama_perusahaan' => 'PT Sentosa Abadi', 'tier_id' => $silver->id, 'segment' => CustomerSegment::ACTIVE],
            ['nama_lengkap' => 'Hendra Gunawan', 'telepon' => '081234567009', 'nama_perusahaan' => 'Toko Elektronik Murah', 'tier_id' => $bronze->id, 'segment' => CustomerSegment::NEW],
            ['nama_lengkap' => 'Maya Sari', 'telepon' => '081234567010', 'nama_perusahaan' => 'PT Cahaya Baru', 'tier_id' => $silver->id, 'segment' => CustomerSegment::ACTIVE],
        ];

        $customerRole = Role::findByName('customer');

        foreach ($customers as $i => $c) {
            $email = 'customer' . ($i + 1) . '@contoh.com';

            // Buat akun user untuk customer (register mandiri)
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $c['nama_lengkap'],
                    'password' => Hash::make('password'),
                    'telepon' => $c['telepon'],
                    'is_active' => true,
                ]
            );
            $user->syncRoles([$customerRole]);

            // Buat profil customer
            Customer::updateOrCreate(
                ['telepon' => $c['telepon']],
                array_merge($c, [
                    'user_id' => $user->id,
                    'alamat' => 'Jl. Contoh No. ' . ($i + 1) . ', Jakarta',
                    'total_transaksi' => 0,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('CustomerSeeder: ' . count($customers) . ' customer dibuat (password: password).');
    }
}