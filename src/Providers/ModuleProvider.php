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
    private $moduleDirectory;

    /**
     * @var string
     */
    private $classDirectory;

    /**
     * @var string
     */
    private $packageName;

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
        $name = $this->getLowercasePackageName();

        $configuration = "{$this->getModulePath()}/config/{$name}.php";

        if (! file_exists($configuration)) {
            return;
        }

        $this->mergeConfigFrom(
            $configuration,
            $name
        );
    }

    /**
     * Register directories containing Eloquent model factories if enabled in the current
     * environment.
     */
    protected function registerFactories() : void
    {
        $devEnvironments = config("{$this->getLowercasePackageName()}.development_environments");

        if (! app()->environment($devEnvironments)) {
            return;
        }

        $this->app->make(Factory::class)->load(
            $this->getModulePath() . '/database/factories'
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
            "* {$this->getPackageName()} (configuration)"
        );
    }

    /**
     * Prepare all module assets.
     */
    protected function bootResources() : void
    {
        $this->loadMigrationsFrom($this->getModulePath() . '/database/migrations');
        $this->loadTranslationsFrom($this->getModulePath() . '/resources/lang', $this->getLowercasePackageName());
        $this->loadJsonTranslationsFrom($this->getModulePath() . '/resources/lang');

        $views = $this->getModulePath() . '/resources/views';

        if (file_exists($views)) {
            $this->loadViewsFrom($views, $this->getLowercasePackageName());
        }
    }

    /**
     * @return string
     */
    protected function getModulePath() : string
    {
        // Some primitive caching
        if ($this->moduleDirectory) {
            return $this->moduleDirectory;
        }

        return $this->moduleDirectory = str_replace(
            '/src/Providers',
            '',
            $this->getClassDirectory()
        );
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
        // Some primitive caching
        if ($this->packageName) {
            return $this->packageName;
        }

        return $this->packageName = str_replace(
            'ServiceProvider',
            '',
            class_basename($this)
        );
    }

    /**
     * @return string
     */
    protected function getLowercasePackageName() : string
    {
        return mb_strtolower($this->getPackageName());
    }
}
