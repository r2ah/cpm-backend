<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\PersonRequest;

use Illuminate\Http\Request;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Http\Resources\PersonResource;

use Illuminate\Validation\ValidationException;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse
    {
       // Pagination + simple filtering
        $query = Person::query();

        if ($search = $request->query('search')) {
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$search}%")
            );
        }

        if($request->query('all')) 
            $items = $query->latest()->get();           
        else 
            $items = $query->latest()->paginate($request->integer('per_page', 10));

	    return response()->json([
            'success' => true,
            'data' => PersonResource::collection($items)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonRequest $request): JsonResponse
{
    $validated = $request->validated();

    $validated['is_natural_person'] = $request->boolean('is_natural_person');

    $person = Person::create($validated);

    return response()->json([
        'success' => true,
        'data' => new PersonResource($person)
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Person $person) : JsonResponse
    {
	    return response()->json([
            'success' => true,
            'data' => new PersonResource($person)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonRequest $request, Person $person): JsonResponse
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

    // 🔥 FIX CRÍTICO
   $isNatural = $request->boolean('is_natural_person');

$person->update([
    ...$validated,
    'is_natural_person' => $isNatural,
]);

    return response()->json([
        'success' => true,
        'data' => new PersonResource($person)
    ], 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person) : JsonResponse
    {
        $person->delete();

	    return response()->json([
            'success' => true
        ], 200);
    }
}
