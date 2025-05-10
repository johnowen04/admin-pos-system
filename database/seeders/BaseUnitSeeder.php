<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('base_units')->insert([
            ['name' => 'gram', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'meter', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'piece', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'liter', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'second', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}