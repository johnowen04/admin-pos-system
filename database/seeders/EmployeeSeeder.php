<?php

namespace Database\Seeders;

use App\Models\Employee;
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
        // Seed employees
        $employees = [
            [
                'nip' => 'EMP001',
                'name' => 'John Doe',
                'phone' => '08123456789',
                'email' => 'john.doe@example.com',
                'roles_id' => 3, // Superuser
                'password' => 'password123', // Plain password for seeding
                'outlets' => [1, 2], // Assign Outlet 1 and Outlet 2
            ],
            [
                'nip' => 'EMP002',
                'name' => 'Jane Smith',
                'phone' => '08198765432',
                'email' => 'jane.smith@example.com',
                'roles_id' => 1, // Admin
                'password' => 'password123', // Plain password for seeding
                'outlets' => [1, 2], // Assign Outlet 1 and Outlet 2
            ],
            [
                'nip' => 'EMP003',
                'name' => 'Alice Johnson',
                'phone' => '08122334455',
                'email' => 'alice.johnson@example.com',
                'roles_id' => 2, // Cashier
                'password' => 'password123', // Plain password for seeding
                'outlets' => [1], // Assign only Outlet 1
            ],
            [
                'nip' => 'EMP004',
                'name' => 'Andrea Smith',
                'phone' => '08122334455',
                'email' => 'andrea.smith@example.com',
                'roles_id' => 2, // Cashier
                'password' => 'password123', // Plain password for seeding
                'outlets' => [2], // Assign only Outlet 2
            ],
        ];

        foreach ($employees as $employeeData) {
            // Extract password and outlets from the data
            $password = $employeeData['password'];
            $outlets = $employeeData['outlets'];
            unset($employeeData['password'], $employeeData['outlets']);

            // Create the employee
            $employee = Employee::create($employeeData);

            // Create the associated user
            User::create([
                'name' => $employeeData['name'], // Use the employee's name for the user
                'email' => $employeeData['email'],
                'password' => Hash::make($password), // Hash the password
                'employee_id' => $employee->id, // Link the user to the employee
            ]);

            // Sync outlets
            $employee->outlets()->sync($outlets);
        }
    }
}