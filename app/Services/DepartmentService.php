<?php

namespace App\Services;

use App\Models\Department;

class DepartmentService
{
    /**
     * Get all departments.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllDepartments()
    {
        return Department::all();
    }

    /**
     * Create a new department.
     *
     * @param array $data
     * @return \App\Models\Department
     */
    public function createDepartment(array $data)
    {
        return Department::create($data);
    }

    /**
     * Update an existing department.
     *
     * @param \App\Models\Department $department
     * @param array $data
     * @return bool
     */
    public function updateDepartment(Department $department, array $data)
    {
        return $department->update($data);
    }

    /**
     * Delete a department.
     *
     * @param \App\Models\Department $department
     * @return bool|null
     */
    public function deleteDepartment(Department $department)
    {
        return $department->delete();
    }
}