<?php

namespace SebastiaanLuca\Module\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use SebastiaanLuca\Module\ModuleServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app) : array
    {
        return [
            ModuleServiceProvider::class,
        ];
    }
}
