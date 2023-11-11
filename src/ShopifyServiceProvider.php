<?php

namespace Kmrifat\Shopify;

use Kmrifat\Shopify\Commands\RegisterWebhooks;
use Kmrifat\Shopify\Providers\SocialiteDriverProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ShopifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/shopify.php', 'shopify');

        $this->app->register(SocialiteDriverProvider::class);

        $this->app->bind('shopify', function ($app) {
            return new Shopify();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        Route::prefix('laravel-shopify')
            ->as('laravel-shopify.')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/shopify.php' => config_path('shopify.php')
            ], 'config');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            $this->commands([
                RegisterWebhooks::class
            ]);

        }
    }
}
