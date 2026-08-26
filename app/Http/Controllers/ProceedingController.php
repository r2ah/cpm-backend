<?php

namespace App\Http\Controllers;

use App\Models\Proceeding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProceedingRequest;
use App\Http\Requests\UpdateProceedingRequest;
use App\Http\Resources\ProceedingResource;

use App\Models\MediaFiles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
    'elaboradoPor',
    'documents'
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

    // =====================================================
    // RELACIONES
    // =====================================================

    $documents = array_map(
        'intval',
        $validated['documents'] ?? []
    );

    $participants = array_map(
        'intval',
        $validated['participants'] ?? []
    );

    $location = $validated['location'] ?? null;

    unset(
        $validated['documents'],
        $validated['participants'],
        $validated['location']
    );

    // =====================================================
    // VALIDAR ELABORADOR
    // =====================================================

    $elaborador = User::with('commissions')
        ->find($validated['elaborado_por']);

    if (!$elaborador) {
        return response()->json([
            'success' => false,
            'message' => 'Usuario elaborador no encontrado.'
        ], 422);
    }

    // =====================================================
    // OBTENER COMISIÓN DEL ELABORADOR
    // =====================================================

    $commission = $elaborador->commissions->first();

    if (!$commission) {
        return response()->json([
            'success' => false,
            'message' =>
                'El usuario seleccionado no pertenece a ninguna comisión.'
        ], 422);
    }

    $validated['commission_id'] = $commission->id;

    // =====================================================
    // CREAR ACTA
    // =====================================================

    $proceeding = Proceeding::create($validated);

    // =====================================================
    // GUARDAR LOCALIZACIÓN POSTGIS
    // =====================================================

    if ($location) {

        DB::statement(
            'UPDATE proceedings
             SET location = ST_SetSRID(
                 ST_MakePoint(?, ?),
                 4326
             )
             WHERE id = ?',
            [
                $location['longitude'],
                $location['latitude'],
                $proceeding->id
            ]
        );
    }

    // =====================================================
    // PARTICIPANTES
    // =====================================================

    $proceeding
        ->participants()
        ->sync($participants);

    // =====================================================
    // DOCUMENTOS
    // =====================================================

    $proceeding
        ->documents()
        ->sync($documents);

    // =====================================================
    // RESPUESTA
    // =====================================================

    $proceeding->load([
        'participants',
        'commission',
        'elaboradoPor',
        'documents'
    ]);

    return response()->json([
        'success' => true,
        'data' => new ProceedingResource(
            $proceeding
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
        'documents',
        'commission'
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

    /*
    |--------------------------------------------------------------------------
    | Validar datos
    |--------------------------------------------------------------------------
    */

    $validated = $request->validated();

\Log::info('DATOS UPDATE ACTA', [
    'request_all' => $request->all(),
    'validated' => $validated,
    'documents' => $validated['documents'] ?? null,
]);

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

   $documents = $validated['documents'] ?? [];
$participants = $validated['participants'] ?? [];

$documents = array_map('intval', $documents);
$participants = array_map('intval', $participants);

// Guardar temporalmente la ubicación
$location = $validated['location'] ?? null;

unset(
    $validated['documents'],
    $validated['participants'],
    $validated['location']
);

    /*
    |--------------------------------------------------------------------------
    | Validar usuario elaborador
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Documentos antiguos
    |--------------------------------------------------------------------------
    */

    $oldDocuments = $proceeding
        ->documents()
        ->pluck('media_files.id')
        ->map(fn ($id) => (int) $id)
        ->toArray();

    /*
    |--------------------------------------------------------------------------
    | Actualizar acta
    |--------------------------------------------------------------------------
    */

    $proceeding->update($validated);

    // =====================================================
// ACTUALIZAR LOCALIZACIÓN POSTGIS
// =====================================================

if ($location) {

    DB::statement(
        'UPDATE proceedings
         SET location = ST_SetSRID(
             ST_MakePoint(?, ?),
             4326
         )
         WHERE id = ?',
        [
            $location['longitude'],
            $location['latitude'],
            $proceeding->id
        ]
    );
}

    /*
    |--------------------------------------------------------------------------
    | Actualizar participantes
    |--------------------------------------------------------------------------
    */

    $proceeding
        ->participants()
        ->sync($participants);

    /*
    |--------------------------------------------------------------------------
    | Actualizar documentos
    |--------------------------------------------------------------------------
    */
 
    /*
|--------------------------------------------------------------------------
| Actualizar documentos
|--------------------------------------------------------------------------
*/

\Log::info('ANTES SYNC DOCUMENTOS', [
    'acta' => $proceeding->id,
    'documents' => $documents,
]);

try {

    $syncResult = $proceeding
        ->documents()
        ->sync($documents);

    \Log::info('DESPUES SYNC DOCUMENTOS', [
        'acta' => $proceeding->id,
        'documents' => $documents,
        'sync_result' => $syncResult,
    ]);

} catch (\Throwable $e) {

    \Log::error('ERROR SYNC DOCUMENTOS', [
        'acta' => $proceeding->id,
        'documents' => $documents,
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);

    throw $e;
}
    /*
    |--------------------------------------------------------------------------
    | Eliminar documentos que ya no pertenecen al acta
    |--------------------------------------------------------------------------
    */

    $deletedDocuments = array_diff(
        $oldDocuments,
        $documents
    );

    foreach ($deletedDocuments as $documentId) {

        $document = MediaFiles::find($documentId);

        if (!$document) {
            continue;
        }

        $usedElsewhere = DB::table('proceeding_media_files')
            ->where('media_file_id', $documentId)
            ->exists();

        if (!$usedElsewhere) {

            if ($document->path) {

                Storage::delete(
                    'public/' . $document->path
                );
            }

            $document->delete();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Respuesta
    |--------------------------------------------------------------------------
    */

    return response()->json([
        'success' => true,

        'data' => new ProceedingResource(
            $proceeding
                ->fresh()
                ->load([
                    'participants',
                    'commission',
                    'elaboradoPor',
                    'documents'
                ])
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