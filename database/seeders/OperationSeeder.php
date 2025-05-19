<?php

namespace Database\Seeders;

use App\Models\Operation;
use Illuminate\Database\Seeder;

class OperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operations = [
            ['name' => '*', 'slug' => '*'],
            ['name' => 'View', 'slug' => 'view'],
            ['name' => 'Create', 'slug' => 'create'],
            ['name' => 'Edit', 'slug' => 'edit'],
            ['name' => 'Delete', 'slug' => 'delete'],
            ['name' => 'Print', 'slug' => 'print'],
            ['name' => 'Export', 'slug' => 'export']
        ];

        foreach ($operations as $operation) {
            Operation::create($operation);
        }
    }
}