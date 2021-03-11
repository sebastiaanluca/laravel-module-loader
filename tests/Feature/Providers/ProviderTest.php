<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Providers;

use Orchestra\Testbench\TestCase;
use SebastiaanLuca\Module\Tests\Feature\Concerns\ProviderTestProvider;
use SebastiaanLuca\Module\Tests\Feature\Concerns\ProviderTestServiceProvider;

class ProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it registers all additional providers(): void
    {
        $this->assertArrayNotHasKey(
            ProviderTestProvider::class,
            $this->app->getLoadedProviders(),
            'The additional provider was already registered.'
        );

        $this->app->register(ProviderTestServiceProvider::class);

        $this->assertArrayHasKey(
            ProviderTestProvider::class,
            $this->app->getLoadedProviders(),
            'The additional provider was not registered.'
        );
    }
}
