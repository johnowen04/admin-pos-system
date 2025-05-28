<?php

namespace Database\Seeders;

use App\Enums\UserRoleType;
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
        // Create superuser
        User::create([
            'name' => 'Super User',
            'email' => 'superuser@example.com',
            'username' => 'superuser',
            'password' => Hash::make('password'),
            'role' => UserRoleType::SUPERUSER->value,
        ]);

        $this->command->info('Users seeded successfully: 1 superuser');
    }
}
