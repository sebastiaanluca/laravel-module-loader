<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests;

use Composer\Autoload\ClassLoader;
use Illuminate\Filesystem\Filesystem;
use Mockery;
use Orchestra\Testbench\TestCase as BaseTestCase;
use SebastiaanLuca\Module\Services\ModuleLoader;
use Symfony\Component\Process\Process;

class TestCase extends BaseTestCase
{
    /**
     * @var \Composer\Autoload\ClassLoader|\Mockery\MockInterface
     */
    private $autoloader;

    /**
     * Asserts that two variables are equal regardless of their order.
     *
     * @param mixed $expected
     * @param mixed $actual
     */
    public static function assertSameValues($expected, $actual) : void
    {
        static::assertEqualsCanonicalizing(
            $expected,
            $actual
        );
    }

    /**
     * @return \Composer\Autoload\ClassLoader|\Mockery\MockInterface
     */
    protected function getAutoloader()
    {
        return $this->autoloader;
    }

    /**
     * @return \SebastiaanLuca\Module\Services\ModuleLoader
     */
    protected function getModuleLoader() : ModuleLoader
    {
        $this->app->singleton(ModuleLoader::class, function () {
            return new ModuleLoader(
                app(Filesystem::class),
                config('module-loader'),
                $this->autoloader
            );
        });

        return app(ModuleLoader::class);
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

        $this->autoloader = Mockery::mock(ClassLoader::class);

        config()->set('module-loader', include __DIR__ . '/../config/module-loader.php');
        config()->set('module-loader.development_environments', ['different_environment']);
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
        $process = new Process([
            'cd '. base_path(),
            'composer dumpautoload'
        ]);

        $process->run();
    }
}
