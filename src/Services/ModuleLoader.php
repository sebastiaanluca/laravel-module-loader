<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Services;

use Illuminate\Support\Collection;
use SebastiaanLuca\Module\Actions\AutoloadModules;
use SebastiaanLuca\Module\Actions\ListProviders;
use SebastiaanLuca\Module\Actions\ScanDirectories;
use SebastiaanLuca\Module\Factories\ModulesDirectoryFactory;

class ModuleLoader
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $modules;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public static function getCachePath() : string
    {
        return base_path('bootstrap/cache/module-loader.php');
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
        // Runtime autoloading should be an option, even if
        // service providers were cached.
        if ($autoload) {
            app(AutoloadModules::class)->execute($this->getModules());
        }

        if ($this->useCache()) {
            return;
        }

        app(ListProviders::class)->execute($this->getModules());

        // TODO
        //        $this->registerProviders(
        //            $this->getProviders(
        //                $this->getModules()
        //            )
        //        );
    }

    /**
     * @return array
     */
    private function getModules() : array
    {
        if ($this->modules === null) {
            $this->modules = app(ScanDirectories::class)->execute($this->getModuleDirectories());
        }

        return $this->modules;
    }

    /**
     * @return bool
     */
    private function useCache() : bool
    {
        if (! file_exists($cache = static::getCachePath())) {
            return false;
        }

        $providers = require $cache;

        // TODO: move to reusable action
        foreach ($providers as $provider) {
            app()->register($provider);
        }

        return true;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getModuleDirectories() : Collection
    {
        return ModulesDirectoryFactory::createCollectionFromArray(
            $this->config['directories']
        );
    }
}
