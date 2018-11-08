<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

abstract class Provider extends ServiceProvider
{
    /**
     * The additional providers to be automatically registered.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * The polymorphic models to map to their alias.
     *
     * @var array
     */
    protected $morphMap = [];

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * The routers to be automatically mapped.
     *
     * @var array
     */
    protected $routers = [];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->registerProviders();
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() : void
    {
        $this->registerListeners();
        $this->mapModelMorphAliases();

        if (! $this->app->routesAreCached()) {
            $this->mapRoutes();
        }
    }

    /**
     * Register all additional service providers.
     *
     * @return void
     */
    protected function registerProviders() : void
    {
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Register predefined listeners event listeners.
     *
     * @return void
     */
    protected function registerListeners() : void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    /**
     * Map polymorphic models to their alias.
     *
     * @return void
     */
    protected function mapModelMorphAliases() : void
    {
        Relation::morphMap($this->morphMap);
    }

    /**
     * Map out all predefined module routes.
     *
     * @return void
     */
    protected function mapRoutes() : void
    {
        foreach ($this->routers as $router) {
            $this->app->make($router);
        }
    }
}
