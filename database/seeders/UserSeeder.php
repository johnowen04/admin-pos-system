<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $superuserRole = Role::where('name', 'Super User')->first();

        if (!$superuserRole) {
            $this->command->error('Roles are not seeded yet! Please run RoleSeeder first.');
            return;
        }

        // Create superuser
        User::create([
            'name' => 'Super User',
            'email' => 'superuser@example.com',
            'username' => 'superuser',
            'password' => Hash::make('password'),
            'role_id' => $superuserRole->id,
        ]);

        $this->command->info('Users seeded successfully: 1 superuser, 1 employee');
    }
}
