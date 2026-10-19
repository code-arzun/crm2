<?php

namespace Database\Seeders;

use App\Models\LoyaltyRule;
use Illuminate\Database\Seeder;

class LoyaltyRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'nama' => 'Voucher First Order 10%',
                'tipe' => 'voucher',
                'kondisi' => ['first_order' => true, 'min_transaksi' => 1000000],
                'reward' => ['tipe' => 'persen', 'nilai' => 10],
                'is_active' => true,
            ],
            [
                'nama' => 'Poin Setiap Belanja',
                'tipe' => 'poin',
                'kondisi' => ['min_transaksi' => 0],
                'reward' => ['tipe' => 'poin_per_rupiah', 'nilai' => 10000],
                'is_active' => true,
            ],
            [
                'nama' => 'Diskon Member 1 Tahun',
                'tipe' => 'voucher',
                'kondisi' => ['member_sejak_bulan' => 12],
                'reward' => ['tipe' => 'persen', 'nilai' => 5],
                'is_active' => true,
            ],
            [
                'nama' => 'Free Ongkir Belanja 500rb',
                'tipe' => 'freeship',
                'kondisi' => ['min_transaksi' => 500000],
                'reward' => ['tipe' => 'gratis_ongkir', 'nilai' => 0],
                'is_active' => true,
            ],
        ];

        foreach ($rules as $r) {
            LoyaltyRule::updateOrCreate(['nama' => $r['nama']], $r);
        }

        $this->command->info('LoyaltyRuleSeeder: ' . count($rules) . ' loyalty rule dibuat.');
    }
}