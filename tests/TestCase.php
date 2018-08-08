<?php

namespace SebastiaanLuca\Module\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Asserts that two variables are equal regardless of their order.
     *
     * @param mixed $expected
     * @param mixed $actual
     */
    public static function assertSameValues($expected, $actual) : void
    {
        static::assertEquals(
            $expected,
            $actual,
            '$canonicalize = true',
            0.0,
            10,
            true
        );
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app) : void
    {
        app()->setBasePath(__DIR__ . '/resources/app');
    }
}
