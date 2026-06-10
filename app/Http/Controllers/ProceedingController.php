<?php

namespace App\Http\Controllers;

use App\Models\Proceeding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\ProceedingStoreRequest;
use App\Http\Requests\ProceedingPutRequest;
use App\Http\Resources\ProceedingResource;

use Illuminate\Validation\ValidationException;

class ProceedingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse
    {
        // Pagination + simple filtering
        $query = Proceeding::query();

        if($request->query('all')) 
            $items = $query->latest()->all();            
        else 
            $items = $query->latest()->paginate($request->integer('per_page', 10));

	    return response()->json([
            'success' => true,
            'data' => ProceedingResource::collection($items)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProceedingStoreRequest $request) : JsonResponse
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
            'data' => new ProceedingResource(Proceeding::create($validated))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Proceeding $proceeding) : JsonResponse
    {
	    return response()->json([
            'success' => true,
            'data' => new ProceedingResource($proceeding)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProceedingPutRequest $request, Proceeding $proceeding) : JsonResponse
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

	    $proceeding->update($validated);

	    return response()->json([
            'success' => true,
            'data' => new ProceedingResource($proceeding)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proceeding $proceeding) : JsonResponse
    {
        $proceeding->delete();

	    return response()->json([
            'success' => true
        ], 200);
    }
}
