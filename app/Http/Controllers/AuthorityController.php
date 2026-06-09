<?php

namespace App\Http\Controllers;

use App\Models\Authority;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\AuthorityRequest;
use App\Http\Resources\AuthorityResource;

class AuthorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AuthorityRequest $request) : JsonResponse
    {
        // Pagination + simple filtering
        $query = Authority::query();

        if ($search = $request->query('search')) {
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$search}%")
            );
        }

        $authorities = $query->latest()->paginate($request->integer('per_page', 10));

	    return response()->json([
            'success' => true,
            'data' => AuthorityResource::collection($authorities)
        ], 200);
    }

    public function store(AuthorityRequest $request) : JsonResponse
    { 
        $authority = Authority::create($request->validate());

	    return response()->json([
            'success' => true,
            'data' => new AuthorityResource($authority)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Authority $authority)
    {
	    return response()->json([
            'success' => true,
            'data' => new AuthorityResource($authority)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuthorityRequest $request, Authority $authority) : JsonResponse
    {
	    $authority->update($request->validate());

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
