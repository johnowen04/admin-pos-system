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
        ]);
    }
}