<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Operation;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = Feature::all();
        $operations = Operation::all();

        foreach ($features as $feature) {
            foreach ($operations as $operation) {
                // Skip certain combinations if needed
                if ($feature->name == 'Dashboard' && in_array($operation->name, ['Create', 'Edit', 'Delete'])) {
                    continue;
                }

                Permission::create([
                    'feature_id' => $feature->id,
                    'operation_id' => $operation->id,
                    'slug' => $feature->slug . '.' . $operation->slug,
                ]);
            }
        }
    }
}