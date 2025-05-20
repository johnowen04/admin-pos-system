<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Ice Cream Aice',
                'departments_id' => 1, // Assuming department ID 1 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ice Cream Walls',
                'departments_id' => 1, // Assuming department ID 1 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ice Cream Glico',
                'departments_id' => 1, // Assuming department ID 1 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lapangan Futsal',
                'departments_id' => 2, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Voucher Futsal',
                'departments_id' => 2, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lain lain',
                'departments_id' => 2, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Minuman',
                'departments_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Perlengkapan Futsal',
                'departments_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Perlengkapan Kolam',
                'departments_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rokok',
                'departments_id' => 2, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Snack',
                'departments_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toiletries',
                'departments_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}