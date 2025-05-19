<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            ['name' => 'Dashboard', 'slug' => 'dashboard'],
            ['name' => 'Feature', 'slug' => 'feature'],
            ['name' => 'Operation', 'slug' => 'operation'],
            ['name' => 'Permission', 'slug' => 'permission'],
            ['name' => 'Role', 'slug' => 'role'],
            ['name' => 'ACL', 'slug' => 'acl'],
            ['name' => 'Outlet', 'slug' => 'outlet'],
            ['name' => 'Employee', 'slug' => 'employee'],
            ['name' => 'Department', 'slug' => 'department'],
            ['name' => 'Category', 'slug' => 'category'],
            ['name' => 'Base Unit', 'slug' => 'bu'],
            ['name' => 'Unit', 'slug' => 'unit'],
            ['name' => 'Product', 'slug' => 'product'],
            ['name' => 'Inventory', 'slug' => 'inventory'],
            ['name' => 'Purchase', 'slug' => 'purchase'],
            ['name' => 'Sales', 'slug' => 'sales'],
            ['name' => 'Point of Sales', 'slug' => 'pos'],
            ['name' => 'Reports', 'slug' => 'reports'],
            ['name' => 'Settings', 'slug' => 'settings'],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }
}