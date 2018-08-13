<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Commands;

use Illuminate\Contracts\Console\Kernel;
use SebastiaanLuca\Module\Commands\ClearCache;
use SebastiaanLuca\Module\Tests\TestCase;

class ClearCacheCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it clears the cache() : void
    {
        app(Kernel::class)->registerCommand(app(ClearCache::class, [$this->getModuleLoader()]));

        touch($cache = base_path('bootstrap/cache/module-loader.php'));

        $this->assertFileExists($cache);

        $this->artisan('modules:clear');

        $this->assertFileNotExists($cache);
    }
}
