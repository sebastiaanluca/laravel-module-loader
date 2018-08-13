<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Commands;

use Illuminate\Contracts\Console\Kernel;
use SebastiaanLuca\Module\Commands\Cache;
use SebastiaanLuca\Module\Tests\TestCase;

class CacheCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it caches all providers() : void
    {
        app(Kernel::class)->registerCommand(app(Cache::class, [$this->getModuleLoader()]));

        $cache = base_path('bootstrap/cache/module-loader.php');

        $this->assertFileNotExists($cache);

        $this->artisan('modules:cache');

        $this->assertFileExists($cache);
    }
}
