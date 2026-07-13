<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Services\SITApiService;

class SITApiController extends Controller
{
    public function __construct(
        private SITApiService $sitApiService
    ) {}

    public function show(Request $request): JsonResponse
    {
        $codigo = $request->query('codigo');
        $entidad = $request->query('entidad');
        $filter = $request->query('filter', '');
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        try {
            if($request->has('tipo') && $request->query('tipo') === 'inscripciones') {
                $folio = $request->query('folio', '');
                $nombre = $request->query('nombre', '');                

                if(!$codigo && !$folio && !$nombre) {
                    return response()->json([
                        'error' => 'Either "codigo", "folio" or "nombre" query parameter is required',
                    ], 400);
                }

                $data = $this->sitApiService->getIncriptions($codigo, $folio, $nombre, $filter, $limit, $page);
            } else {
                if(!$codigo && !$entidad) {
                    return response()->json([
                        'error' => 'Either "codigo" or "entidad" query parameter is required',
                    ], 400);
                }

                $data = $this->sitApiService->getEntities($codigo, $entidad, $filter, $limit, $page);
            }

            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch data from SIT API',
            ], 503);
        }
    }   
}
