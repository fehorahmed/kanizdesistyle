<?php

namespace App\Providers;

use App\Services\WishlistService;
use Illuminate\Support\ServiceProvider;

class WishlistServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('wishlist', function($app) {
            return new WishlistService();
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
