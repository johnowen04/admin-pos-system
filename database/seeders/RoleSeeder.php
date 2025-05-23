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
            ],
            [
                'name' => 'Employee',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
