<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->extend('translation.loader', function ($loader, $app) {
            return new \App\Translation\JsonGroupFileLoader($app['files'], $app['path.lang']);
        });
    }
}
