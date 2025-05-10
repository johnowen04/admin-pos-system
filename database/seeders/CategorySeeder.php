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
                'name' => 'Electronics',
                'departments_id' => 1, // Assuming department ID 1 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Furniture',
                'departments_id' => 2, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Clothing',
                'departments_id' => 3, // Assuming department ID 3 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Books',
                'departments_id' => 4, // Assuming department ID 4 exists
                'is_shown' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toys',
                'departments_id' => 5, // Assuming department ID 5 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}