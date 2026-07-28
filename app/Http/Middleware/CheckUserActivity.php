<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckUserActivity
{
    /**
     * Tiempo máximo de inactividad en minutos.
     */
    const TIMEOUT = 15;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {

            $lastActivity = $user->last_activity;

            if (
                $lastActivity &&
                Carbon::parse($lastActivity)->diffInMinutes(now()) >= self::TIMEOUT
            ) {

                // Eliminar el token actual si existe
                if ($token = $user->currentAccessToken()) {
                    $token->delete();
                }

                return response()->json([
                    'message' => 'Sesión expirada por inactividad.'
                ], 401);
            }
        }

        // Ejecutar la petición
        $response = $next($request);

        // Actualizar la última actividad del usuario
        if ($user) {
            $user->update([
                'last_activity' => now(),
            ]);
        }

        return $response;
    }
}