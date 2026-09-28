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
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatrimonioController;
use App\Http\Controllers\GisController;


use App\Http\Controllers\OpinionDocumentController;


Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::prefix('plan-maestro')->group(function () {
        Route::get('/', [SITApiController::class, 'show']);
        Route::get('/entities', [SITApiController::class, 'show']);
        Route::get('/inscriptions', [SITApiController::class, 'show']);
    });

    Route::middleware([
    'auth:sanctum',
    CheckUserActivity::class,
        ])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => new \App\Http\Resources\UserResource(
                    $request->user()->load('commissions')
                ),
            ], 200);
        });

        Route::apiResource('authorities', AuthorityController::class);
        
    
        Route::apiResource('commissions', CommissionController::class);
        
        
        Route::apiResource('interventions', InterventionController::class);
        
        Route::get('opinion-documents/{document}/download',[OpinionDocumentController::class,'download']) ->name('opinions.documents.download');
        
        Route::post('images/upload', [MediaFileController::class, 'store']);
        Route::apiResource('people', PersonController::class);
        Route::apiResource('appointments', AppointmentController::class);
        Route::patch('users/password', [UserController::class, 'updatePassword']);
        Route::apiResource('users', UserController::class);
        Route::get('/gis/layers/{layer}', [GisController::class, 'layer']);
        Route::get('/gis/buildings/{code}', [GisController::class, 'building']);
        Route::apiResource('proceedings', ProceedingController::class);
        
        Route::get('media-files/{file}/download',[MediaFileController::class, 'download'])->name('media-files.download');
    });
    Route::apiResource('opinions', OpinionController::class);
   Route::get('/patrimonio', [PatrimonioController::class, 'index']);
    Route::get('/patrimonio/imagenes', [PatrimonioController::class, 'imagenesPorCodigo']);
});


    
