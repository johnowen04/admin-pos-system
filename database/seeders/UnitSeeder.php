<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::insert([
            [
                'name' => 'Pieces',
                'conversion_unit' => 1, // Base unit
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dozen',
                'conversion_unit' => 12, // 1 dozen = 12 pieces
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Box',
                'conversion_unit' => 24, // 1 box = 24 pieces
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kilogram',
                'conversion_unit' => 1000, // 1 kilogram = 1000 grams
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gram',
                'conversion_unit' => 1, // Base unit for weight
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Liter',
                'conversion_unit' => 1, // Base unit for volume
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Milliliter',
                'conversion_unit' => 0.001, // 1 milliliter = 0.001 liters
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}