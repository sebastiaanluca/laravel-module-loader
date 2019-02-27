<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;
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
     *
     * @return void
     */
    public function register() : void
    {
        if (! $this->app->configurationIsCached()) {
            $this->registerConfiguration();
        }

        $this->registerFactories();

        parent::register();
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() : void
    {
        parent::boot();

        $this->loadPublishableResources();
        $this->bootResources();
    }

    /**
     * Merge the default module configuration file with its published counterpart.
     *
     * @return void
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
     *
     * @return void
     */
    protected function registerFactories() : void
    {
        $devEnvironments = config('module-loader.development_environments');

        if (! app()->environment($devEnvironments)) {
            return;
        }

        $this->app->make(Factory::class)->load(
            $this->getModulePath() . '/database/factories'
        );
    }

    /**
     * Register all publishable module assets.
     *
     * @return void
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
     *
     * @return void
     */
    protected function bootResources() : void
    {
        $this->loadMigrationsFrom($this->getModulePath() . '/database/migrations');
        $this->loadTranslationsFrom($this->getModulePath() . '/resources/lang', $this->getLowercasePackageName());
        $this->loadJsonTranslationsFrom($this->getModulePath() . '/resources/lang');

        $views = $this->getModulePath() . '/resources/views';

        if (is_dir($views)) {
            $this->app['view']->addNamespace($this->getLowercasePackageName(), $views);
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
        return mb_strtolower(Str::slug(Str::snake($this->getPackageName())));
    }
}
