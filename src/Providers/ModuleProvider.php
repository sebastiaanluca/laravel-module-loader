<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Providers;

use Illuminate\Database\Eloquent\Factory;
use ReflectionClass;

class ModuleProvider extends Provider
{
    /**
     * @var string
     */
    private $packageName;

    /**
     * @var string
     */
    private $classDirectory;

    /**
     * Register the application services.
     */
    public function register() : void
    {
        $this->registerConfiguration();
        $this->registerFactories();

        parent::register();
    }

    /**
     * Bootstrap the application services.
     */
    public function boot() : void
    {
        $this->loadPublishableResources();
        $this->bootResources();

        parent::boot();
    }

    /**
     * Merge the default module configuration file with its published counterpart.
     */
    protected function registerConfiguration() : void
    {
        $configuration = "{$this->getClassDirectory()}/../../config/{$this->getPackageName()}.php";

        if (! file_exists($configuration)) {
            return;
        }

        $this->mergeConfigFrom(
            $configuration,
            $this->getPackageName()
        );
    }

    /**
     * Register directories containing Eloquent model factories if enabled in the current
     * environment.
     */
    protected function registerFactories() : void
    {
        if (! app()->environment(config("{$this->getPackageName()}.development_environments"))) {
            return;
        }

        $this->app->make(Factory::class)->load(
            // FIXME
            $this->getModule()->getPath() . '/database/factories'
        );
    }

    /**
     * Register all publishable module assets.
     */
    protected function loadPublishableResources() : void
    {
        $this->publishes(
            [
                "{$this->getClassDirectory()}/../../config" => config_path("{$this->getPackageName()}.php"),
            ],
            $this->getPackageName()
        );
    }

    /**
     * Prepare all module assets.
     */
    protected function bootResources() : void
    {
        // FIXME
        $this->loadMigrationsFrom($this->getModule()->getPath() . '/database/migrations');
        $this->loadTranslationsFrom($this->getModule()->getPath() . '/resources/lang', $this->getPackageName());
        $this->loadJsonTranslationsFrom($this->getModule()->getPath() . '/resources/lang');

        if (file_exists($views = $this->getModule()->getPath() . '/resources/views')) {
            $this->loadViewsFrom($views, $this->getPackageName());
        }
    }

    /**
     * Get the directory of the current class.
     *
     * Uses reflection to get the directory of the child class instead of the parent if applicable.
     *
     * @return string
     */
    protected function getClassDirectory() : string
    {
        // Some primitive caching
        if ($this->classDirectory) {
            return $this->classDirectory;
        }

        $reflection = new ReflectionClass(get_class($this));

        $this->classDirectory = dirname($reflection->getFileName());

        return $this->classDirectory;
    }

    /**
     * The lowercase name of the package.
     *
     * @return string
     */
    protected function getPackageName() : string
    {
        dd($this->getClassDirectory());

        // Some primitive caching
        if ($this->packageName) {
            return $this->packageName;
        }

        // TODO
        return str_replace('/', '.', $this->getPackageName());
    }

    //    /**
    //     * The lowercase name of the package.
    //     *
    //     * @return string
    //     */
    //    protected function getPackageName() : string
    //    {
    //        if ($this->packageName) {
    //            return $this->packageName;
    //        }
    //
    //        $configuration = $this->getClassDirectory() . '/../../module.json';
    //
    //        if (! file_exists($configuration)) {
    //            throw ModuleException::unableToResolveModuleName();
    //        }
    //
    //        $name = file_get_contents($configuration);
    //        $name = json_decode($name);
    //        $name = object_get($name, 'alias');
    //
    //        if (is_null($name)) {
    //            throw ModuleException::unableToResolveModuleName();
    //        }
    //
    //        return $this->packageName = $name;
    //    }
}
