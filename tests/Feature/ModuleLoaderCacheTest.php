<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature;

use SebastiaanLuca\Module\Tests\TestCase;

class ModuleLoaderCacheTest extends TestCase
{
    /**
     * @test
     */
    public function it loads all cached modules(): void
    {
        $loader = $this->getModuleLoader();

        $cache = $loader->getCachePath();

        $copy = copy(__DIR__.'/../resources/cache.php', $cache);

        $this->assertTrue($copy);
        $this->assertFileExists($cache);

        $loader->load();

        $loaded = app()->getLoadedProviders();

        $this->assertArrayHasKey('MyModule\\Providers\\MyModuleServiceProvider', $loaded);
        $this->assertArrayNotHasKey('Another\\Providers\\AnotherServiceProvider', $loaded);
    }
}
