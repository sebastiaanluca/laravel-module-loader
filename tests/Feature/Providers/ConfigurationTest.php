<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Providers;

use SebastiaanLuca\Module\Tests\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function it registers the configuration(): void
    {
        $this->getModuleLoader()->load();

        $this->assertSame('value', config('my-module.key'));
    }

    /**
     * @test
     */
    public function it does not register the configuration when already cached(): void
    {
        file_put_contents(
            $this->app->getCachedConfigPath(),
            '<?php return '.var_export([], true).';'.PHP_EOL
        );

        $this->getModuleLoader()->load();

        // We can only check if the module's config wasn't written as we never
        // load the Laravel framework in our tests (which loads the config from
        // cache).
        $this->assertNull(config('my-module.key'));
    }
}
