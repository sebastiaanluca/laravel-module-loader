<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Commands;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use SebastiaanLuca\Module\Commands\CreateModule;
use SebastiaanLuca\Module\Tests\TestCase;

class CreateModuleCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it creates a module() : void
    {
        app(Kernel::class)->registerCommand(app(CreateModule::class));

        $this->artisan('modules:create', ['name' => 'MyModule']);

        $this->assertDirectoryExists(base_path('modules/MyModule/src'));
        $this->assertFileExists(base_path('modules/MyModule/src/Providers/MyModuleServiceProvider.php'));
    }

    /**
     * @test
     */
    public function it creates a module using the first config path by default() : void
    {
        app(Kernel::class)->registerCommand(app(CreateModule::class));

        config()->set('module-loader.paths', [
            'customDir',
            'modules',
            'other',
        ]);

        $this->artisan('modules:create', ['name' => 'MyModule']);

        $this->assertDirectoryExists(base_path('customDir/MyModule/src'));
        $this->assertFileExists(base_path('customDir/MyModule/src/Providers/MyModuleServiceProvider.php'));
    }

    /**
     * @test
     */
    public function it creates a module using the given path() : void
    {
        app(Kernel::class)->registerCommand(app(CreateModule::class));

        $this->artisan('modules:create', [
            'name' => 'MyModule',
            '--directory' => 'other',
        ]);

        $this->assertDirectoryExists(base_path('other/MyModule/src'));
        $this->assertFileExists(base_path('other/MyModule/src/Providers/MyModuleServiceProvider.php'));
    }

    /**
     * @test
     */
    public function it shows an error when creating a module without configured directories() : void
    {
        app(Kernel::class)->registerCommand(app(CreateModule::class));

        config()->set('module-loader.paths', []);

        $this->artisan('modules:create', ['name' => 'MyModule']);

        // TODO: get output
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
            \SebastiaanLuca\Module\ModuleServiceProvider::class,
        ];
    }
}
