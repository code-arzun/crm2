<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // PPN
            ['key' => 'ppn_enabled', 'value' => '0', 'tipe' => 'bool'],
            ['key' => 'ppn_persen', 'value' => '11', 'tipe' => 'number'],

            // Invoice
            ['key' => 'invoice_format', 'value' => 'INV/{YYYY}{MM}/{seq}', 'tipe' => 'string'],
            ['key' => 'invoice_prefix', 'value' => 'INV', 'tipe' => 'string'],

            // Perusahaan
            ['key' => 'company_name', 'value' => 'PT Contoh Sejahtera', 'tipe' => 'string'],
            ['key' => 'company_address', 'value' => 'Jl. Contoh No. 123, Jakarta', 'tipe' => 'string'],
            ['key' => 'company_phone', 'value' => '021-1234567', 'tipe' => 'string'],
            ['key' => 'company_email', 'value' => 'info@contoh.com', 'tipe' => 'string'],
            ['key' => 'company_npwp', 'value' => '01.234.567.8-901.000', 'tipe' => 'string'],

            // Loyalty
            ['key' => 'loyalty_poin_per_rupiah', 'value' => '10000', 'tipe' => 'number'],
            ['key' => 'loyalty_enabled', 'value' => '1', 'tipe' => 'bool'],

            // Segmentasi
            ['key' => 'segment_dormant_bulan', 'value' => '6', 'tipe' => 'number'],
            ['key' => 'segment_loyal_tahun', 'value' => '1', 'tipe' => 'number'],
            ['key' => 'segment_loyal_min_order', 'value' => '10', 'tipe' => 'number'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['key' => $s['key']],
                ['value' => $s['value'], 'tipe' => $s['tipe']]
            );
        }

        $this->command->info('SettingSeeder: ' . count($settings) . ' settings dibuat.');
    }
}