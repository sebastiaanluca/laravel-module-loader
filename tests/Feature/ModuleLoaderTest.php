<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature;

use SebastiaanLuca\Module\Exceptions\ModuleLoaderException;
use SebastiaanLuca\Module\Tests\TestCase;

class ModuleLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function it loads all modules() : void
    {
        $this->getModuleLoader()->load();

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
        $modules = $this->getModuleLoader()->scan();

        $expected = [
            'Another' => base_path('modules/Another'),
            'Missing' => base_path('modules/Missing'),
            'MyModule' => base_path('modules/MyModule'),
        ];

        $this->assertSameValues($expected, $modules);
    }

    /**
     * @test
     */
    public function it throws an exception when theres a duplicate module() : void
    {
        config()->set('module-loader.directories', [
            'modules',
            'extra',
        ]);

        mkdir(base_path('extra/Another'), 0777, true);

        $this->expectException(ModuleLoaderException::class);
        $this->expectExceptionMessage('A module named "Another" already exists.');

        $this->getModuleLoader()->load();
    }

    /**
     * @test
     */
    public function it returns all providers() : void
    {
        $providers = $this->getModuleLoader()->getProviders([
            'Another' => base_path('modules/Another'),
            'Missing' => base_path('modules/Missing'),
            'MyModule' => base_path('modules/MyModule'),
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
            $this->getModuleLoader()->getCachePath()
        );
    }
}
