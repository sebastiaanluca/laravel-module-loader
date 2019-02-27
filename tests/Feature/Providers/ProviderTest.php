<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Providers;

use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase;
use SebastiaanLuca\Module\Providers\ModuleProvider;

class ProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it registers all additional providers() : void
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

class ProviderTestServiceProvider extends ModuleProvider
{
    protected $providers = [
        ProviderTestProvider::class,
    ];
}

class ProviderTestProvider extends ServiceProvider
{
}
