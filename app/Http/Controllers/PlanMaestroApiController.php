<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Services\PlanMaestroApiService;

class PlanMaestroApiController extends Controller
{
    protected PlanMaestroApiService $pmApiService;

    public function __construct(PlanMaestroApiService $pmApiService)
    {
	    $this->pmApiService = $pmApiService;
    }
}
