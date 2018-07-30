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
    protected $signature = 'modules:autoload {--K|keep : Keep existing module autoload entries}';

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

        $this->info(sprintf(
            'Wrote composer.json autoload configuration for %s modules',
            count($modules)
        ));
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

            $path = $this->getCleanPath($path);

            $psr4 = array_merge($psr4, [$psrName => $path . '/src/']);

            if (file_exists($tests = $path . '/tests/')) {
                $psr4Dev = array_merge($psr4Dev, [$psrName . 'Tests\\' => $tests]);
            }

            if (file_exists($factories = $path . '/database/factories')) {
                $classmap[] = $factories;
            }

            if (file_exists($seeders = $path . '/database/seeds')) {
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

        $config = json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        file_put_contents($composerPath, $config . PHP_EOL);
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
        $existing = array_get($config, $key, []);

        if (! $this->option('keep')) {
            $existing = collect($existing)
                ->reject(function ($directory, $name) {
                    return starts_with($directory, config('module-loader.paths'));
                })
                ->toArray();
        }

        $value = array_unique(array_merge($existing, $value));

        ksort($value, SORT_ASC | SORT_NATURAL);

        $app = array_pull($value, 'App\\');
        $tests = array_pull($value, 'Tests\\');

        if ($app !== null) {
            $value = array_prepend($value, $app, 'App\\');
        }

        if ($tests !== null) {
            $value = array_prepend($value, $tests, 'Tests\\');
        }

        $this->setConfigValue($config, $key, $value);
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
