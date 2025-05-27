<?php

namespace Database\Seeders;

use App\Services\CategoryService;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryService = new CategoryService();

        $categories = [
            [
                'name' => 'Ice Cream Aice',
                'department_id' => 1,
                'is_shown' => true,
                'outlets' => [1, 2],
            ],
            [
                'name' => 'Ice Cream Walls',
                'department_id' => 1,
                'is_shown' => true,
                'outlets' => [1, 2],
            ],
            [
                'name' => 'Ice Cream Glico',
                'department_id' => 1,
                'is_shown' => true,
                'outlets' => [1, 2],
            ],
            [
                'name' => 'Lapangan Futsal',
                'department_id' => 2,
                'is_shown' => true,
                'outlets' => [1],
            ],
            [
                'name' => 'Voucher Futsal',
                'department_id' => 2,
                'is_shown' => true,
                'outlets' => [1],
            ],
            [
                'name' => 'Lain lain',
                'department_id' => 2,
                'is_shown' => true,
                'outlets' => [1, 2],
            ],
            [
                'name' => 'Minuman',
                'department_id' => 3,
                'is_shown' => true,
                'outlets' => [1, 2],
            ],
            [
                'name' => 'Perlengkapan Futsal',
                'department_id' => 3,
                'is_shown' => true,
                'outlets' => [1],
            ],
            [
                'name' => 'Perlengkapan Kolam',
                'department_id' => 3,
                'is_shown' => true,
                'outlets' => [2],
            ],
            [
                'name' => 'Rokok',
                'department_id' => 2,
                'is_shown' => true,
                'outlets' => [1, 2],
            ],
            [
                'name' => 'Snack',
                'department_id' => 3,
                'is_shown' => true,
                'outlets' => [1, 2],
            ],
            [
                'name' => 'Toiletries',
                'department_id' => 3,
                'is_shown' => true,
                'outlets' => [1, 2],
            ],
        ];

        foreach ($categories as $categoryData) {
            $categoryService->createCategory($categoryData);
        }
    }
}