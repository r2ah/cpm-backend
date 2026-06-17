<?php

namespace App\Http\Controllers;

use App\Models\Authority;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAuthorityRequest;
use App\Http\Requests\PostAuthorityRequest;
use App\Http\Resources\AuthorityResource;

use Illuminate\Validation\ValidationException;

class AuthorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse
    {
        // Pagination + simple filtering
        $query = Authority::query();

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
            'data' => AuthorityResource::collection($items)
        ], 200);
    }

    public function store(StoreAuthorityRequest $request) : JsonResponse
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
            'data' => new AuthorityResource(Authority::create($validated))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Authority $authority) : JsonResponse
    {
	    return response()->json([
            'success' => true,
            'data' => new AuthorityResource($authority)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostAuthorityRequest $request, Authority $authority) : JsonResponse
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

	    $authority->update($validated);

	    return response()->json([
            'success' => true,
            'data' => new AuthorityResource($authority)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Authority $authority) : JsonResponse
    {
	    $authority->delete();

	    return response()->json([
            'success' => true
        ], 200);
    }
}
