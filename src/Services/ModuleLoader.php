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
        $path = $this->getCleanPath($path);

        $this->getAutoloader()->addPsr4(
            $name . '\\',
            $path . '/src',
            true
        );

        if (is_dir($testsPath = $path . 'tests/')) {
            $this->getAutoloader()->addPsr4(
                $name . '\\Tests\\',
                $path . '/tests',
                true
            );
        }

        if (is_dir($databasePath = $path . '/database')) {
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
     * @param string $name
     * @param string $directory
     *
     * @return string|null
     */
    private function getServiceProvider(string $name, string $directory) : ?string
    {
        $path = "{$directory}/src/Providers/{$name}ServiceProvider.php";

        if (! $this->files->exists($path)) {
            return null;
        }

        $path = $this->getCleanPath($path);

        $find = ['modules/' . $name . '/src', '/', '.php'];
        $replace = [$name, '\\', ''];

        return str_replace($find, $replace, $path);
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
