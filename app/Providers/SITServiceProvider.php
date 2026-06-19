<?php
namespace App\Providers;

use App\Services\SITApiService;
use Illuminate\Support\ServiceProvider;

class SITServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singletonIf(SITApiService::classclass, function ($app) {
            return new SITApiService();
        });
    }  
}