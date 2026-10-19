<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SettingSeeder::class,
            CustomerTierSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            LeadSeeder::class,
            LoyaltyRuleSeeder::class,
            UserSeeder::class,
        ]);
    }
}