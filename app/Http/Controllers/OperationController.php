<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Services\OperationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OperationController extends Controller
{
    protected $operationService;

    /**
     * Constructor to inject the OperationService.
     */
    public function __construct(OperationService $operationService)
    {
        $this->middleware('permission:operation.view|operation.*')->only(['index', 'show']);
        $this->middleware('permission:operation.create|operation.*')->only(['create', 'store']);
        $this->middleware('permission:operation.edit|operation.*')->only(['edit', 'update']);
        $this->middleware('permission:operation.delete|operation.*')->only(['destroy']);

        $this->operationService = $operationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $operations = $this->operationService->getAllOperations();
        return view('operation.index', [
            'operations' => $operations,
            'createRoute' => route('operation.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('operation.create', [
            'action' => route('operation.store'),
            'method' => 'POST',
            'operation' => null,
            'cancelRoute' => route('operation.index'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('operations')->withoutTrashed() // Only check non-trashed records
            ],
            'slug' => [
                'required',
                'string',
                'max:20',
                Rule::unique('operations')->withoutTrashed() // Only check non-trashed records
            ],
        ]);

        // Use the service to create the operation
        $this->operationService->createOperation($validatedData);

        // Redirect back to the operation index with a success message
        return redirect()->route('operation.index')->with('success', 'Operation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Operation $operation)
    {
        return view('operation.edit', [
            'action' => route('operation.update', $operation->id),
            'method' => 'PUT',
            'operation' => $operation,
            'cancelRoute' => route('operation.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Operation $operation)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('operations')->ignore($operation->id)->withoutTrashed()
            ],
            'slug' => [
                'required',
                'string',
                'max:20',
                Rule::unique('operations')->ignore($operation->id)->withoutTrashed()
            ],
        ]);

        // Use the service to update the operation
        $this->operationService->updateOperation($operation, $validatedData);

        // Redirect back to the operation index with a success message
        return redirect()->route('operation.index')->with('success', 'Operation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operation $operation)
    {
        // Use the service to delete the operation
        $this->operationService->deleteOperation($operation);

        // Redirect back to the operation index with a success message
        return redirect()->route('operation.index')->with('success', 'Operation deleted successfully.');
    }
}
