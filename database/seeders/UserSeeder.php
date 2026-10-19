<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Superadmin
        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@contoh.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'telepon' => '081100000001',
                'is_active' => true,
            ]
        );
        $superadmin->syncRoles(['superadmin']);

        // 2. Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@contoh.com'],
            [
                'name' => 'Admin CRM',
                'password' => Hash::make('password'),
                'telepon' => '081100000002',
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        // 3. Sales
        $sales = User::updateOrCreate(
            ['email' => 'sales@contoh.com'],
            [
                'name' => 'Budi Sales',
                'password' => Hash::make('password'),
                'telepon' => '081100000003',
                'is_active' => true,
            ]
        );
        $sales->syncRoles(['sales']);

        // 4. Finance
        $finance = User::updateOrCreate(
            ['email' => 'finance@contoh.com'],
            [
                'name' => 'Siti Finance',
                'password' => Hash::make('password'),
                'telepon' => '081100000004',
                'is_active' => true,
            ]
        );
        $finance->syncRoles(['finance']);

        // 5. Warehouse
        $warehouse = User::updateOrCreate(
            ['email' => 'gudang@contoh.com'],
            [
                'name' => 'Joko Gudang',
                'password' => Hash::make('password'),
                'telepon' => '081100000005',
                'is_active' => true,
            ]
        );
        $warehouse->syncRoles(['warehouse']);
        
        $this->command->info('UserSeeder: Akun staf berhasil dibuat (password default: password).');
    }
}