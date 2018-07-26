<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Commands;

use Illuminate\Console\Command;
use SebastiaanLuca\Module\Services\ModuleLoader;

class RegisterModuleAutoloading extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:autoload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan your project for modules and write their autoload config to composer.json';

    /**
     * @var \SebastiaanLuca\Module\Services\ModuleLoader
     */
    private $modules;

    /**
     * Create a new command instance.
     *
     * @param \SebastiaanLuca\Module\Services\ModuleLoader $modules
     */
    public function __construct(ModuleLoader $modules)
    {
        parent::__construct();

        $this->modules = $modules;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() : void
    {
        $modules = $this->modules->scan();

        $this->writeAutoloadConfig(
            $this->getAutoloadConfig($modules)
        );

        $this->info('Modules autoloading config written to composer.json!');
    }

    /**
     * @param array $modules
     *
     * @return array
     */
    private function getAutoloadConfig(array $modules) : array
    {
        $classmap = [];
        $psr4 = [];
        $psr4Dev = [];

        foreach ($modules as $name => $path) {
            $psrName = $name . '\\';
            $psrPath = substr(str_replace(base_path(), '', $path), 1) . '/';

            $psr4 = array_merge($psr4, [$psrName => $psrPath]);
            $psr4Dev = array_merge($psr4Dev, [$psrName . 'Tests\\' => $psrPath . 'tests/']);

            if (file_exists($factories = $psrPath . 'database/factories')) {
                $classmap[] = $factories;
            }

            if (file_exists($seeders = $psrPath . 'database/seeds')) {
                $classmap[] = $seeders;
            }
        }

        return compact(
            'classmap',
            'psr4',
            'psr4Dev'
        );
    }

    /**
     * @param array $autoloadConfig
     */
    private function writeAutoloadConfig(array $autoloadConfig) : void
    {
        [
            'classmap' => $classmap,
            'psr4' => $psr4,
            'psr4Dev' => $psr4Dev,
        ] = $autoloadConfig;

        $composerPath = base_path('composer.json');

        $config = json_decode(file_get_contents($composerPath), true, 512, JSON_OBJECT_AS_ARRAY | JSON_UNESCAPED_SLASHES);

        $this->mergeConfigValue($config, 'autoload.classmap', $classmap);
        $this->mergeConfigValue($config, 'autoload.psr-4', $psr4);
        $this->mergeConfigValue($config, 'autoload-dev.psr-4', $psr4Dev);

        file_put_contents($composerPath, json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * @param array $config
     * @param string $key
     * @param array $value
     */
    private function setConfigValue(array &$config, string $key, array $value) : void
    {
        array_set($config, $key, $value);
    }

    /**
     * @param array $config
     * @param string $key
     * @param array $value
     */
    private function mergeConfigValue(array &$config, string $key, array $value) : void
    {
        $this->setConfigValue(
            $config,
            $key,
            array_unique(array_merge(array_get($config, $key, []), $value))
        );
    }
}
