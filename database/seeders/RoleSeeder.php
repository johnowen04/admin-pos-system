<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super User',
                'level' => 100,
            ],
            [
                'name' => 'Admin',
                'level' => 80,
            ],
            [
                'name' => 'Cashier',
                'level' => 40,
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
