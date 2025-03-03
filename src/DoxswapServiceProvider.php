<?php

namespace Blaspsoft\Doxswap;

use Blaspsoft\Doxswap\Doxswap;
use Illuminate\Support\ServiceProvider;
use Blaspsoft\Doxswap\ConversionService;

class DoxswapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('doxswap.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'doxswap');

        // Register the main class to use with the facade
        //$this->app->singleton('doxswap', function () {
        //    return new Doxswap;
        //});

        $this->app->bind('doxswap', function () {
            return new Doxswap(new ConversionService());
        });
    }
}
