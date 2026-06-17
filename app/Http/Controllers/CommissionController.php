<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommissionRequest;
use App\Http\Requests\UpdateCommissionRequest;
use App\Models\Commission;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCommissionRequest;
use App\Http\Requests\UpdateCommissionRequest;
use App\Http\Resources\CommissionResource;

use Illuminate\Validation\ValidationException;

class CommissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Pagination + simple filtering
        $query = Commission::query();

        if ($search = $request->query('search')) {
            $query->where(
                fn($q) =>
                $q->where('name', 'like', "%{$search}%")
            );
        }

        if ($request->query('all'))
            $items = $query->latest()->all();
        else
            $items = $query->latest()->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => CommissionResource::collection($items)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommissionRequest $request) : JsonResponse
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
            'data' => new CommissionResource(Commission::create($validated))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Commission $commission) : JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new CommissionResource($commission)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommissionRequest $request, Commission $commission) : JsonResponse
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

        $commission->update($validated);

        return response()->json([
            'success' => true,
            'data' => new CommissionResource($commission)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commission $commission) : JsonResponse
    {
        $commission->delete();

        return response()->json([
            'success' => true
        ], 200);
    }
}
