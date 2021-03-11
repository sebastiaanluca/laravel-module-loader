<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Providers;

use Illuminate\Support\Facades\Route;
use Mockery;
use Orchestra\Testbench\TestCase;
use SebastiaanLuca\Module\Tests\Feature\Concerns\RouteTestServiceProvider;

class RouteTest extends TestCase
{
    /**
     * @test
     */
    public function it registers all routes in routers(): void
    {
        Route::spy();

        $this->app->register(RouteTestServiceProvider::class);

        Route::shouldHaveReceived('view', ['test', 'myview'])->once();
    }

    /**
     * @test
     */
    public function it uses the cached routes(): void
    {
        Route::spy();

        $this->app['files'] = Mockery::mock($this->app['files']);

        $this->app['files']->shouldReceive('exists')->andReturnTrue();

        $this->app->register(RouteTestServiceProvider::class);

        Route::shouldNotHaveReceived('view');
    }
}
