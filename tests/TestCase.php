<?php

namespace SebastiaanLuca\Module\Tests;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Symfony\Component\Process\Process;

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
        app()->setBasePath(__DIR__ . '/temp');
    }

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();

        app(Filesystem::class)->copyDirectory(__DIR__ . '/resources/setup', base_path());

        // Generate composer autoload config base on the temp composer.json
        // file after setting up our temporary app directory.
        $this->dumpautoload();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown() : void
    {
        parent::tearDown();

        app(Filesystem::class)->deleteDirectory(base_path());
    }

    protected function dumpautoload() : void
    {
        $process = new Process(sprintf(
            'cd %s && composer dumpautoload',
            base_path()
        ));

        $process->run();
    }
}
