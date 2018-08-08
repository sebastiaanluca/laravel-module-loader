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

    // TODO: test you can specify the path yourself (update config first with multiple paths)
    // TODO: test it errors when the path is not found in the config

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
