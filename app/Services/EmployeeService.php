<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    /**
     * Get all employees.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllEmployees($withTrashed = false, $withTrashedRole = false)
    {
        $query = $withTrashed ? Employee::withTrashed() : Employee::query();

        // Load relationships, considering their trashed status
        $query->with([
            'role' => function ($query) use ($withTrashedRole) {
                if ($withTrashedRole) {
                    $query->withTrashed();
                }
            },
        ]);

        // Only return permissions with existing relationships
        if (!$withTrashedRole) {
            $query->whereHas('role');
        }

        return $query->get();
    }

    public function getAllEmployeesWithLowerOrEqualRole($roleLevel, $withTrashed = false, $withTrashedRole = false)
    {
        $query = $withTrashed ? Employee::withTrashed() : Employee::query();

        // Load relationships, considering their trashed status
        $query->with([
            'role' => function ($query) use ($withTrashedRole) {
                if ($withTrashedRole) {
                    $query->withTrashed();
                }
            },
        ]);

        // Only return employees with existing relationships
        $query->whereHas('role', function ($query) use ($roleLevel) {
            $query->where('level', '<=', $roleLevel);
        });

        return $query->get();
    }

    /**
     * Create a new employee and its associated user.
     *
     * @param array $data
     * @return \App\Models\Employee
     */
    public function createEmployee(array $data)
    {
        // Create the employee
        $employee = Employee::create([
            'nip' => $data['nip'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'roles_id' => $data['roles_id'],
        ]);

        // Create the associated user
        $employee->user()->create([
            'name' => $data['name'], // Use the employee's name for the user
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Hash the password
            'employee_id' => $employee->id, // Link the user to the employee
        ]);

        // Sync outlets
        if (!empty($data['outlets'])) {
            $employee->outlets()->sync($data['outlets']);
        }

        return $employee;
    }

    /**
     * Update an existing employee and its associated user.
     *
     * @param \App\Models\Employee $employee
     * @param array $data
     * @return bool
     */
    public function updateEmployee(Employee $employee, array $data)
    {
        // Update the employee
        $employee->update([
            'nip' => $data['nip'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'roles_id' => $data['roles_id'],
        ]);

        // Update the associated user
        $employee->user()->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) ? Hash::make($data['password']) : $employee->user->password,
        ]);

        // Sync outlets
        if (!empty($data['outlets'])) {
            $employee->outlets()->sync($data['outlets']);
        }

        return true;
    }

    /**
     * Delete an employee and its associated user.
     *
     * @param \App\Models\Employee $employee
     * @return bool|null
     */
    public function deleteEmployee(Employee $employee)
    {
        // Delete the associated user
        $employee->user()->delete();

        // Delete the employee
        return $employee->delete();
    }
    
    /**
     * Get the selected outlets for an employee.
     *
     * @param \App\Models\Employee $employee
     * @return array
     */
    public function getSelectedOutlets(Employee $employee)
    {
        return $employee->outlets->pluck('id')->toArray();
    }
}
