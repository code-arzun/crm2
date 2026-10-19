<?php

namespace Database\Seeders;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $sales = User::role('sales')->first();

        if (!$sales) {
            $this->command->warn('LeadSeeder: tidak ada user dengan role sales. Skip.');
            return;
        }

        $leads = [
            ['nama' => 'Andi Pratama', 'telepon' => '081298765001', 'nama_perusahaan' => 'PT Andi Corp', 'sumber' => 'website', 'status' => LeadStatus::BARU],
            ['nama' => 'Bambang Sutrisno', 'telepon' => '081298765002', 'nama_perusahaan' => 'CV Bambang Jaya', 'sumber' => 'pameran', 'status' => LeadStatus::DIHUBUNGI],
            ['nama' => 'Citra Dewi', 'telepon' => '081298765003', 'nama_perusahaan' => 'PT Citra Mandiri', 'sumber' => 'referral', 'status' => LeadStatus::NEGOSIASI],
            ['nama' => 'Doni Firmansyah', 'telepon' => '081298765004', 'nama_perusahaan' => 'UD Doni Sejahtera', 'sumber' => 'website', 'status' => LeadStatus::NEGOSIASI],
            ['nama' => 'Eka Putri', 'telepon' => '081298765005', 'nama_perusahaan' => 'PT Eka Abadi', 'sumber' => 'telepon', 'status' => LeadStatus::BARU],
        ];

        foreach ($leads as $l) {
            Lead::updateOrCreate(
                ['telepon' => $l['telepon']],
                array_merge($l, [
                    'sales_id' => $sales->id,
                    'catatan' => 'Lead dari seeder, siap di-follow up.',
                ])
            );
        }

        $this->command->info('LeadSeeder: ' . count($leads) . ' lead dibuat.');
    }
}