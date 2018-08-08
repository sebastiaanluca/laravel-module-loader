<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Commands;

use Illuminate\Filesystem\Filesystem;
use SebastiaanLuca\Module\ModuleServiceProvider;
use SebastiaanLuca\Module\Tests\TestCase;

class CacheCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it caches all providers() : void
    {
        $cache = base_path('bootstrap/cache/module-loader.php');

        $this->assertFileNotExists($cache);

        $this->artisan('modules:cache');

        $this->assertFileExists($cache);
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

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        app(Filesystem::class)->copyDirectory(__DIR__ . '/../../resources/modules/', base_path('modules/'));
    }
}
