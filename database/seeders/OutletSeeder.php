<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('outlets')->insert([
            [
                'name' => 'Futsal',
                'type' => 'pos',
                'status' => 'open',
                'phone' => '1234567890',
                'whatsapp' => '1234567890',
                'email' => 'outletA@example.com',
                'address' => '123 Main Street, City A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kolam',
                'type' => 'pos',
                'status' => 'open',
                'phone' => '0987654321',
                'whatsapp' => '0987654321',
                'email' => 'outletB@example.com',
                'address' => '456 Elm Street, City B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}