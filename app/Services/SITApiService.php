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
		$this->baseUrl = rtrim((string) config('services.api.pm.base_url'), '/');
		$this->apiKey = (string) config('services.api.pm.key', '');
	}

	private function decodeResponse($response): array
	{
		$body = preg_replace('/^\xEF\xBB\xBF/', '', $response->body());
		$data = json_decode($body, true);

		if (!is_array($data)) {
		    throw new \Exception('Invalid response from SIT API');
		}

		return $data;
	}

    public function getEntities(?string $codigo, ?string $entidad, string $filter, int $limit, int $page): array
    {
		$params = [
                'codigo' => $codigo,
        ];

		if($codigo) {
			$cache_key = Hash::make("entities:{$codigo},{$entidad}");
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
            $url = "{$this->baseUrl}/Entidades?operation=getData&" . http_build_query($params);
            $response = Http::connectTimeout(5)
                ->timeout(10)
                ->get($url);

            if ($response->failed()) {
                throw new \Exception('SIT API service unavailable');
            }

            return $this->decodeResponse($response);
        });
    }

    public function getIncriptions(?string $codigo, string $folio, ?string $nombre, string $filter, int $limit, int $page): array
    {
		$params = [
		        'codigo' => $codigo,
		];

		if($codigo) {
			$cache_key = Hash::make("inscriptions:{$codigo},folio:{$folio}");
		} else {
			$cache_key = Hash::make("inscriptions:{$folio},name:{$nombre},filter:{$filter},limit:{$limit},page:{$page}");

			$params = [
		        'folio' => $folio,
		    ];
		}

        return Cache::remember($cache_key, 1800, function () use ($params) {
            $url = "{$this->baseUrl}/Inscripciones?operation=getData&" . http_build_query($params);
            $response = Http::connectTimeout(5)
                ->timeout(10)
                ->get($url);

            if ($response->failed()) {
                throw new \Exception('SIT API service unavailable');
            }

            return $this->decodeResponse($response);
        });
    }	
}
