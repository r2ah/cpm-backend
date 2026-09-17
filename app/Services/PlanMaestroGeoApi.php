<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PlanMaestroGeoApi
{
    private const BASE_URL = 'http://apps.planmaestro.ohc.cu/Geoapi';
    private const USER = 'inv_ohch';
    private const KEY_CACHE = 'plan_maestro_geoapi_key';
    private const HTTP_TIMEOUT = 120;
    private const HTTP_CONNECT_TIMEOUT = 15;
    private const LAYER_CACHE_SECONDS = 300;
    private const KEY_LOCK = 'plan_maestro_geoapi_key_lock';

    private const LAYERS = [
        'base',
        'manzanas',
        'manzanas_zpc',
        'parcelas',
        'parcelas_zpc',
        'aceras',
        'calles',
        'direccion',
        'edificios',
        'edificios_zpc',
        'canteros',
        'ornamental',
        'publicos',
    ];

    public function layer(string $layer): array
    {
        if (!in_array($layer, self::LAYERS, true)) {
            throw new RuntimeException('La capa GIS solicitada no está disponible.');
        }

        return Cache::remember(
            "plan_maestro_geoapi_layer_{$layer}",
            now()->addSeconds(self::LAYER_CACHE_SECONDS),
            fn (): array => array_map(
                fn (array $row): array => $this->normalizeLayerRow($row),
                $this->request("/geodata/{$layer}")
            )
        );
    }

    public function building(string $code): array
    {
        return $this->request('/geodata_filtered/edificio', [
            'codigo' => $code,
        ]);
    }

    public function alternativeAddresses(string $code): array
    {
        return $this->request('/geodata_filtered/alt_dir', [
            'codigo' => $code,
        ]);
    }

    private function request(string $path, array $query = []): array
    {
        $response = Http::acceptJson()
            ->retry(2, 1000)
            ->timeout(self::HTTP_TIMEOUT)
            ->connectTimeout(self::HTTP_CONNECT_TIMEOUT)
            ->withHeaders([
                'X-API-KEY' => $this->accessKey(),
            ])
            ->get(self::BASE_URL . $path, $query);

        if ($response->status() === 400 || $response->status() === 401 || $response->status() === 403) {
            Cache::forget(self::KEY_CACHE);
            $response = Http::acceptJson()
                ->retry(2, 1000)
                ->timeout(self::HTTP_TIMEOUT)
                ->connectTimeout(self::HTTP_CONNECT_TIMEOUT)
                ->withHeaders([
                    'X-API-KEY' => $this->accessKey(),
                ])
                ->get(self::BASE_URL . $path, $query);
        }

        if ($response->failed()) {
            throw new RuntimeException('Plan Maestro no pudo responder la consulta GIS.');
        }

        $payload = $this->decodePayload($response->body());

        if (($payload['status'] ?? null) !== 'success' || !is_array($payload['data'] ?? null)) {
            throw new RuntimeException('Plan Maestro devolvió una respuesta GIS inválida.');
        }

        return $payload['data'];
    }

    private function accessKey(): string
    {
        $cachedKey = Cache::get(self::KEY_CACHE);
        if (is_string($cachedKey) && $cachedKey !== '') {
            return $cachedKey;
        }

        return Cache::lock(self::KEY_LOCK, 180)->block(130, function (): string {
            $cachedKey = Cache::get(self::KEY_CACHE);
            if (is_string($cachedKey) && $cachedKey !== '') {
                return $cachedKey;
            }

            $response = Http::acceptJson()
                ->retry(2, 1000)
                ->timeout(self::HTTP_TIMEOUT)
                ->connectTimeout(self::HTTP_CONNECT_TIMEOUT)
                ->get(
                    self::BASE_URL . '/geodatakey',
                    ['user' => self::USER]
                );

            if ($response->failed()) {
                throw new RuntimeException('No se pudo obtener la llave GIS de Plan Maestro.');
            }

            $payload = $this->decodePayload($response->body());

            if (($payload['status'] ?? null) !== 'success' || !is_string($payload['key'] ?? null)) {
                throw new RuntimeException('Plan Maestro no devolvió una llave GIS válida.');
            }

            Cache::put(self::KEY_CACHE, $payload['key'], now()->addSeconds(100));

            return $payload['key'];
        });
    }

    private function decodePayload(string $body): array
    {
        $body = preg_replace('/^\xEF\xBB\xBF/', '', $body) ?? $body;
        $payload = json_decode($body, true);

        if (!is_array($payload)) {
            throw new RuntimeException('Plan Maestro devolvió un JSON inválido.');
        }

        return $payload;
    }

    private function normalizeLayerRow(array $row): array
    {
        $normalized = [
            'id' => $row['id'] ?? null,
            'codigo' => $row['codigo'] ?? null,
            'geo_wkt' => $row['geo_wkt'] ?? null,
        ];

        foreach ([
            'direccion',
            'direccion_completa',
            'calle',
            'numero',
            'municipio',
            'provincia',
        ] as $field) {
            if (array_key_exists($field, $row)) {
                $normalized[$field] = $row[$field];
            }
        }

        return $normalized;
    }
}
