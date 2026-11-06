<?php

namespace App\Providers;

use App\Services\CompareService;
use Illuminate\Support\ServiceProvider;

class CompareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('compare', function($app) {
            return new CompareService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
