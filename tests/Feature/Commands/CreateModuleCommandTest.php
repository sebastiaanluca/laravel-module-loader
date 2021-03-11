<?php

declare(strict_types=1);

namespace SebastiaanLuca\Module\Tests\Feature\Commands;

use Illuminate\Contracts\Console\Kernel;
use Mockery;
use Mockery\MockInterface;
use SebastiaanLuca\Module\Commands\CreateModule;
use SebastiaanLuca\Module\Tests\TestCase;

class CreateModuleCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it creates a module(): void
    {
        $this->mockCommand();

        $this->artisan('modules:create', ['name' => 'NewModule']);

        static::assertDirectoryExists(base_path('modules/NewModule/src'));
        static::assertFileExists(base_path('modules/NewModule/src/Providers/NewModuleServiceProvider.php'));
    }

    /**
     * @test
     */
    public function it creates a module using the first config path by default(): void
    {
        $this->mockCommand();

        config()->set('module-loader.directories', [
            'customDir',
            'modules',
            'other',
        ]);

        $this->artisan('modules:create', ['name' => 'NewModule']);

        static::assertDirectoryExists(base_path('customDir/NewModule/src'));
        static::assertFileExists(base_path('customDir/NewModule/src/Providers/NewModuleServiceProvider.php'));
    }

    /**
     * @test
     */
    public function it creates a module using the given path(): void
    {
        $this->mockCommand();

        $this->artisan('modules:create', [
            'name' => 'NewModule',
            '--directory' => 'other',
        ]);

        static::assertDirectoryExists(base_path('other/NewModule/src'));
        static::assertFileExists(base_path('other/NewModule/src/Providers/NewModuleServiceProvider.php'));
    }

    /**
     * @test
     */
    public function it shows an error when creating a module without configured directories(): void
    {
        $command = Mockery::mock(CreateModule::class.'[error]');

        $command->shouldReceive('error')->once()->with('No module directories configured nor any explicitly given!');

        app(Kernel::class)->registerCommand($command);

        config()->set('module-loader.directories', []);

        $this->artisan('modules:create', ['name' => 'NewModule']);
    }

    /**
     * @test
     */
    public function it shows an error when creating a module that already exists(): void
    {
        $command = Mockery::mock(CreateModule::class.'[error]');

        $command->shouldReceive('error')->once()->with('MyModule module already exists!');

        app(Kernel::class)->registerCommand($command);

        $this->artisan('modules:create', ['name' => 'MyModule']);
    }

    /**
     * @return \Mockery\MockInterface
     */
    private function mockCommand(): MockInterface
    {
        $command = Mockery::mock(CreateModule::class.'[call]');

        $command->shouldReceive('call')->once()->with('modules:refresh', ['--keep' => false]);

        app(Kernel::class)->registerCommand($command);

        return $command;
    }
}
