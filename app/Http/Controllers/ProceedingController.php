<?php

namespace App\Http\Controllers;

use App\Models\Proceeding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProceedingRequest;
use App\Http\Requests\UpdateProceedingRequest;
use App\Http\Resources\ProceedingResource;

use Illuminate\Validation\ValidationException;
use App\Models\User;
class ProceedingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Proceeding::with([
    'participants',
    'commission',
    'elaboradoPor'
]);


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

   $elaborador = User::with('commissions')
    ->find($validated['elaborado_por']);


if (!$elaborador) {
    return response()->json([
        'success' => false,
        'message' => 'Usuario elaborador no encontrado.'
    ], 422);
}


$commission = $elaborador->commissions->first();


if (!$commission) {
    return response()->json([
        'success' => false,
        'message' => 'El usuario seleccionado no pertenece a ninguna comisión.'
    ], 422);
}




    $validated['commission_id'] = $commission->id;

    $proceeding = Proceeding::create($validated);

    if (!empty($validated['participants'])) {
        $proceeding->participants()->attach($validated['participants']);
    }

    return response()->json([
        'success' => true,
        'data' => new ProceedingResource(
            $proceeding->load(
    'participants',
    'commission',
    'elaboradoPor'
)
        )
    ], 201);
}



    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
{
    $proceeding = Proceeding::with([
    'participants',
    'elaboradoPor',
    'signedDocument'
])
->find($id);


    if (!$proceeding) {

        return response()->json([
            'success'=>false,
            'message'=>'Acta no encontrada',
            'id recibido'=>$id
        ],404);

    }


    return response()->json([
        'success'=>true,
        'data'=>new ProceedingResource($proceeding)
    ]);
}



    /**
     * Update the specified resource.
     */
   public function update(
    UpdateProceedingRequest $request,
    $id
): JsonResponse {

    $proceeding = Proceeding::find($id);

    if (!$proceeding) {
        return response()->json([
            'success' => false,
            'message' => 'Acta no encontrada'
        ], 404);
    }

    $validated = $request->validated();

    $elaborador = User::with('commissions')
    ->find($validated['elaborado_por']);


if (!$elaborador) {
    return response()->json([
        'success' => false,
        'message' => 'Usuario elaborador no encontrado.'
    ], 422);
}


$commission = $elaborador->commissions->first();


if (!$commission) {
    return response()->json([
        'success' => false,
        'message' => 'El usuario seleccionado no pertenece a ninguna comisión.'
    ], 422);
}


$validated['commission_id'] = $commission->id;

    $proceeding->update($validated);

    if ($request->has('participants')) {
        $proceeding->participants()->sync($request->participants ?? []);
    }

    return response()->json([
        'success' => true,
        'data' => new ProceedingResource(
            $proceeding->fresh()->load('participants', 'commission')
        )
    ]);
}




/**
 * Remove the specified resource.
 */
public function destroy($id): JsonResponse
{
    $proceeding = Proceeding::find($id);

    if (!$proceeding) {
        return response()->json([
            'success' => false,
            'message' => 'Acta no encontrada'
        ], 404);
    }

    // eliminar relaciones de la tabla pivote
    $proceeding->participants()->detach();

    // eliminar acta
    $proceeding->delete();

    return response()->json([
        'success' => true,
        'message' => 'Acta eliminada correctamente'
    ], 200);
}
}