<?php

namespace App\Http\Controllers;

use App\Models\Opinion;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\StoreOpinionRequest;
use App\Http\Requests\UpdateOpinionRequest;
use App\Http\Resources\OpinionResource;

use Illuminate\Validation\ValidationException;

class OpinionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Opinion::query();

        if ($search = $request->query('search')) {

            $query->where(function ($q) use ($search) {

                $q->where('entity', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");

            });

        }

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
            'data' => OpinionResource::collection($items)
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreOpinionRequest $request): JsonResponse
{   logger()->debug($request->header('Authorization'));
    try {

        logger('ENTRO AL STORE');

        $validated = $request->validated();

        logger('VALIDADO');

        $authUser = Auth::user();

        logger('AUTH');

        if ($authUser->hasRole('admin')) {

            logger('ADMIN');

            $preparedBy = User::with('commissions')
                ->findOrFail($validated['prepared_by']);

            logger('USUARIO ENCONTRADO');

            $commission = $preparedBy->commissions->first();

            logger('COMISION OBTENIDA');

        } else {

            logger('EDITOR');

            $authUser->load('commissions');

            $commission = $authUser->commissions->first();

        }

        logger('ANTES DEL CREATE');

        $validated['commission_id'] = $commission->id;

        $opinion = Opinion::create($validated);

        logger('DESPUES DEL CREATE');

        return response()->json([
            'success' => true,
            'data' => new OpinionResource($opinion)
        ]);

    } catch (\Throwable $e) {

        logger()->error($e->getMessage());
        logger()->error($e->getFile().':'.$e->getLine());

        return response()->json([
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ],500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Opinion $opinion): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new OpinionResource($opinion)
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateOpinionRequest $request, Opinion $opinion): JsonResponse
{
    try {

        logger('ENTRO AL UPDATE');

        $validated = $request->validated();

        logger('VALIDADO');

        $authUser = Auth::user();

        logger('AUTH');

        if (!$authUser) {
            throw new \Exception('Usuario no autenticado.');
        }

        if ($authUser->hasRole('admin')) {

            logger('ADMIN');

            $preparedBy = User::with('commissions')
                ->findOrFail($validated['prepared_by']);

            $commission = $preparedBy->commissions->first();

        } else {

            logger('EDITOR');

            $authUser->load('commissions');

            $commission = $authUser->commissions->first();

        }

        if (!$commission) {
            throw new \Exception('El usuario no pertenece a ninguna comisión.');
        }

        $validated['commission_id'] = $commission->id;

        logger('ANTES DEL UPDATE');

        $opinion->update($validated);

        logger('DESPUES DEL UPDATE');

        return response()->json([
            'success' => true,
            'data' => new OpinionResource($opinion)
        ]);

    } catch (\Throwable $e) {

        logger()->error($e->getMessage());
        logger()->error($e->getFile().':'.$e->getLine());

        return response()->json([
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
}

    /**
     * Remove the specified resource.
     */
    public function destroy(Opinion $opinion): JsonResponse
    {
        $opinion->delete();

        return response()->json([
            'success' => true
        ]);
    }
}