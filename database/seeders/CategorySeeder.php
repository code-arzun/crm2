<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Elektronik', 'ATK', 'Furniture', 'Jasa'];

        foreach ($categories as $nama) {
            Category::updateOrCreate(
                ['slug' => Str::slug($nama)],
                ['nama' => $nama, 'is_active' => true]
            );
        }

        $this->command->info('CategorySeeder: ' . count($categories) . ' kategori dibuat.');
    }
}