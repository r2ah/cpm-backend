<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;
use App\Http\Requests\StoreInterventionRequest;
use App\Http\Requests\UpdateInterventionRequest;
use App\Http\Resources\InterventionResource;

use Illuminate\Validation\ValidationException;
class InterventionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
{
    $query = Intervention::with('parent');

    if ($search = $request->query('search')) {
        $query->where('name', 'like', "%{$search}%");
    }

    $items = $request->query('all')
        ? $query->latest()->get()
        : $query->latest()->paginate($request->integer('per_page', 10));

    return response()->json([
        'success' => true,
        'data' => InterventionResource::collection($items)
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    /**
 * Store a newly created resource in storage.
 */
public function store(StoreInterventionRequest $request): JsonResponse
{
    try {

        $validated = $request->validated();

    } catch (ValidationException $e) {

        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    }


    $intervention = Intervention::create($validated);


    // Cargar relación padre para devolver el nombre
    $intervention->load('parent');


    return response()->json([
        'success' => true,
        'data' => new InterventionResource($intervention)
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Intervention $intervention) : JsonResponse
{
    $intervention->load('parent');

    return response()->json([
        'success' => true,
        'data' => new InterventionResource($intervention)
    ], 200);        
}

    /**
     * Update the specified resource in storage.
     */
    /**
 * Update the specified resource in storage.
 */
public function update(
    UpdateInterventionRequest $request,
    Intervention $intervention
): JsonResponse
{
    try {

        $validated = $request->validated();

    } catch (ValidationException $e) {

        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    }


    // Actualizar datos
    $intervention->fill($validated);

    $intervention->save();


    // Recargar relación padre
    $intervention->refresh();

    $intervention->load('parent');


    return response()->json([
        'success' => true,
        'data' => new InterventionResource($intervention)
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
