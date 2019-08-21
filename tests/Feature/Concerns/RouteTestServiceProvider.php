<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Concerns;

use SebastiaanLuca\Module\Providers\ModuleProvider;

class RouteTestServiceProvider extends ModuleProvider
{
    /**
     * The routers to be automatically mapped.
     *
     * @var array
     */
    protected $routers = [
        RouteTestRouter::class,
    ];
}
