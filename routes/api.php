<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\AuthorityController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProceedingController;
use App\Http\Controllers\OpinionController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\PlanMaestroApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MediaFileController;

use App\Http\Controllers\Auth\AuthController;

Route::prefix('v1/plan-maestro')->group(function () {
    Route::get('/', [PlanMaestroApiController::class, 'index']);
    Route::get('/entities', [PlanMaestroApiController::class, 'getEntities']);
    Route::get('/inscriptions', [PlanMaestroApiController::class, 'getInscriptions']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    //TODO: Revisar esto
    Route::get('user', function(Request $request){
        return response()->json($request->user(), 200);
    });
    
	Route::apiResource('authorities', AuthorityController::class)->missing(function (Request $request) {
        return Redirect::route('authorities.index');
    });

	Route::apiResource('people', PersonController::class)->missing(function (Request $request) {
        return Redirect::route('people.index');
    });

	Route::apiResource('proceedings', ProceedingController::class)->missing(function (Request $request) {
        return Redirect::route('proceedings.index');
    });

	Route::apiResource('opinions', OpinionController::class)->missing(function (Request $request) {
        return Redirect::route('opinions.index');
    });

	Route::apiResource('interventions', InterventionController::class)->missing(function (Request $request) {
        return Redirect::route('interventions.index');
    });

	Route::apiResource('users', UserController::class)->missing(function (Request $request) {
        return Redirect::route('users.index');
    });

    Route::post('images/upload', [MediaFileController::class, 'store']);
});