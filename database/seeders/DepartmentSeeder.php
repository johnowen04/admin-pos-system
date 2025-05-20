<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'name' => 'Ice Cream',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'LSC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Toko',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}