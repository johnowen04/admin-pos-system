<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Get all categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCategories()
    {
        return Category::all();
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return \App\Models\Category
     */
    public function createCategory(array $data)
    {
        $category = Category::create([
            'name' => $data['name'],
            'department_id' => $data['department_id'],
            'is_shown' => $data['is_shown'],
        ]);

        if (!empty($data['outlets'])) {
            $category->outlets()->sync($data['outlets']);
        }

        return $category;
    }

    /**
     * Update an existing category.
     *
     * @param \App\Models\Category $category
     * @param array $data
     * @return bool
     */
    public function updateCategory(Category $category, array $data)
    {
        $category->update([
            'name' => $data['name'],
            'department_id' => $data['department_id'],
            'is_shown' => $data['is_shown'],
        ]);
        
        $category->outlets()->sync($data['outlets'] ?? []);

        return true;
    }

    /**
     * Delete a category.
     *
     * @param \App\Models\Category $category
     * @return bool|null
     */
    public function deleteCategory(Category $category)
    {
        return $category->delete();
    }

    /**
     * Get the selected outlets for a category.
     *
     * @param \App\Models\Category $category
     * @return array
     */
    public function getSelectedOutlets(Category $category)
    {
        return $category->outlets->pluck('id')->toArray();
    }
}
