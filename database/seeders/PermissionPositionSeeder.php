<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PermissionPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create admin position if it doesn't exist
        $adminPosition = Position::firstOrCreate(
            ['name' => 'Admin'],
        );

        // More efficient way to attach all permissions at once
        $permissionIds = Permission::where('is_super_user_only', 0)->pluck('id')->toArray();
        $adminPosition->permissions()->sync($permissionIds);

        $this->command->info('Admin position created and assigned all permissions successfully.');
    }
}
