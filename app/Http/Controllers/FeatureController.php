<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Services\FeatureService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeatureController extends Controller
{
    /**
     * Constructor to inject the FeatureService.
     */
    public function __construct(protected FeatureService $featureService)
    {
        $this->middleware('permission:feature.view|feature.*')->only(['index', 'show']);
        $this->middleware('permission:feature.create|feature.*')->only(['create', 'store']);
        $this->middleware('permission:feature.edit|feature.*')->only(['edit', 'update']);
        $this->middleware('permission:feature.delete|feature.*')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = $this->featureService->getAllFeatures();
        return view('feature.index', [
            'features' => $features,
            'createRoute' => route('feature.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('feature.create', [
            'action' => route('feature.store'),
            'method' => 'POST',
            'feature' => null,
            'cancelRoute' => route('feature.index'),
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
                Rule::unique('features')->withoutTrashed()
            ],
            'slug' => [
                'required',
                'string',
                'max:20',
                Rule::unique('features')->withoutTrashed()
            ],
        ]);

        $this->featureService->createFeature($validatedData);
        return redirect()->route('feature.index')->with('success', 'Feature created successfully.');
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
    public function edit(Feature $feature)
    {
        return view('feature.edit', [
            'action' => route('feature.update', $feature->id),
            'method' => 'PUT',
            'feature' => $feature,
            'cancelRoute' => route('feature.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feature $feature)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('features')->ignore($feature->id)->withoutTrashed()
            ],
            'slug' => [
                'required',
                'string',
                'max:20',
                Rule::unique('features')->ignore($feature->id)->withoutTrashed()
            ],
        ]);

        $this->featureService->updateFeature($feature, $validatedData);
        return redirect()->route('feature.index')->with('success', 'Feature updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feature $feature)
    {
        $this->featureService->deleteFeature($feature);
        return redirect()->route('feature.index')->with('success', 'Feature deleted successfully.');
    }
}
