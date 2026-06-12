<?php

namespace App\Http\Controllers;

use App\Models\Opinion;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;
use App\Http\Requests\StoreOpinionRequest;
use App\Http\Requests\PostOpinionRequest;
use App\Http\Resources\OpinionResource;

use Illuminate\Validation\ValidationException;

class OpinionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse
    {
        // Pagination + simple filtering
        $query = Opinion::query();

        if($request->query('all')) 
            $items = $query->latest()->all();            
        else 
            $items = $query->latest()->paginate($request->integer('per_page', 10));

	    return response()->json([
            'success' => true,
            'data' => OpinionResource::collection($items)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOpinionRequest $request) : JsonResponse
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
            'data' => new OpinionResource(Opinion::create($validated))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Opinion $opinion) : JsonResponse
    {
	    return response()->json([
            'success' => true,
            'data' => new OpinionResource($opinion)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostOpinionRequest $request, Opinion $opinion) : JsonResponse
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

	    $opinion->update($validated);

	    return response()->json([
            'success' => true,
            'data' => new OpinionResource($opinion)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Opinion $opinion) : JsonResponse
    {
        $opinion->delete();

	    return response()->json([
            'success' => true
        ], 200);
    }
}
