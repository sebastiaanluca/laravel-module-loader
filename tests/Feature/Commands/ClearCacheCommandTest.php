<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Commands;

use SebastiaanLuca\Module\Commands\ClearCache;
use SebastiaanLuca\Module\ModuleServiceProvider;
use SebastiaanLuca\Module\Tests\TestCase;

class ClearCacheCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it clears the cache() : void
    {
        $this->artisan(ClearCache::class);

        touch($cache = base_path('bootstrap/cache/module-loader.php'));

        $this->assertFileExists($cache);

        $this->artisan('modules:clear');

        $this->assertFileNotExists($cache);
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app) : array
    {
        return [
            ModuleServiceProvider::class,
        ];
    }
}
