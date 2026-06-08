<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\PlanMaestroApiService;

class PlanMaestroApiController extends Controller
{
    protected $pmApiService;

    public function __construct(PlanMaestroApiService $pmApiService)
    {
	$this->$pmApiService = $pmApiService;
    }
}
