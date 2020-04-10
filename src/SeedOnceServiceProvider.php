<?php

namespace Ranium\SeedOnce;

use Illuminate\Support\ServiceProvider;

class SeedOnceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/seedonce.php', 'seedonce'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__.'/../config/seedonce.php' => config_path('seedonce.php'),
            ], 'config');

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}