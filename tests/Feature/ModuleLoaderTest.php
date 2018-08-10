<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use SebastiaanLuca\Module\Services\ModuleLoader;
use SebastiaanLuca\Module\Tests\TestCase;

class ModuleLoaderTest extends TestCase
{
    // TODO: exception on duplicate module

    /**
     * @test
     */
    public function it loads all modules() : void
    {
        app(ModuleLoader::class)->load();

        $expected = [
            'Another\\Providers\\AnotherServiceProvider' => true,
            'MyModule\\Providers\\MyModuleServiceProvider' => true,
        ];

        $this->assertArraySubset($expected, app()->getLoadedProviders());
    }

    /**
     * @test
     */
    public function it scans and returns all modules() : void
    {
        $modules = app(ModuleLoader::class)->scan();

        $expected = [
            'Another' => '/Users/Sebastiaan/Workspace/Projects/laravel-module-loader/tests/temp/modules/Another',
            'Missing' => '/Users/Sebastiaan/Workspace/Projects/laravel-module-loader/tests/temp/modules/Missing',
            'MyModule' => '/Users/Sebastiaan/Workspace/Projects/laravel-module-loader/tests/temp/modules/MyModule',
        ];

        $this->assertSameValues($expected, $modules);
    }

    /**
     * @test
     */
    public function it returns all providers() : void
    {
        $providers = app(ModuleLoader::class)->getProviders([
            'Another' => '/Users/Sebastiaan/Workspace/Projects/laravel-module-loader/tests/temp/modules/Another',
            'Missing' => '/Users/Sebastiaan/Workspace/Projects/laravel-module-loader/tests/temp/modules/Missing',
            'MyModule' => '/Users/Sebastiaan/Workspace/Projects/laravel-module-loader/tests/temp/modules/MyModule',
        ]);

        $expected = [
            0 => 'Another\\Providers\\AnotherServiceProvider',
            1 => 'MyModule\\Providers\\MyModuleServiceProvider',
        ];

        $this->assertSameValues($expected, $providers);
    }

    /**
     * @test
     */
    public function it returns the cache path() : void
    {
        $this->assertSame(
            base_path('bootstrap/cache/module-loader.php'),
            app(ModuleLoader::class)->getCachePath()
        );
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        config()->set('module-loader', require __DIR__ . '/../../config/module-loader.php');

        $this->app->singleton(ModuleLoader::class, function () {
            return new ModuleLoader(
                app(Filesystem::class),
                config('module-loader')
            );
        });
    }
}
