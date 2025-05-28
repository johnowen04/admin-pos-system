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
            ['name' => 'Feature', 'slug' => 'feature'],
            ['name' => 'Operation', 'slug' => 'operation'],
            ['name' => 'Permission', 'slug' => 'permission'],
            ['name' => 'ACL', 'slug' => 'acl'],
            ['name' => 'Dashboard', 'slug' => 'dashboard'],
            ['name' => 'Outlet', 'slug' => 'outlet'],
            ['name' => 'Position', 'slug' => 'position'],
            ['name' => 'Employee', 'slug' => 'employee'],
            ['name' => 'Base Unit', 'slug' => 'bu'],
            ['name' => 'Unit', 'slug' => 'unit'],
            ['name' => 'Department', 'slug' => 'department'],
            ['name' => 'Category', 'slug' => 'category'],
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