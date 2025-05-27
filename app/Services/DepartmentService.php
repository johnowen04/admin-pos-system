<?php

namespace App\Services;

use App\Models\Category;
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
        $categories = isset($data['categories']) ? $data['categories'] : [];

        if (isset($data['categories'])) {
            unset($data['categories']);
        }

        $department->update($data);

        if (method_exists($department, 'categories')) {
            $department->categories()
                ->whereNotIn('id', $categories)
                ->update(['department_id' => null]);

            Category::whereIn('id', $categories)
                ->update(['department_id' => $department->id]);
        }
        return true;
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
