<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create super user role if it doesn't exist
        $superUserRole = Role::firstOrCreate(
            ['name' => 'Super User'],
        );

        // More efficient way to attach all permissions at once
        $permissionIds = Permission::pluck('id')->toArray();
        $superUserRole->permissions()->sync($permissionIds);

        $this->command->info('Super User role created and assigned all permissions successfully.');
    }
}
