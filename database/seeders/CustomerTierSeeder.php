<?php

namespace Database\Seeders;

use App\Models\CustomerTier;
use Illuminate\Database\Seeder;

class CustomerTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['nama' => 'Bronze', 'diskon_persen' => 0, 'min_transaksi' => 0],
            ['nama' => 'Silver', 'diskon_persen' => 2, 'min_transaksi' => 10000000],
            ['nama' => 'Gold', 'diskon_persen' => 5, 'min_transaksi' => 50000000],
        ];

        foreach ($tiers as $t) {
            CustomerTier::updateOrCreate(['nama' => $t['nama']], $t);
        }

        $this->command->info('CustomerTierSeeder: ' . count($tiers) . ' tier dibuat.');
    }
}