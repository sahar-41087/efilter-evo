<?php

namespace Sahar41087\EFilterEvo;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class EFilterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap package services: views, routes, asset publishing.
     */
    public function boot(): void
    {
        // Blade views: resources/views/efilter/*
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'efilter');

        // Frontend routes (Evolution CMS loads custom routes via RoutingServiceProvider).
        // For a composer package we register them here so they are always present.
        $routesFile = __DIR__ . '/../routes/web.php';
        if (is_file($routesFile)) {
            Route::middleware('web')->group($routesFile);
        }

        // Publishable assets (css/js) -> user site assets/efilter/
        $this->publishes([
            __DIR__ . '/../assets/efilter' => $this->app->basePath('assets/efilter'),
        ], 'efilter-assets');

        // Publishable engine templates (config.default.php) -> customizable copy
        $this->publishes([
            __DIR__ . '/Support/config/config.default.php' => $this->app->basePath('custom/efilter/config.default.php'),
        ], 'efilter-config');
    }

    public function register(): void
    {
        // Bind the engine as a singleton so it can be injected where needed.
        $this->app->singleton(Support\Engine::class, function ($app) {
            return new Support\Engine(evo(), []);
        });
    }
}
