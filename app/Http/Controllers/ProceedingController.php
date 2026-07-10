<?php

namespace App\Http\Controllers;

use App\Models\Proceeding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\StoreProceedingRequest;
use App\Http\Requests\UpdateProceedingRequest;
use App\Http\Resources\ProceedingResource;

use Illuminate\Validation\ValidationException;

class ProceedingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Proceeding::with('participants');


        if ($request->query('all')) {

            $items = $query
                ->latest()
                ->get();

        } else {

            $items = $query
                ->latest()
                ->paginate(
                    $request->integer('per_page', 10)
                );
        }


        return response()->json([
            'success' => true,
            'data' => ProceedingResource::collection($items)
        ], 200);
    }


    /**
     * Store a newly created resource.
     */
    public function store(StoreProceedingRequest $request): JsonResponse
{
    $validated = $request->validated();

    $proceeding = Proceeding::create($validated);


    if ($request->filled('participants')) {

        $proceeding->participants()
            ->attach($request->participants);

    }


    $proceeding->load('participants');


    return response()->json([
        'success' => true,
        'data' => $proceeding
    ], 201);
}



    /**
     * Display the specified resource.
     */
    public function show(Proceeding $proceeding): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ProceedingResource(
                $proceeding->load('participants')
            )
        ], 200);
    }



    /**
     * Update the specified resource.
     */
    public function update(
        UpdateProceedingRequest $request,
        Proceeding $proceeding
    ): JsonResponse
    {

        try {

            $validated = $request->validated();

        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ],422);
        }



        /*
        |--------------------------------------------------------------------------
        | Actualizar datos del acta
        |--------------------------------------------------------------------------
        */

        $proceeding->update($validated);



        /*
        |--------------------------------------------------------------------------
        | Actualizar participantes
        |--------------------------------------------------------------------------
        */

        if ($request->has('participants')) {

            $proceeding
                ->participants()
                ->sync(
                    $request->participants
                );
        }



        return response()->json([
            'success' => true,
            'data' => new ProceedingResource(
                $proceeding->load('participants')
            )
        ],200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) : JsonResponse
{
    $proceeding = Proceeding::find($id);


    if (!$proceeding) {
        return response()->json([
            'success' => false,
            'message' => 'Acta no encontrada'
        ], 404);
    }


    // eliminar relaciones con usuarios
    $proceeding->participants()->detach();


    // eliminar acta
    $proceeding->delete();


    return response()->json([
        'success' => true,
        'message' => 'Acta eliminada correctamente'
    ], 200);
}
}