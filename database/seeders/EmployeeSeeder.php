<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superUserRole = Role::where('name', 'Super User')->first();
        $employeeRole = Role::where('name', 'Employee')->first();

        $employees = [
            [
                'nip' => 'EMP001',
                'name' => 'Admin User',
                'phone' => '08123456789',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => 'password123',
                'role_id' => $employeeRole->id,
                'position_id' => 2,
                'outlets' => [1, 2],
            ],
            [
                'nip' => 'EMP002',
                'name' => 'Cashier One',
                'phone' => '08122334455',
                'email' => 'cashier1@example.com',
                'username' => 'cashier1',
                'password' => 'password123',
                'role_id' => $employeeRole->id,
                'position_id' => 3,
                'outlets' => [1],
            ],
            [
                'nip' => 'EMP003',
                'name' => 'Cashier Two',
                'phone' => '08122334466',
                'email' => 'cashier2@example.com',
                'username' => 'cashier2',
                'password' => 'password123',
                'role_id' => $employeeRole->id,
                'position_id' => 3,
                'outlets' => [2],
            ],
        ];

        foreach ($employees as $employeeData) {
            $password = $employeeData['password'];
            $roleId = $employeeData['role_id'];
            $outlets = $employeeData['outlets'];

            $userId = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'username' => $employeeData['username'],
                'password' => Hash::make($password),
                'role_id' => $roleId,
            ])->id;

            unset(
                $employeeData['outlets'],
                $employeeData['password'],
                $employeeData['role_id'],
                $employeeData['username']
            );

            $employeeData['user_id'] = $userId;
            $employee = Employee::create($employeeData);

            $employee->outlets()->sync($outlets);
        }

        $this->command->info('3 employees created successfully: 1 admin and 2 cashiers');
    }
}
