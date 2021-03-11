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
    public function it scans all modules and doesnt load any providers for modules that are not active(): void
    {
        config()->set('module-loader.directories', [
            'empty',
        ]);

        $this->getModuleLoader()->load();

        $loaded = app()->getLoadedProviders();

        static::assertArrayNotHasKey('Another\\Providers\\AnotherServiceProvider', $loaded);
        static::assertArrayNotHasKey('MyModule\\Providers\\MyModuleServiceProvider', $loaded);
    }

    /**
     * @test
     */
    public function it scans all modules and registers all providers(): void
    {
        $this->getModuleLoader()->load();

        $loaded = app()->getLoadedProviders();

        static::assertArrayHasKey('Another\\Providers\\AnotherServiceProvider', $loaded);
        static::assertArrayHasKey('MyModule\\Providers\\MyModuleServiceProvider', $loaded);
    }

    /**
     * @test
     */
    public function it scans and returns all modules(): void
    {
        $modules = $this->getModuleLoader()->scan();

        $expected = [
            'Another' => base_path('modules/Another'),
            'Missing' => base_path('modules/Missing'),
            'MyModule' => base_path('modules/MyModule'),
        ];

        static::assertSameValues($expected, $modules);
    }

    /**
     * @test
     */
    public function it throws an exception when theres a duplicate module(): void
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
    public function it returns all providers(): void
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

        static::assertSameValues($expected, $providers);
    }

    /**
     * @test
     */
    public function it returns the cache path(): void
    {
        static::assertSame(
            base_path('bootstrap/cache/module-loader.php'),
            $this->getModuleLoader()->getCachePath()
        );
    }
}
