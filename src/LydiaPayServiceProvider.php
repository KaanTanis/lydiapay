<?php

namespace DataGrade\LydiaPay;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LydiaPayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lydiapay.php', 'lydiapay');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Route::group([
            'prefix' => config('lydiapay.route_configuration.prefix'),
            'middleware' => config('lydiapay.route_configuration.middleware')
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/lydiapay.php' => config_path('lydiapay.php'),
            ], 'config');
        }
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => config('lydiapay.route_configuration.prefix')
        ];
    }
}
