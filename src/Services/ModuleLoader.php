<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Services;

use Illuminate\Support\Collection;
use SebastiaanLuca\Module\Actions\ListProviders;
use SebastiaanLuca\Module\Actions\RegisterProviders;
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
     * @return void
     */
    public function load() : void
    {
        if ($this->useCache()) {
            return;
        }

        app(RegisterProviders::class)->execute(
            app(ListProviders::class)->execute(
                $this->getModules()
            )
        );
    }

    /**
     * @return array
     */
    public function getModules() : array
    {
        if ($this->modules === null) {
            $this->modules = app(ScanDirectories::class)->execute($this->getModuleDirectories());
        }

        return $this->modules;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getModuleDirectories() : Collection
    {
        return ModulesDirectoryFactory::createCollectionFromArray(
            $this->config['directories']
        );
    }

    /**
     * @return bool
     */
    private function useCache() : bool
    {
        if (! file_exists($cache = static::getCachePath())) {
            return false;
        }

        app(RegisterProviders::class)->execute(require $cache);

        return true;
    }
}
