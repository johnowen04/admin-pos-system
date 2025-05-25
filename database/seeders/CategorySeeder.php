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
                'department_id' => 1, // Assuming department ID 1 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ice Cream Walls',
                'department_id' => 1, // Assuming department ID 1 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ice Cream Glico',
                'department_id' => 1, // Assuming department ID 1 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lapangan Futsal',
                'department_id' => 2, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Voucher Futsal',
                'department_id' => 2, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lain lain',
                'department_id' => 2, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Minuman',
                'department_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Perlengkapan Futsal',
                'department_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Perlengkapan Kolam',
                'department_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rokok',
                'department_id' => 2, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Snack',
                'department_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toiletries',
                'department_id' => 3, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}