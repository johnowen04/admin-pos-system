<?php

namespace App\Services;

use App\Models\Feature;

class FeatureService
{
    public function getAllFeatures($withTrashed = false)
    {
        return $withTrashed ? Feature::withTrashed()->get() : Feature::all();
    }

    public function getFeatureById($id)
    {
        return Feature::findOrFail($id);
    }

    public function createFeature(array $data)
    {
        // Check if there's a soft-deleted feature with the same name and slug
        $existingFeature = Feature::withTrashed()
            ->where('name', $data['name'])
            ->where('slug', $data['slug'])
            ->first();

        if ($existingFeature) {
            if ($existingFeature->trashed()) {
                // If found and trashed, restore
                $existingFeature->restore();
                return $existingFeature;
            } else {
                return $existingFeature;
            }
        }

        return Feature::create($data);
    }

    public function updateFeature(Feature $feature, array $data)
    {
        return $feature->update($data);
    }

    public function deleteFeature(Feature $feature)
    {
        return $feature->delete();
    }
}
