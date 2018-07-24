<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Services;

use Composer\Autoload\ClassLoader;
use Illuminate\Filesystem\Filesystem;
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
     * @var array
     */
    private $modules;

    /**
     * @var \Composer\Autoload\ClassLoader
     */
    private $autoloader;

    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param array $config
     */
    public function __construct(Filesystem $files, array $config)
    {
        $this->files = $files;
        $this->config = $config;
    }

    /**
     * Scan and load all modules.
     */
    public function load() : void
    {
        $paths = $this->config['paths'];

        $this->autoloader = $this->getAutoloader();

        $this->modules = $this->scan($paths);

        $this->modules = $this->filterNonModules($this->modules);

        foreach ($this->modules as $name => $path) {
            $this->autoload($name, $path);
            $this->register($name, $path);
        }
    }

    /**
     * @param array|null $paths
     *
     * @return array
     * @throws \SebastiaanLuca\Module\Exceptions\ModuleLoaderException
     */
    private function scan(?array $paths) : array
    {
        if ($paths === null || empty($paths)) {
            return [];
        }

        $modules = [];

        foreach ($paths as $path) {
            $directories = $this->files->directories($path);

            foreach ($directories as $directory) {
                $name = studly_case(basename($directory));

                if (array_key_exists($name, $modules)) {
                    throw ModuleLoaderException::duplicate($name);
                }

                $modules[$name] = $directory;
            }
        }

        return $modules;
    }

    /**
     * @param array $modules
     *
     * @return array
     */
    private function filterNonModules(array $modules) : array
    {
        return collect($modules)
            ->reject(function ($path, $name) {
                return $this->getServiceProvider($name, $path) === null;
            })
            ->toArray();
    }

    /**
     * @param string $name
     * @param string $path
     */
    private function autoload(string $name, string $path) : void
    {
        $this->getAutoloader()->addPsr4(
            $name . '\\',
            $path . '/'
        );

        $this->getAutoloader()->addPsr4(
            $name . '\\Tests\\',
            $path . '/tests/'
        );
    }

    /**
     * @param string $name
     * @param string $path
     */
    private function register(string $name, string $path) : void
    {
        $provider = $this->getServiceProvider($name, $path);

        app()->register($provider);
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    private function getAutoloader() : ClassLoader
    {
        if ($this->autoloader) {
            return $this->autoloader;
        }

        return $this->autoloader = require base_path('vendor/autoload.php');
    }

    /**
     * @param string $module
     * @param string $directory
     *
     * @return string|null
     */
    private function getServiceProvider(string $module, string $directory) : ?string
    {
        $path = "{$directory}/src/Providers/{$module}ServiceProvider.php";

        if (! $this->files->exists($path)) {
            return null;
        }

        return $path;
    }
}
