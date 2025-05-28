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
                if ($feature->name == 'Dashboard' && in_array($operation->name, ['*', 'Create', 'Edit', 'Delete', 'Print', 'Export'])) {
                    continue;
                }

                if ($feature->name == 'ACL' && in_array($operation->name, ['Create', 'Delete'])) {
                    continue;
                }

                if ($feature->name == 'Inventory' && in_array($operation->name, ['Create', 'Edit', 'Delete'])) {
                    continue;
                }

                if ($feature->name == 'POS' && in_array($operation->name, ['Edit', 'Delete'])) {
                    continue;
                }

                $isSuperUserOnly = false;

                if (in_array($feature->name, ['Feature', 'Operation', 'Permission', 'ACL'])) {
                    $isSuperUserOnly = true;
                }

                Permission::create([
                    'feature_id' => $feature->id,
                    'operation_id' => $operation->id,
                    'slug' => $feature->slug . '.' . $operation->slug,
                    'is_super_user_only' => $isSuperUserOnly,
                ]);
            }
        }
    }
}