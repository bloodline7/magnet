<?php

namespace Ausumsports\Admin;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Ausumsports\Admin\Http\Middleware\Admin;
use Illuminate\Pagination\Paginator;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
	    Log::setTimezone(new \DateTimeZone('Asia/Seoul'));

        $this->registerHelpers();
	    //$this->registerEvents();
        $this->registerMiddleware();

        $this->registerAuthProvider();

        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ausumsports');
         $this->loadViewsFrom(__DIR__.'/../resources/views', 'adminViews');
         $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
         $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function registerMiddleware()
    {

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('Admin', Admin::class);

       /*
        모든 페이지에 미들웨어를 적용한다
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(Admin::class);*/

    }

    public function registerEvents()
    {
	    Event::listen('event.*', function ($eventName, array $data) {
		  Log::notice($eventName, $data);
	    });
    }
    /**
     * Register helpers file
     */
    public function registerHelpers()
    {
        if (file_exists($file = __DIR__.'/helpers.php')) {
            require $file;
        }
    }


    /**
     * Default Auth Model change to App/Model/user to Package Admin Model
     */
    public function registerAuthProvider()
    {

        Config::set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'bloodline',
        ]);

        // Will use the EloquentUserProvider driver with the Admin model
        Config::set('auth.providers.bloodline', [
            'driver' => 'eloquent',
            'model' => \Ausumsports\Admin\Models\Admin::class,
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/admin.php', 'admin');

        // Register the service the package provides.
        $this->app->singleton('admin', function ($app) {
            return new Admin;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['admin'];
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
            __DIR__.'/../config/admin.php' => config_path('admin.php'),
        ], 'admin.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/ausumsports'),
        ], 'admin.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/ausumsports'),
        ], 'admin.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/ausumsports'),
        ], 'admin.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
