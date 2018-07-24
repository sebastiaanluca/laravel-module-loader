<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

abstract class Provider extends ServiceProvider
{
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
     */
    public function register() : void
    {
        $this->registerListeners();
    }

    /**
     * Bootstrap the application services.
     */
    public function boot() : void
    {
        $this->mapMorphTypes();
        $this->mapRoutes();
    }

    /**
     * Register predefined listeners event listeners.
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
     */
    protected function mapMorphTypes() : void
    {
        Relation::morphMap($this->morphMap);
    }

    /**
     * Map out all predefined module routes.
     */
    protected function mapRoutes() : void
    {
        foreach ($this->routers as $router) {
            $this->app->make($router);
        }
    }
}
