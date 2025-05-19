<?php

namespace App\Services;

use App\Models\Operation;

class OperationService
{
    public function getAllOperations()
    {
        return Operation::all();
    }

    public function getOperationById($id)
    {
        return Operation::findOrFail($id);
    }

    public function createOperation(array $data)
    {
        // Check if there's a soft-deleted operation with the same name
        $existingOperation = Operation::withTrashed()
            ->where('name', $data['name'])
            ->where('slug', $data['slug'])
            ->first();

        if ($existingOperation) {
            if ($existingOperation->trashed()) {
                // If found and trashed, restore
                $existingOperation->restore();
                return $existingOperation;
            } else {
                return $existingOperation;
            }
        }

        return Operation::create($data);
    }

    public function updateOperation(Operation $operation, array $data)
    {
        return $operation->update($data);
    }

    public function deleteOperation(Operation $operation)
    {
        return $operation->delete();
    }
}
