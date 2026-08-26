<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatrimonioController extends Controller
{
    /**
     * URL base del servicio de Plan Maestro.
     */
    private string $baseUrl = 'http://apps.planmaestro.ohc.cu/Servicio/v1';

    /**
     * Cantidad de registros por página para el frontend.
     */
    private int $defaultLimit = 20;

    /**
     * Tiempo de caché de los patrimonios.
     */
    private int $cacheMinutes = 30;

    /**
     * Tiempo de caché del banco de imágenes.
     */
    private int $imageCacheMinutes = 60;


    /**
     * ==========================================================
     * INDEX
     * ==========================================================
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $page = max(
                (int) $request->query('page', 1),
                1
            );

            $limit = min(
                max(
                    (int) $request->query(
                        'limit',
                        $this->defaultLimit
                    ),
                    1
                ),
                100
            );


            /*
             * --------------------------------------------------
             * OBTENER TODOS LOS PATRIMONIOS
             * --------------------------------------------------
             */

            $records = Cache::remember(
                'patrimonio_entidades_patrimoniales',
                now()->addMinutes($this->cacheMinutes),
                function () {
                    return $this->obtenerEntidadesPatrimoniales();
                }
            );


            if (!is_array($records)) {

                Log::error(
                    'La caché de Patrimonio no contiene un array válido.'
                );

                return response()->json([
                    'success' => false,
                    'message' =>
                        'No se pudieron obtener las entidades patrimoniales.',
                ], 502);
            }


            /*
             * --------------------------------------------------
             * PAGINACIÓN LOCAL
             * --------------------------------------------------
             */

            $total = count($records);

            $totalPages = $total > 0
                ? (int) ceil($total / $limit)
                : 0;


            if (
                $totalPages > 0 &&
                $page > $totalPages
            ) {
                $page = $totalPages;
            }


            $offset = ($page - 1) * $limit;


            $pageRecords = array_slice(
                $records,
                $offset,
                $limit
            );


            $nextPage =
                $page < $totalPages
                    ? $page + 1
                    : null;


            $previousPage =
                $page > 1
                    ? $page - 1
                    : null;


            /*
             * --------------------------------------------------
             * RESPUESTA
             * --------------------------------------------------
             */

            return response()->json([

                'success' => true,

                'data' => array_values($pageRecords),

                'pagination' => [

                    'page' => $page,

                    'limit' => $limit,

                    'total' => $total,

                    'total_pages' => $totalPages,

                    'next_page' => $nextPage,

                    'previous_page' => $previousPage,

                ],

            ]);


        } catch (\Throwable $e) {

            Log::error(
                'Error consultando Patrimonio.',
                [
                    'message' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );


            return response()->json([

                'success' => false,

                'message' =>
                    'No se pudo obtener la información de Patrimonio.',

                'error' =>
                    $e->getMessage(),

            ], 500);
        }
    }


    /**
     * ==========================================================
     * OBTENER ENTIDADES PATRIMONIALES
     * ==========================================================
     *
     * Obtiene las entidades Patrimonio y las relaciona con
     * Banco_de_Imagenes.
     *
     * Relación principal:
     *
     * Patrimonio.codigo
     *
     * =
     *
     * Banco_de_Imagenes.metadata.codigo_inmueble
     *
     * También se utiliza metadata.codigo como respaldo.
     */
    private function obtenerEntidadesPatrimoniales(): array
    {
        $recordsPatrimoniales = [];


        /*
         * --------------------------------------------------
         * BANCO DE IMÁGENES
         * --------------------------------------------------
         */

        $imagenesPorCodigo = Cache::remember(
            'patrimonio_banco_imagenes_por_codigo',
            now()->addMinutes($this->imageCacheMinutes),
            function () {

                return $this->obtenerBancoImagenes();

            }
        );


        if (!is_array($imagenesPorCodigo)) {

            $imagenesPorCodigo = [];

        }


        /*
         * --------------------------------------------------
         * CONFIGURACIÓN
         * --------------------------------------------------
         */

        $page = 1;

        $limit = 100;

        $maxPages = 1000;


        /*
         * CONTADORES PARA DEBUG
         */

        $totalRecordsApi = 0;

        $totalPatrimoniales = 0;

        $totalConImagen = 0;

        $totalSinImagen = 0;


        /*
         * --------------------------------------------------
         * CONSULTAR PATRIMONIO
         * --------------------------------------------------
         */

        while ($page <= $maxPages) {

            try {

                $response = Http::timeout(30)
                    ->retry(
                        2,
                        500
                    )
                    ->get(
                        "{$this->baseUrl}/Entidades",
                        [
                            'operation' => 'getData',

                            'entidad' => 'Patrimonio',

                            'limit' => $limit,

                            'page' => $page,
                        ]
                    );


                /*
                 * --------------------------------------------------
                 * ERROR HTTP
                 * --------------------------------------------------
                 */

                if ($response->failed()) {

                    Log::error(
                        'Plan Maestro devolvió error HTTP para Patrimonio.',
                        [
                            'page' =>
                                $page,

                            'status' =>
                                $response->status(),

                            'body' =>
                                substr(
                                    $response->body(),
                                    0,
                                    1000
                                ),
                        ]
                    );

                    break;
                }


                /*
                 * --------------------------------------------------
                 * DECODIFICAR RESPUESTA
                 * --------------------------------------------------
                 */

                $data =
                    $this->decodificarRespuesta(
                        $response->body()
                    );


                if (!is_array($data)) {

                    Log::error(
                        'Plan Maestro devolvió JSON inválido para Patrimonio.',
                        [
                            'page' => $page,
                        ]
                    );

                    break;
                }


                /*
                 * --------------------------------------------------
                 * RECORDS
                 * --------------------------------------------------
                 */

                $records =
                    $data['data']['records']
                    ?? [];


                if (!is_array($records)) {

                    $records = [];

                }


                $totalRecordsApi += count($records);


                /*
                 * --------------------------------------------------
                 * SI NO HAY RECORDS
                 * --------------------------------------------------
                 */

                if (empty($records)) {
                    break;
                }


                /*
                 * --------------------------------------------------
                 * PROCESAR INMUEBLES
                 * --------------------------------------------------
                 */

                foreach ($records as $record) {

                    if (!is_array($record)) {
                        continue;
                    }


                    /*
                     * ------------------------------------------------
                     * VALOR PATRIMONIAL
                     * ------------------------------------------------
                     *
                     * El JSON real utiliza:
                     *
                     * "patrimonial": "t"
                     *
                     * No:
                     *
                     * "valor_patrimonial"
                     */

                    $valorPatrimonial =
                        $record['patrimonial']
                        ?? $record['valor_patrimonial']
                        ?? null;


                    if (
                        !$this->esVerdadero(
                            $valorPatrimonial
                        )
                    ) {
                        continue;
                    }


                    $totalPatrimoniales++;


                    /*
                     * ------------------------------------------------
                     * CÓDIGO DEL INMUEBLE
                     * ------------------------------------------------
                     *
                     * En el JSON real:
                     *
                     * "codigo": "230401270901"
                     */

                    $codigoInmueble =
                        $record['codigo']
                        ?? $record['codigo_gis']
                        ?? $record['codigo_inmueble']
                        ?? null;


                    if ($codigoInmueble !== null) {

                        $codigoInmueble =
                            trim(
                                (string) $codigoInmueble
                            );
                    }


                    /*
                     * ------------------------------------------------
                     * BUSCAR IMAGEN
                     * ------------------------------------------------
                     */

                    $imagenRepresentativa = null;


                    if (
                        $codigoInmueble !== null &&
                        $codigoInmueble !== ''
                    ) {

                        $imagenes =
                            $imagenesPorCodigo[
                                $codigoInmueble
                            ]
                            ?? [];


                        $imagenRepresentativa =
                            $this->seleccionarImagenRepresentativa(
                                $imagenes
                            );
                    }


                    /*
                     * ------------------------------------------------
                     * CONTADORES
                     * ------------------------------------------------
                     */

                    if (
                        is_array(
                            $imagenRepresentativa
                        )
                    ) {

                        $totalConImagen++;

                    } else {

                        $totalSinImagen++;

                    }


                    /*
                     * ------------------------------------------------
                     * AGREGAR IMAGEN
                     * ------------------------------------------------
                     */

                    $record['imagen'] =
                        $imagenRepresentativa;


                    /*
                     * ------------------------------------------------
                     * GUARDAR
                     * ------------------------------------------------
                     */

                    $recordsPatrimoniales[] =
                        $record;
                }


                /*
                 * --------------------------------------------------
                 * SIGUIENTE PÁGINA
                 * --------------------------------------------------
                 */

                $nextPage =
                    $data['data']['nextpage']
                    ?? null;


                /*
                 * Si el API no proporciona nextpage,
                 * intentamos utilizar totalpage.
                 */

                if (
                    $nextPage === null ||
                    $nextPage === false ||
                    $nextPage === ''
                ) {

                    $totalPageApi =
                        (int) (
                            $data['data']['totalpage']
                            ?? 0
                        );


                    if (
                        $totalPageApi > $page
                    ) {

                        $page++;

                        continue;
                    }


                    break;
                }


                $nextPage =
                    (int) $nextPage;


                if (
                    $nextPage <= $page
                ) {

                    break;
                }


                $page =
                    $nextPage;


            } catch (\Throwable $e) {

                Log::error(
                    'Error obteniendo página de Patrimonio.',
                    [
                        'page' =>
                            $page,

                        'message' =>
                            $e->getMessage(),
                    ]
                );

                break;
            }
        }


        /*
         * --------------------------------------------------
         * LOG FINAL
         * --------------------------------------------------
         */

        Log::info(
            'Patrimonio cargado y relacionado con imágenes.',
            [
                'paginas_consultadas' =>
                    $page,

                'records_api' =>
                    $totalRecordsApi,

                'entidades_patrimoniales' =>
                    $totalPatrimoniales,

                'entidades_con_imagen' =>
                    $totalConImagen,

                'entidades_sin_imagen' =>
                    $totalSinImagen,

                'codigos_con_imagen' =>
                    count(
                        $imagenesPorCodigo
                    ),
            ]
        );


        return $recordsPatrimoniales;
    }


    /**
     * ==========================================================
     * OBTENER BANCO DE IMÁGENES
     * ==========================================================
     *
     * Obtiene Banco_de_Imagenes y crea un índice:
     *
     * [
     *     '230400481801' => [
     *         imagen1,
     *         imagen2
     *     ]
     * ]
     *
     * El código se obtiene de:
     *
     * metadata.codigo_inmueble
     *
     * o:
     *
     * metadata.codigo
     */
    private function obtenerBancoImagenes(): array
    {
        $imagenesPorCodigo = [];


        $page = 1;

        $limit = 100;

        $maxPages = 2000;


        /*
         * CONTADORES
         */

        $totalImagenes = 0;

        $totalImagenesValidas = 0;


        while ($page <= $maxPages) {

            try {

                $response = Http::timeout(30)
                    ->retry(
                        2,
                        500
                    )
                    ->get(
                        "{$this->baseUrl}/Entidades",
                        [
                            'operation' =>
                                'getData',

                            'entidad' =>
                                'Banco_de_Imagenes',

                            'limit' =>
                                $limit,

                            'page' =>
                                $page,
                        ]
                    );


                /*
                 * --------------------------------------------------
                 * ERROR HTTP
                 * --------------------------------------------------
                 */

                if ($response->failed()) {

                    Log::error(
                        'Error consultando Banco_de_Imagenes.',
                        [
                            'page' =>
                                $page,

                            'status' =>
                                $response->status(),

                            'body' =>
                                substr(
                                    $response->body(),
                                    0,
                                    1000
                                ),
                        ]
                    );

                    break;
                }


                /*
                 * --------------------------------------------------
                 * DECODIFICAR
                 * --------------------------------------------------
                 */

                $data =
                    $this->decodificarRespuesta(
                        $response->body()
                    );


                if (!is_array($data)) {

                    Log::error(
                        'JSON inválido de Banco_de_Imagenes.',
                        [
                            'page' =>
                                $page,
                        ]
                    );

                    break;
                }


                /*
                 * --------------------------------------------------
                 * RECORDS
                 * --------------------------------------------------
                 */

                $records =
                    $data['data']['records']
                    ?? [];


                if (!is_array($records)) {

                    $records = [];

                }


                /*
                 * --------------------------------------------------
                 * NO HAY RECORDS
                 * --------------------------------------------------
                 */

                if (empty($records)) {
                    break;
                }


                /*
                 * --------------------------------------------------
                 * INDEXAR
                 * --------------------------------------------------
                 */

                foreach ($records as $imagen) {

                    if (!is_array($imagen)) {
                        continue;
                    }


                    $totalImagenes++;


                    /*
                     * ------------------------------------------------
                     * METADATA
                     * ------------------------------------------------
                     */

                    $metadata =
                        $imagen['metadata']
                        ?? [];


                    if (!is_array($metadata)) {

                        $metadata = [];

                    }


                    /*
                     * ------------------------------------------------
                     * CÓDIGO
                     * ------------------------------------------------
                     *
                     * JSON real:
                     *
                     * metadata.codigo_inmueble
                     *
                     * También:
                     *
                     * metadata.codigo
                     */

                    $codigo =
                        $metadata['codigo_inmueble']
                        ?? $metadata['codigo']
                        ?? null;


                    if (
                        $codigo === null ||
                        trim((string) $codigo) === ''
                    ) {

                        continue;
                    }


                    $codigo =
                        trim(
                            (string) $codigo
                        );


                    /*
                     * ------------------------------------------------
                     * URL
                     * ------------------------------------------------
                     */

                    $url =
                        $imagen['url']
                        ?? [];


                    if (!is_array($url)) {

                        continue;
                    }


                    /*
                     * ------------------------------------------------
                     * NORMALIZAR URLS
                     * ------------------------------------------------
                     */

                    $url =
                        $this->normalizarUrlsImagen(
                            $url
                        );


                    /*
                     * ------------------------------------------------
                     * VERIFICAR URL
                     * ------------------------------------------------
                     */

                    if (empty($url)) {

                        continue;
                    }


                    /*
                     * Guardamos la estructura original
                     * completa.
                     */

                    $imagen['url'] =
                        $url;


                    /*
                     * ------------------------------------------------
                     * CREAR ÍNDICE
                     * ------------------------------------------------
                     */

                    if (
                        !isset(
                            $imagenesPorCodigo[$codigo]
                        )
                    ) {

                        $imagenesPorCodigo[$codigo] =
                            [];
                    }


                    $imagenesPorCodigo[$codigo][] =
                        $imagen;


                    $totalImagenesValidas++;
                }


                /*
                 * --------------------------------------------------
                 * SIGUIENTE PÁGINA
                 * --------------------------------------------------
                 */

                $nextPage =
                    $data['data']['nextpage']
                    ?? null;


                if (
                    $nextPage === null ||
                    $nextPage === false ||
                    $nextPage === ''
                ) {

                    $totalPageApi =
                        (int) (
                            $data['data']['totalpage']
                            ?? 0
                        );


                    if (
                        $totalPageApi > $page
                    ) {

                        $page++;

                        continue;
                    }


                    break;
                }


                $nextPage =
                    (int) $nextPage;


                if (
                    $nextPage <= $page
                ) {

                    break;
                }


                $page =
                    $nextPage;


            } catch (\Throwable $e) {

                Log::error(
                    'Error obteniendo Banco_de_Imagenes.',
                    [
                        'page' =>
                            $page,

                        'message' =>
                            $e->getMessage(),
                    ]
                );

                break;
            }
        }


        /*
         * --------------------------------------------------
         * LOG
         * --------------------------------------------------
         */

        Log::info(
            'Banco de imágenes cargado.',
            [
                'paginas_consultadas' =>
                    $page,

                'imagenes_api' =>
                    $totalImagenes,

                'imagenes_validas' =>
                    $totalImagenesValidas,

                'inmuebles_con_imagenes' =>
                    count(
                        $imagenesPorCodigo
                    ),
            ]
        );


        /*
         * --------------------------------------------------
         * DEBUG DEL PRIMER CÓDIGO
         * --------------------------------------------------
         */

        if (!empty($imagenesPorCodigo)) {

            $primerCodigo =
                array_key_first(
                    $imagenesPorCodigo
                );


            Log::info(
                'Ejemplo de índice de imágenes.',
                [
                    'codigo' =>
                        $primerCodigo,

                    'cantidad' =>
                        count(
                            $imagenesPorCodigo[
                                $primerCodigo
                            ]
                        ),
                ]
            );
        }


        return $imagenesPorCodigo;
    }


    /**
     * ==========================================================
     * SELECCIONAR IMAGEN REPRESENTATIVA
     * ==========================================================
     *
     * Devuelve UNA imagen.
     *
     * Prioridad:
     *
     * 1. geometry = Inmueble
     * 2. type = georreferenciada
     * 3. fachada
     * 4. principal
     * 5. edificio
     * 6. fotografía
     * 7. arquitectura
     * 8. exterior
     * 9. rating
     * 10. fecha
     */
    private function seleccionarImagenRepresentativa(
        array $imagenes
    ): ?array {

        if (empty($imagenes)) {
            return null;
        }


        $candidatas = [];


        foreach ($imagenes as $imagen) {

            if (!is_array($imagen)) {
                continue;
            }


            $metadata =
                $imagen['metadata']
                ?? [];


            $urls =
                $imagen['url']
                ?? [];


            if (!is_array($metadata)) {
                $metadata = [];
            }


            if (!is_array($urls)) {
                $urls = [];
            }


            /*
             * --------------------------------------------------
             * VERIFICAR URL
             * --------------------------------------------------
             */

            $urls =
                $this->normalizarUrlsImagen(
                    $urls
                );


            if (empty($urls)) {
                continue;
            }


            /*
             * --------------------------------------------------
             * DATOS PARA SCORE
             * --------------------------------------------------
             */

            $geometry =
                strtolower(
                    trim(
                        (string) (
                            $metadata['geometry']
                            ?? ''
                        )
                    )
                );


            $type =
                strtolower(
                    trim(
                        (string) (
                            $metadata['type']
                            ?? ''
                        )
                    )
                );


            $category =
                strtolower(
                    trim(
                        (string) (
                            $metadata['category']
                            ?? ''
                        )
                    )
                );


            $description =
                strtolower(
                    trim(
                        (string) (
                            $metadata['description']
                            ?? ''
                        )
                    )
                );


            $keywords =
                strtolower(
                    trim(
                        (string) (
                            $metadata['keywords']
                            ?? ''
                        )
                    )
                );


            $name =
                strtolower(
                    trim(
                        (string) (
                            $metadata['nombre_actual']
                            ?? $metadata['nombre_original']
                            ?? $metadata['name']
                            ?? $metadata['nombre']
                            ?? ''
                        )
                    )
                );


            $texto =
                $geometry . ' ' .
                $type . ' ' .
                $category . ' ' .
                $description . ' ' .
                $keywords . ' ' .
                $name;


            /*
             * --------------------------------------------------
             * SCORE
             * --------------------------------------------------
             */

            $score = 0;


            /*
             * Geometry = Inmueble
             */

            if (
                $geometry === 'inmueble'
            ) {

                $score += 100;
            }


            /*
             * Tipo georreferenciada
             */

            if (
                str_contains(
                    $type,
                    'georreferenciada'
                )
            ) {

                $score += 10;
            }


            /*
             * Palabras positivas
             */

            $palabrasFotografia = [

                'foto',

                'fotografía',

                'fotografia',

                'edificio',

                'inmueble',

                'fachada',

                'principal',

                'arquitectura',

                'exterior',

                'interior',

                'construcción',

                'construccion',

                'patrimonio',

            ];


            foreach (
                $palabrasFotografia
                as $palabra
            ) {

                if (
                    str_contains(
                        $texto,
                        $palabra
                    )
                ) {

                    $score += 15;
                }
            }


            /*
             * Palabras negativas
             */

            $palabrasNoFotografia = [

                'mapa',

                'map',

                'plano',

                'planos',

                'cartografía',

                'cartografia',

                'ubicación',

                'ubicacion',

                'localización',

                'localizacion',

                'croquis',

                'esquema',

                'dibujo',

                'gis',

                'ortofoto',

                'ortofotografía',

                'ortofotografia',

            ];


            foreach (
                $palabrasNoFotografia
                as $palabra
            ) {

                if (
                    str_contains(
                        $texto,
                        $palabra
                    )
                ) {

                    $score -= 60;
                }
            }


            /*
             * Keywords
             */

            if (
                str_contains(
                    $keywords,
                    'fachada'
                )
            ) {

                $score += 30;
            }


            if (
                str_contains(
                    $keywords,
                    'principal'
                )
            ) {

                $score += 30;
            }


            if (
                str_contains(
                    $keywords,
                    'edificio'
                )
            ) {

                $score += 25;
            }


            /*
             * Description
             */

            if (
                str_contains(
                    $description,
                    'fachada'
                )
            ) {

                $score += 20;
            }


            /*
             * Rating
             */

            $rating =
                $metadata['rating']
                ?? 0;


            if (
                is_numeric($rating)
            ) {

                $score += min(
                    (int) $rating,
                    10
                );
            }


            /*
             * Fecha
             */

            $date =
                $metadata['date']
                ?? $metadata['date_creation']
                ?? '';


            /*
             * Guardar candidata
             */

            $candidatas[] = [

                'imagen' =>
                    $imagen,

                'score' =>
                    $score,

                'date' =>
                    $date,

            ];
        }


        /*
         * --------------------------------------------------
         * NO HAY IMÁGENES VÁLIDAS
         * --------------------------------------------------
         */

        if (empty($candidatas)) {

            return null;
        }


        /*
         * --------------------------------------------------
         * ORDENAR
         * --------------------------------------------------
         */

        usort(
            $candidatas,
            function ($a, $b) {

                if (
                    $a['score']
                    !==
                    $b['score']
                ) {

                    return
                        $b['score']
                        <=>
                        $a['score'];
                }


                return strcmp(
                    (string) (
                        $b['date']
                        ?? ''
                    ),
                    (string) (
                        $a['date']
                        ?? ''
                    )
                );
            }
        );


        /*
         * --------------------------------------------------
         * IMAGEN GANADORA
         * --------------------------------------------------
         */

        $imagen =
            $candidatas[0]['imagen']
            ?? null;


        if (!is_array($imagen)) {

            return null;
        }


        /*
         * --------------------------------------------------
         * NORMALIZAR URL
         * --------------------------------------------------
         */

        $imagen['url'] =
            $this->normalizarUrlsImagen(
                $imagen['url']
                ?? []
            );


        if (
            empty(
                $imagen['url']
            )
        ) {

            return null;
        }


        /*
         * --------------------------------------------------
         * DEVOLVER IMAGEN COMPLETA
         * --------------------------------------------------
         *
         * Importante:
         *
         * NO eliminamos metadata.
         *
         * Devuelve exactamente:
         *
         * {
         *     metadata: {...},
         *     url: {...}
         * }
         */

        return $imagen;
    }


    /**
     * ==========================================================
     * NORMALIZAR URLS DE IMAGEN
     * ==========================================================
     *
     * El API devuelve rutas como:
     *
     * fotos gis/habana vieja/0048/archivo.png
     *
     * Los espacios deben convertirse en %20.
     */
    private function normalizarUrlsImagen(
    array $urls
): array {

    $resultado = [];

    foreach (
        [
            '320x240',
            '800x600',
            '1024x768',
        ]
        as $size
    ) {

        if (
            !array_key_exists(
                $size,
                $urls
            )
        ) {
            continue;
        }

        $url =
            trim(
                (string)
                $urls[$size]
            );

        if ($url === '') {
            continue;
        }

        $url =
            str_replace(
                ' ',
                '%20',
                $url
            );

        $url =
            str_replace(
                '%2520',
                '%20',
                $url
            );

        $resultado[$size] =
            $url;
    }

    return $resultado;
}

    /**
     * ==========================================================
     * DECODIFICAR RESPUESTA
     * ==========================================================
     *
     * Maneja:
     *
     * - JSON normal
     * - JSON con BOM
     * - JSON devuelto como string JSON
     */
    private function decodificarRespuesta(
        string $raw
    ): ?array {

        /*
         * --------------------------------------------------
         * ELIMINAR BOM UTF-8
         * --------------------------------------------------
         */

        $raw =
            preg_replace(
                '/^\xEF\xBB\xBF/',
                '',
                $raw
            );


        /*
         * Algunos servidores pueden devolver el BOM
         * mal interpretado como texto.
         */

        $raw =
            preg_replace(
                '/^ï»¿/',
                '',
                $raw
            );


        /*
         * --------------------------------------------------
         * PRIMER JSON
         * --------------------------------------------------
         */

        $data =
            json_decode(
                $raw,
                true
            );


        /*
         * --------------------------------------------------
         * JSON ENCAPSULADO COMO STRING
         * --------------------------------------------------
         */

        if (is_string($data)) {

            $data =
                preg_replace(
                    '/^\xEF\xBB\xBF/',
                    '',
                    $data
                );


            $data =
                preg_replace(
                    '/^ï»¿/',
                    '',
                    $data
                );


            $data =
                json_decode(
                    $data,
                    true
                );
        }


        if (
            !is_array($data)
        ) {

            return null;
        }


        return $data;
    }


    /**
     * ==========================================================
     * CONVERTIR VALORES BOOLEANOS DEL API
     * ==========================================================
     *
     * El API utiliza principalmente:
     *
     * "t"
     * "f"
     */
    private function esVerdadero(
        mixed $valor
    ): bool {

        if (
            $valor === true ||
            $valor === 1 ||
            $valor === '1'
        ) {

            return true;
        }


        if (
            is_string($valor)
        ) {

            return in_array(
                strtolower(
                    trim($valor)
                ),
                [
                    't',
                    'true',
                    'si',
                    'sí',
                    'yes',
                    'y',
                ],
                true
            );
        }


        return false;
    }


    /**
     * ==========================================================
     * LIMPIAR CACHÉ
     * ==========================================================
     */
    public function limpiarCache(): JsonResponse
    {
        Cache::forget(
            'patrimonio_entidades_patrimoniales'
        );


        Cache::forget(
            'patrimonio_banco_imagenes_por_codigo'
        );


        return response()->json([

            'success' => true,

            'message' =>
                'Caché de Patrimonio e imágenes eliminada correctamente.',

        ]);
    }
}