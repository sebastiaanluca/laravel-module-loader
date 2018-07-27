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
     *
     * @param bool $autoload
     *
     * @return void
     */
    public function load(bool $autoload = false) : void
    {
        $this->modules = $this->scan();

        if ($autoload) {
            foreach ($this->modules as $name => $path) {
                $this->autoload($name, $path);
            }
        }

        foreach ($this->modules as $name => $path) {
            $this->registerProvider($name, $path);
        }
    }

    /**
     * @return array
     * @throws \SebastiaanLuca\Module\Exceptions\ModuleLoaderException
     */
    public function scan() : array
    {
        $paths = $this->config['paths'];

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

        return $this->filterNonModules($modules);
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
                return file_exists($path . '/src');
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
            $path . '/src',
            true
        );

        if (file_exists($testsPath = $path . 'tests/')) {
            $this->getAutoloader()->addPsr4(
                $name . '\\Tests\\',
                $path . '/tests',
                true
            );
        }

        if (file_exists($databasePath = $path . '/database')) {
            $this->autoloadClassmap($databasePath);
        }
    }

    /**
     * @param string $name
     * @param string $path
     */
    private function registerProvider(string $name, string $path) : void
    {
        $provider = $this->getServiceProvider($name, $path);

        if ($provider === null) {
            return;
        }

        $find = [$path . '/src', '/', '.php'];
        $replace = [$name, '\\', ''];

        $provider = str_replace($find, $replace, $provider);

        // Do not register providers that don't exist or
        // don't have their namespace loaded
        if (! class_exists($provider)) {
            return;
        }

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
     * @param string $path
     */
    private function autoloadClassmap(string $path) : void
    {
        $classmap = $this->files->directories($path);

        $this->getAutoloader()->add('', $classmap);

        // Recursively load all non-namespaced classes
        foreach ($classmap as $directory) {
            $this->autoloadClassmap($directory);
        }
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
