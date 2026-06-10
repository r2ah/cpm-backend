<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;
use App\Http\Requests\InterventionStoreRequest;
use App\Http\Requests\InterventionPutRequest;
use App\Http\Resources\InterventionResource;

use Illuminate\Validation\ValidationException;
class InterventionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse
    {
        // Pagination + simple filtering
        $query = Intervention::query();

        if ($search = $request->query('search')) {
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$search}%")
            );
        }

        if($request->query('all')) 
            $items = $query->latest()->all();            
        else 
            $items = $query->latest()->paginate($request->integer('per_page', 10));

	    return response()->json([
            'success' => true,
            'data' => InterventionResource::collection($items)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InterventionStoreRequest $request) : JsonResponse
    {
        try {
            $validated = $request->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

	    return response()->json([
            'success' => true,
            'data' =>  new InterventionResource(Intervention::create($validated))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Intervention $intervention) : JsonResponse
    {
	    return response()->json([
            'success' => true,
            'data' =>  new InterventionResource($intervention)
        ], 200);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InterventionPutRequest $request, Intervention $intervention) : JsonResponse
    {
        try {
            $validated = $request->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $intervention->update($validated);

        return response()->json([
            'success' => true,
            'data' =>  new InterventionResource($intervention)
        ], 200); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Intervention $intervention) : JsonResponse
    {
        $intervention->delete();

	    return response()->json([
            'success' => true
        ], 200);
    }
}
