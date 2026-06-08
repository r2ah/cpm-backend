<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PlanMaestroApiService
{
	protected $baseUrl;
	protected $apiKey;

	public function __construct()
	{
		$this->baseUrl = config('services.api.pm.base_url');
		$this->apiKey = config('services.api.pm.key');
	}
}
