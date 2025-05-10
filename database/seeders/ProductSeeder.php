<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            [
                'sku' => 'PROD001',
                'name' => 'Product A',
                'description' => 'Description for Product A',
                'base_price' => 100.00,
                'buy_price' => 80.00,
                'sell_price' => 120.00,
                'min_qty' => 10,
                'units_id' => 1, // Assuming Unit ID 1 (e.g., Pieces) exists
                'categories_id' => 1, // Assuming Category ID 1 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD002',
                'name' => 'Product B',
                'description' => 'Description for Product B',
                'base_price' => 200.00,
                'buy_price' => 150.00,
                'sell_price' => 250.00,
                'min_qty' => 5,
                'units_id' => 2, // Assuming Unit ID 2 (e.g., Dozen) exists
                'categories_id' => 2, // Assuming Category ID 2 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD003',
                'name' => 'Product C',
                'description' => 'Description for Product C',
                'base_price' => 300.00,
                'buy_price' => 250.00,
                'sell_price' => 350.00,
                'min_qty' => 20,
                'units_id' => 3, // Assuming Unit ID 3 (e.g., Box) exists
                'categories_id' => 3, // Assuming Category ID 3 exists
                'is_shown' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD004',
                'name' => 'Product D',
                'description' => 'Description for Product D',
                'base_price' => 50.00,
                'buy_price' => 40.00,
                'sell_price' => 60.00,
                'min_qty' => 50,
                'units_id' => 4, // Assuming Unit ID 4 (e.g., Kilogram) exists
                'categories_id' => 4, // Assuming Category ID 4 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD005',
                'name' => 'Product E',
                'description' => 'Description for Product E',
                'base_price' => 10.00,
                'buy_price' => 8.00,
                'sell_price' => 12.00,
                'min_qty' => 100,
                'units_id' => 5, // Assuming Unit ID 5 (e.g., Gram) exists
                'categories_id' => 5, // Assuming Category ID 5 exists
                'is_shown' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}