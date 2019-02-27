<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Services;

use Composer\Autoload\ClassLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use SebastiaanLuca\Module\Exceptions\ModuleLoaderException;

class ModuleLoader
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $files;

    /**
     * @var array
     */
    private $config;

    /**
     * @var \Composer\Autoload\ClassLoader
     */
    private $autoloader;

    /**
     * @var array
     */
    private $modules;

    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param array $config
     * @param \Composer\Autoload\ClassLoader $autoloader
     */
    public function __construct(Filesystem $files, array $config, ClassLoader $autoloader)
    {
        $this->files = $files;
        $this->config = $config;
        $this->autoloader = $autoloader;
    }

    /**
     * Scan and load all modules.
     *
     * @param bool $autoload
     *
     * @return void
     */
    public function load(bool $autoload = false) : void
    {
        // Runtime autoloading should be an option,
        // even if service providers were cached
        if ($autoload) {
            $this->modules = $this->scan();

            foreach ($this->modules as $name => $path) {
                $this->autoload($name, $path);
            }
        }

        if ($this->useCache()) {
            return;
        }

        if ($this->modules === null) {
            $this->modules = $this->scan();
        }

        $this->registerProviders(
            $this->getProviders($this->modules)
        );
    }

    /**
     * @return array
     * @throws \SebastiaanLuca\Module\Exceptions\ModuleLoaderException
     */
    public function scan() : array
    {
        $paths = $this->config['directories'];

        if ($paths === null || empty($paths)) {
            return [];
        }

        $modules = [];

        foreach ($paths as $path) {
            $path = base_path($path);

            if (! $this->files->exists($path)) {
                continue;
            }

            $directories = $this->files->directories($path);

            foreach ($directories as $directory) {
                $name = Str::studly(basename($directory));

                if (array_key_exists($name, $modules)) {
                    throw ModuleLoaderException::duplicate($name);
                }

                $modules[$name] = $directory;
            }
        }

        return $this->filterNonModules($modules);
    }

    /**
     * @return string
     */
    public function getCachePath() : string
    {
        return base_path('bootstrap/cache/module-loader.php');
    }

    /**
     * @param array $modules
     *
     * @return array
     */
    public function getProviders(array $modules) : array
    {
        $providers = [];

        foreach ($modules as $name => $path) {
            $provider = $this->getProvider($name, $path);

            if (! $provider) {
                continue;
            }

            $providers[] = $provider;
        }

        return $providers;
    }

    /**
     * @return bool
     */
    private function useCache() : bool
    {
        if (! file_exists($cache = $this->getCachePath())) {
            return false;
        }

        $providers = require $cache;

        foreach ($providers as $provider) {
            app()->register($provider);
        }

        return true;
    }

    /**
     * @param array $modules
     *
     * @return array
     */
    private function filterNonModules(array $modules) : array
    {
        return collect($modules)
            ->filter(function ($path, $name) {
                return is_dir($path . '/src');
            })
            ->toArray();
    }

    /**
     * @param string $name
     * @param string $path
     */
    private function autoload(string $name, string $path) : void
    {
        $psrPath = $this->getCleanPath($path);

        $this->autoloader->addPsr4(
            $name . '\\',
            $psrPath . '/src/',
            true
        );

        if (is_dir($testsPath = $path . '/tests') && app()->environment($this->config['development_environments'])) {
            $this->autoloader->addPsr4(
                $name . '\\Tests\\',
                $psrPath . '/tests/',
                true
            );
        }

        if (is_dir($path . '/database')) {
            $this->autoloadClassmap($path . '/database');
        }
    }

    /**
     * @param array $providers
     */
    private function registerProviders(array $providers) : void
    {
        foreach ($providers as $provider) {
            app()->register($provider);
        }
    }

    /**
     * @param string $path
     */
    private function autoloadClassmap(string $path) : void
    {
        $classmap = $this->files->directories($path);

        // Recursively load all non-namespaced classes
        foreach ($classmap as $directory) {
            $this->autoloader->add('', $this->getCleanPath($directory));
        }
    }

    /**
     * @param string $name
     * @param string $directory
     *
     * @return string|null
     */
    private function getProvider(string $name, string $directory) : ?string
    {
        $path = "{$directory}/src/Providers/{$name}ServiceProvider.php";

        if (! $this->files->exists($path)) {
            return null;
        }

        $path = $this->getCleanPath($path);

        $find = [$name . '/src', '/', '.php'];
        $replace = [$name, '\\', ''];

        $provider = str_replace($find, $replace, $path);

        // Support multiple module directories
        $directories = array_map(function (string $directory) {
            return $directory . '\\';
        }, $this->config['directories']);

        $provider = str_replace($directories, [], $provider);

        // Do not register providers that don't exist or
        // don't have their namespace loaded
        if (! class_exists($provider)) {
            return null;
        }

        return $provider;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getCleanPath(string $path) : string
    {
        return str_replace(base_path() . '/', '', $path);
    }
}
