<?php

namespace App\Http\Controllers;

use App\Services\PlanMaestroGeoApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class GisController extends Controller
{
    public function __construct(
        private readonly PlanMaestroGeoApi $geoApi
    ) {
    }

    public function layer(string $layer): JsonResponse
    {
        return $this->execute(
            fn () => $this->geoApi->layer($layer)
        );
    }

    public function building(string $code): JsonResponse
    {
        return $this->execute(
            fn () => [
                'building' => $this->geoApi->building($code),
                'alternative_addresses' => $this->geoApi->alternativeAddresses($code),
            ]
        );
    }

    private function execute(callable $callback): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $callback(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 502);
        }
    }
}
