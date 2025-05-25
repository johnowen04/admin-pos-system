<?php

namespace App\Services;

use App\Models\Operation;

class OperationService
{
    /**
     * Get all operations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllOperations()
    {
        return Operation::all();
    }

    /**
     * Get operation by id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getOperationById($id)
    {
        return Operation::findOrFail($id);
    }

    
    /**
     * Create a new operation or restore an existing one.
     * 
     * @param array $data
     * @return \App\Models\Operation
     */
    public function createOperation(array $data)
    {
        $existingOperation = Operation::withTrashed()
            ->where('name', $data['name'])
            ->where('slug', $data['slug'])
            ->first();

        if ($existingOperation) {
            if ($existingOperation->trashed()) {
                $existingOperation->restore();
                return $existingOperation;
            } else {
                return $existingOperation;
            }
        }

        return Operation::create($data);
    }

    /**
     * Update an existing operation.
     *
     * @param \App\Models\Operation $operation
     * @param array $data
     * @return bool
     */
    public function updateOperation(Operation $operation, array $data)
    {
        return $operation->update($data);
    }

    /**
     * Delete an operation.
     *
     * @param \App\Models\Operation $operation
     * @return bool
     */
    public function deleteOperation(Operation $operation)
    {
        return $operation->delete();
    }
}
