<?php

namespace App\Services;

use App\Models\Feature;

class FeatureService
{
    /**
     * Get all features.
     *
     * @param bool $withTrashed
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFeatures($withTrashed = false)
    {
        return $withTrashed ? Feature::withTrashed()->get() : Feature::all();
    }

    /**
     * Get feature by id.
     *
     * @param int $id
     * @return \App\Models\Feature
     */
    public function getFeatureById($id)
    {
        return Feature::findOrFail($id);
    }

    /**
     * Create a new feature or restore an existing one.
     *
     * @param array $data
     * @return \App\Models\Feature
     */
    public function createFeature(array $data)
    {
        $existingFeature = Feature::withTrashed()
            ->where('name', $data['name'])
            ->where('slug', $data['slug'])
            ->first();

        if ($existingFeature) {
            if ($existingFeature->trashed()) {
                $existingFeature->restore();
                return $existingFeature;
            } else {
                return $existingFeature;
            }
        }

        return Feature::create($data);
    }

    /**
     * Update an existing feature.
     *
     * @param \App\Models\Feature $feature
     * @param array $data
     * @return bool
     */
    public function updateFeature(Feature $feature, array $data)
    {
        return $feature->update($data);
    }

    /**
     * Delete a feature.
     *
     * @param \App\Models\Feature $feature
     * @return bool|null
     */
    public function deleteFeature(Feature $feature)
    {
        return $feature->delete();
    }
}
