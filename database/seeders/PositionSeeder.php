<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Owner',
                'level' => 100,
            ],
            [
                'name' => 'Admin',
                'level' => 80,
            ],
            [
                'name' => 'Cashier',
                'level' => 40,
            ],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
