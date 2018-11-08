<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use SebastiaanLuca\Module\Providers\ModuleProvider;
use SebastiaanLuca\Module\Tests\MocksInstances;

class RouteTest extends TestCase
{
    use MocksInstances;

    /**
     * @test
     */
    public function it registers all routes in routers() : void
    {
        Route::spy();

        $this->app->register(RouteTestServiceProvider::class);

        Route::shouldHaveReceived('view', ['test', 'myview'])->once();
    }

    /**
     * @test
     */
    public function it uses the cached routes() : void
    {
        Route::spy();

        $this->app['files'] = Mockery::mock($this->app['files']);

        $this->app['files']->shouldReceive('exists')->andReturnTrue();

        $this->app->register(RouteTestServiceProvider::class);

        Route::shouldNotHaveReceived('view');
    }
}

class RouteTestServiceProvider extends ModuleProvider
{
    protected $routers = [
        RouteTestRouter::class,
    ];
}

class RouteTestRouter
{
    public function __construct()
    {
        Route::view('test', 'myview');
    }
}
