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
                'name' => 'Food',
                'departments_id' => 1, // Assuming department ID 1 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beverages',
                'departments_id' => 1, // Assuming department ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}