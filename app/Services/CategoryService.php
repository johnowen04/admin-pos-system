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
        // Create the category
        $category = Category::create([
            'name' => $data['name'],
            'departments_id' => $data['departments_id'],
            'is_shown' => $data['is_shown'],
        ]);

        // Attach outlets to the category (if any)
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
        // Update the category
        $category->update([
            'name' => $data['name'],
            'departments_id' => $data['departments_id'],
            'is_shown' => $data['is_shown'],
        ]);

        // Sync outlets
        if (!empty($data['outlets'])) {
            $category->outlets()->sync($data['outlets']);
        }

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