<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Concerns;

use SebastiaanLuca\Module\Providers\ModuleProvider;

class ProviderTestServiceProvider extends ModuleProvider
{
    /**
     * The routers to be automatically mapped.
     *
     * @var array
     */
    protected $providers = [
        ProviderTestProvider::class,
    ];
}
