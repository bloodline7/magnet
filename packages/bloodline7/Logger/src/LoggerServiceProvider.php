<?php

namespace Bloodline7\Logger;

use Illuminate\Support\ServiceProvider;
use Bloodline7\Logger\Commands\Logger;

class LoggerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bloodline7');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'bloodline7');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();

            $this->commands([
                Logger::class
            ]);



        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/logger.php', 'logger');

        // Register the service the package provides.
        $this->app->singleton('logger', function ($app) {
            return new Logger;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['logger'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/logger.php' => config_path('logger.php'),
        ], 'logger.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/bloodline7'),
        ], 'logger.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/bloodline7'),
        ], 'logger.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/bloodline7'),
        ], 'logger.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
