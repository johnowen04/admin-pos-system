<?php

namespace App\Services;

use App\Enums\UserRoleType;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    /**
     * Get all employees.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllEmployees($withTrashed = false, $withTrashedPosition = false)
    {
        $query = $withTrashed ? Employee::withTrashed() : Employee::query();

        $query->with([
            'position' => function ($query) use ($withTrashedPosition) {
                if ($withTrashedPosition) {
                    $query->withTrashed();
                }
            },
        ]);

        if (!$withTrashedPosition) {
            $query->whereHas('position');
        }

        return $query->get();
    }

    /**
     * Get all employees with a position level lower than or equal to the specified level.
     * 
     * @param int $positionLevel
     * @param bool $withTrashed
     * @param bool $withTrashedPosition
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllEmployeesWithLowerOrEqualPosition($positionLevel, $withTrashed = false, $withTrashedPosition = false)
    {
        $query = $withTrashed ? Employee::withTrashed() : Employee::query();

        $query->with([
            'position' => function ($query) use ($withTrashedPosition) {
                if ($withTrashedPosition) {
                    $query->withTrashed();
                }
            },
        ]);

        $query->whereHas('position', function ($query) use ($positionLevel) {
            $query->where('level', '<=', $positionLevel);
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
        $user = null;

        if (isset($data['password'])) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'role' => UserRoleType::EMPLOYEE->value,
            ]);
        }

        $employee = Employee::create([
            'nip' => $data['nip'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'position_id' => $data['position_id'],
            'user_id' => $user ? $user->id : null,
        ]);

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
        $employee->update([
            'nip' => $data['nip'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'position_id' => $data['position_id'],
        ]);

        if ($employee->user) {
            $employee->user()->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => isset($data['password']) ? Hash::make($data['password']) : $employee->user->password,
            ]);
        }

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
        $employee->user()->delete();
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
