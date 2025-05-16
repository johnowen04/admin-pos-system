<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            ['name' => 'piece', 'conversion_unit' => 1, 'to_base_unit_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'kilogram', 'conversion_unit' => 1000, 'to_base_unit_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'milligram', 'conversion_unit' => 0.001, 'to_base_unit_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'kilometer', 'conversion_unit' => 1000, 'to_base_unit_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'centimeter', 'conversion_unit' => 0.01, 'to_base_unit_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'dozen', 'conversion_unit' => 12, 'to_base_unit_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}