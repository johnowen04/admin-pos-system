<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Services\OperationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OperationController extends Controller
{
    /**
     * Constructor to inject the OperationService.
     */
    public function __construct(protected OperationService $operationService)
    {
        $this->middleware('permission:operation.view|operation.*')->only(['index', 'show']);
        $this->middleware('permission:operation.create|operation.*')->only(['create', 'store']);
        $this->middleware('permission:operation.edit|operation.*')->only(['edit', 'update']);
        $this->middleware('permission:operation.delete|operation.*')->only(['destroy']);
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
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('operations')->withoutTrashed()
            ],
            'slug' => [
                'required',
                'string',
                'max:20',
                Rule::unique('operations')->withoutTrashed()
            ],
        ]);

        $this->operationService->createOperation($validatedData);
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
        
        $this->operationService->updateOperation($operation, $validatedData);
        return redirect()->route('operation.index')->with('success', 'Operation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operation $operation)
    {
        $this->operationService->deleteOperation($operation);
        return redirect()->route('operation.index')->with('success', 'Operation deleted successfully.');
    }
}
