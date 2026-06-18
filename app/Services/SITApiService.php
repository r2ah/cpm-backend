<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class SITApiService
{
	private string $baseUrl;
	private string $apiKey;

	public function __construct()
	{
		$this->baseUrl = config('services.api.pm.base_url');
		$this->apiKey = config('services.api.pm.key');
	}

    public function getEntities(string $codigo, string $entidad, string $filter, int $limit, int $page): array
    {
		$params = [
                'codigo' => $codigo,
        ];

		if($codigo) {
			$cache_key = Hash::make("entity:{$codigo}");
		} else {
			$cache_key = Hash::make("entities:{$entidad},filter:{$filter},limit:{$limit},page:{$page}");

			$params = [
                'entidad' => $entidad,
                'filter' => $filter,
                'limit' => $limit,
				'page' => $page,
            ];
		}

        return Cache::remember($cache_key, 1800, function () use ($params) {
            $response = Http::get("{$this->baseUrl}/Entidades?operation=getData", $params);

            if ($response->failed()) {
                throw new \Exception('SIT API service unavailable');
            }

            return $response->json()->toArray();
        });
    }

    public function getIncriptions(string $codigo, string $folio, string $nombre, string $filter, int $limit, int $page): array
    {
		$params = [
                'codigo' => $codigo,
        ];

		if($codigo) {
			$cache_key = Hash::make("entity:{$codigo}");
		} else {
			$cache_key = Hash::make("inscriptions:{$folio},name:{$nombre},filter:{$filter},limit:{$limit},page:{$page}");

			$params = [
                'folio' => $folio,
                'nombre' => $nombre,
                'filter' => $filter,
                'limit' => $limit,
				'page' => $page,
            ];
		}

        return Cache::remember($cache_key, 1800, function () use ($params) {
            $response = Http::get("{$this->baseUrl}/Inscripciones?operation=getData", $params);

            if ($response->failed()) {
                throw new \Exception('SIT API service unavailable');
            }

            return $response->json()->toArray();
        });
    }	
}
