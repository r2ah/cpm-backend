<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AuthorityController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\MediaFileController;
use App\Http\Controllers\OpinionController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SITApiController;
use App\Http\Controllers\ProceedingController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckUserActivity;

Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::prefix('plan-maestro')->group(function () {
        Route::get('/', [SITApiController::class, 'index']);
        Route::get('/entities', [SITApiController::class, 'getEntities']);
        Route::get('/inscriptions', [SITApiController::class, 'getInscriptions']);
    });

    Route::middleware([
    'auth:sanctum',
    CheckUserActivity::class,
        ])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return response()->json($request->user(), 200);
        });

        Route::apiResource('authorities', AuthorityController::class);
        Route::apiResource('people', PersonController::class);
       
        
        Route::apiResource('interventions', InterventionController::class);
        Route::apiResource('proceedings', ProceedingController::class);
        Route::apiResource('opinions', OpinionController::class);
    
        Route::apiResource('commissions', CommissionController::class);
        
        
        Route::apiResource('users', UserController::class);
        Route::post('images/upload', [MediaFileController::class, 'store']);
    });
    
});


    
